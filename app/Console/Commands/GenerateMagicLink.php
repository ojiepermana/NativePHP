<?php

namespace App\Console\Commands;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateMagicLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:magic-link {email? : The email address of the user} {--native : Generate deep link URL for native app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a magic link for testing/development purposes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? 'test@example.com';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");
            $this->line('');
            $this->line('Available users:');
            User::all()->each(function ($u) {
                $this->line("  - {$u->email}");
            });

            return self::FAILURE;
        }

        // Create magic link token
        $loginToken = LoginToken::createForUser($user);

        // Generate the appropriate URL based on context
        if ($this->option('native')) {
            $deeplink = config('nativephp.deeplink_scheme');
            $magicLink = $deeplink.'://auth/verify?token='.$loginToken->token;
        } else {
            $magicLink = route('auth.verify', ['token' => $loginToken->token]);
        }

        $this->info('Magic link generated successfully!');
        $this->line('');
        $this->line('User: '.$user->name);
        $this->line('Email: '.$user->email);
        $this->line('');

        if ($this->option('native')) {
            $this->line('Deep Link (for Native App):');
            $this->line($magicLink);
            $this->line('');
            $this->comment('Copy the deep link above and paste it in a browser or email client.');
            $this->comment('The link will open the native app automatically.');
        } else {
            $this->line('Magic Link:');
            $this->line($magicLink);
            $this->line('');
            $this->comment('Copy the link above and paste it in your browser to login.');
        }

        return self::SUCCESS;
    }
}
