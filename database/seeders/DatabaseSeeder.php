<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Ensure at least one user exists
        User::factory()->create([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('pass123.'),
        ]);

        // Create 10 fake users
        User::factory(10)->create();

        // Create 100 notes
        Note::factory(100)->create();

        // Seed movies
        $this->call(MovieSeeder::class);
    }
}
