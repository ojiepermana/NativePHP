<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\NavigationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = new User([
            'name' => 'Ojiepermana',
            'email' => 'me@ojiepermana.com',
            'email_verified_at' => now(),
        ]);
        $user->id = 'd885a096-4a64-11ea-88e5-42010a940005';
        $user->save();

        $this->command->info('Database seeded successfully!');
        $this->command->line('');
        $this->command->line('User created:');
        $this->command->line("  ID: {$user->id}");
        $this->command->line("  Name: {$user->name}");
        $this->command->line("  Email: {$user->email}");
        $this->command->line('');
        $this->command->comment('To login in development, generate a magic link:');
        $this->command->line("  php artisan auth:magic-link {$user->email}");
        $this->command->line('');

        $this->call(NavigationSeeder::class);
    }
}
