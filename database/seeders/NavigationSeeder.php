<?php

namespace Database\Seeders;

use App\Models\Navigation;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Navigation::query()->delete();

        // Dashboard
        Navigation::create([
            'label' => 'Dashboard',
            'icon' => 'home',
            'url' => '/dashboard',
            'is_expandable' => false,
            'order' => 1,
        ]);
    }
}
