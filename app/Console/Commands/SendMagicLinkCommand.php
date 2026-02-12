<?php

namespace App\Console\Commands;

use App\Mail\MagicLinkMail;
use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;

class SendMagicLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:send-link {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim magic link login ke email user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        if (! $email) {
            $email = search(
                label: 'Cari user (ketik email atau nama)',
                options: fn (string $value) => strlen($value) > 0
                  ? User::whereNull('suspended_at')
                      ->where(function ($query) use ($value) {
                          $query->where('email', 'like', "%{$value}%")->orWhere('name', 'like', "%{$value}%");
                      })
                      ->limit(10)
                      ->get()
                      ->mapWithKeys(
                          fn ($user) => [
                              $user->email => sprintf('%s (%s)', $user->name, $user->email),
                          ]
                      )
                      ->all()
                  : [],
                placeholder: 'Ketik untuk mencari...'
            );
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            error("User dengan email {$email} tidak ditemukan.");

            return self::FAILURE;
        }

        if ($user->suspended_at) {
            error("User {$user->name} ({$user->email}) sedang dalam status suspended.");
            info('Gunakan: php artisan user:suspend --unsuspend '.$user->email);

            return self::FAILURE;
        }

        $confirmed = confirm(
            label: "Kirim magic link login ke {$user->name} ({$user->email})?",
            default: true
        );

        if (! $confirmed) {
            info('Operasi dibatalkan.');

            return self::SUCCESS;
        }

        info('Membuat token login...');
        $loginToken = LoginToken::createForUser($user);

        info('Mengirim email...');
        Mail::to($user->email)->send(new MagicLinkMail($loginToken));

        info("✓ Magic link berhasil dikirim ke {$user->email}!");
        $this->table(
            ['User', 'Email', 'Token Expire', 'Link'],
            [
                [
                    $user->name,
                    $user->email,
                    $loginToken->expires_at->format('d M Y H:i'),
                    route('auth.verify', ['token' => $loginToken->token]),
                ],
            ]
        );

        return self::SUCCESS;
    }
}
