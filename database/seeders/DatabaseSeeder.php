<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Clean Stack',
            'is_admin' => true,
            'email' => 'info@cleanstack.ir',
        ]);

        $this->call([
            SitePageSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            PostSeeder::class,
        ]);

        Lead::factory()
            ->count(10)
            ->create();
    }
}
