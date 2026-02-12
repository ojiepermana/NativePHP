<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class RegisterUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:register {--name=} {--email=} {--role=user} {--id=} {--database= : Database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register atau update user dalam sistem';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Set database connection if specified
        $database = $this->option('database');
        if ($database) {
            config(['database.default' => $database]);
            info("Using database connection: {$database}");
        }

        // Check if user ID is provided for update
        $userId = $this->option('id');
        $existingUser = null;
        $isUpdate = false;

        if ($userId) {
            // Validate UUID format
            if (! preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $userId)) {
                error('Format ID tidak valid (harus UUID).');

                return self::FAILURE;
            }

            $existingUser = User::find($userId);
            if ($existingUser) {
                $isUpdate = true;
                info("User dengan ID {$userId} ditemukan. Mode: UPDATE");
            } else {
                info("User dengan ID {$userId} tidak ditemukan. Mode: CREATE dengan ID spesifik");
            }
        }

        $name =
          $this->option('name') ?? text(
              label: 'Nama user',
              placeholder: 'John Doe',
              required: true,
              default: $existingUser?->name
          );

        $email =
          $this->option('email') ??
          text(
              label: 'Email user',
              placeholder: 'user@example.com',
              required: true,
              default: $existingUser?->email,
              validate: fn (string $value) => match (true) {
                  ! filter_var($value, FILTER_VALIDATE_EMAIL) => 'Email tidak valid.',
                  User::where('email', $value)->where('id', '!=', $userId)->exists() => 'Email sudah terdaftar oleh user lain.',
                  default => null,
              }
          );

        $role =
          $this->option('role') ??
          select(
              label: 'Role user',
              options: ['user' => 'User', 'admin' => 'Admin'],
              default: $existingUser?->role ?? 'user'
          );

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'role' => $role],
            [
                'name' => 'required|string|max:255',
                'email' => ['required', 'email'],
                'role' => 'required|in:user,admin',
            ]
        );

        if ($validator->fails()) {
            error('Validasi gagal!');
            foreach ($validator->errors()->all() as $errorMsg) {
                warning($errorMsg);
            }

            return self::FAILURE;
        }

        if ($isUpdate) {
            // Update existing user
            $existingUser->update([
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ]);

            $user = $existingUser->fresh();
            info('✓ User berhasil diupdate!');
        } else {
            // Create new user
            $userData = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'email_verified_at' => now(),
            ];

            // If user ID is provided, use it
            if ($userId) {
                $userData['id'] = $userId;
            }

            $user = User::create($userData);
            info('✓ User berhasil didaftarkan!');
        }

        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'Terdaftar'],
            [
                [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->created_at->format('d M Y H:i'),
                ],
            ]
        );

        return self::SUCCESS;
    }
}
