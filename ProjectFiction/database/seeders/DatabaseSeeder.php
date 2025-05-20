<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Story;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // $this->call([
        //     StoryTableSeeder::class,
        // ]);

        //List of genres to cycle through
        $genres = [
            'Action',
            'Essay',
            'Fiction',
            'Fantasy',
            'Mystery',
            'Science Fiction',
            'Horror',
            'Historical',
            'Humor',
            'Thriller',
            'Mythology',
            'romance',
            'Biography',
            'Supernatural'
        ];

        User::factory()
            ->count(10) // Create 10 users
            ->has(
                Story::factory()
                    ->count(rand(14, 36)) // Each user will have between 14/36 stories
                    ->sequence(fn(Sequence $sequence) => [ // Apply genre cycling to stories
                        'genre' => $genres[$sequence->index % count($genres)],
                    ])
            )
            ->create();



        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

    }


}
