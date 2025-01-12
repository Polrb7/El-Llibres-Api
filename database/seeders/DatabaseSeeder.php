<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Pol',
            'surname' => 'Romeu',
            'username' => 'Polrb7',
            'age' => 20,
            'email' => 'pol.romeu@cirvianum.cat',
            'email_verified_at' => now(),
            'password' => 1234,
            'admin' => 1,
            'remember_token' => Str::random(10),
            'profile_img' => null,
        ]);
        User::factory()->create([
            'name' => 'Julios',
            'surname' => 'Leon',
            'username' => 'tekken',
            'age' => 20,
            'email' => 'julios.leon@cirvianum.cat',
            'email_verified_at' => now(),
            'password' => 1234,
            'admin' => 0,
            'remember_token' => Str::random(10),
            'profile_img' => null,
        ]);
        User::factory()->create([
            'name' => 'Sergi',
            'surname' => 'Urbano',
            'username' => 'sergiu',
            'age' => 20,
            'email' => 'sergi.urbano@cirvianum.cat',
            'email_verified_at' => now(),
            'password' => 1234,
            'admin' => 0,
            'remember_token' => Str::random(10),
            'profile_img' => null,
        ]);

        Book::factory(10)->create();
        Review::factory(7)->create();
        Comment::factory(5)->create();
        Like::factory(5)->create();
    }
}
