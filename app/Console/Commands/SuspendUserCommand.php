<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\search;

class SuspendUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:suspend {email?} {--unsuspend}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspend atau unsuspend user dari sistem';

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
                  ? User::where('email', 'like', "%{$value}%")
                      ->orWhere('name', 'like', "%{$value}%")
                      ->limit(10)
                      ->get()
                      ->mapWithKeys(
                          fn ($user) => [
                              $user->email => sprintf(
                                  '%s (%s) - %s',
                                  $user->name,
                                  $user->email,
                                  $user->suspended_at ? '🔒 Suspended' : '✓ Active'
                              ),
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

        $isUnsuspend = $this->option('unsuspend');
        $isSuspended = ! is_null($user->suspended_at);

        if ($isUnsuspend) {
            if (! $isSuspended) {
                error("User {$user->name} ({$user->email}) tidak dalam status suspended.");

                return self::FAILURE;
            }

            $confirmed = confirm(
                label: "Apakah Anda yakin ingin mengaktifkan kembali user {$user->name} ({$user->email})?",
                default: false
            );

            if (! $confirmed) {
                info('Operasi dibatalkan.');

                return self::SUCCESS;
            }

            $user->update(['suspended_at' => null]);
            info("✓ User {$user->name} ({$user->email}) berhasil diaktifkan kembali!");
        } else {
            if ($isSuspended) {
                error("User {$user->name} ({$user->email}) sudah dalam status suspended.");

                return self::FAILURE;
            }

            $confirmed = confirm(
                label: "Apakah Anda yakin ingin suspend user {$user->name} ({$user->email})?",
                default: false
            );

            if (! $confirmed) {
                info('Operasi dibatalkan.');

                return self::SUCCESS;
            }

            $user->update(['suspended_at' => now()]);
            info("✓ User {$user->name} ({$user->email}) berhasil di-suspend!");
        }

        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'Status'],
            [
                [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->suspended_at
                      ? "🔒 Suspended ({$user->suspended_at->format('d M Y H:i')})"
                      : '✓ Active',
                ],
            ]
        );

        return self::SUCCESS;
    }
}
