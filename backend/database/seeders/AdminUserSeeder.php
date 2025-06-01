<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Forumpost;
use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'girts',
            'name' => 'girts',
            'email' => 'girts@gmail.com',
            'password' => bcrypt('girts'),
            'role_id' => 1,
        ]);
        User::create([
            'username' => 'janis',
            'name' => 'janis',
            'email' => 'janis@gmail.com',
            'password' => bcrypt('janis'),
            'role_id' => 2,
        ]);
        User::create([
            'username' => 'roberts',
            'name' => 'roberts',
            'email' => 'roberts@gmail.com',
            'password' => bcrypt('roberts'),
            'role_id' => 2,
        ]);
        Forumpost::create([
            'title' => 'Pirmais ieraksts',
            'content' => 'Šis ir pirmais ieraksts forumā.',
            'author_id' => 1,
            'date' => "2023-10-01 12:00:00",
        ]);
           Forumpost::create([
            'title' => 'Man patīk šī lapa!',
            'content' => 'Jums arī patīk viņa cik man patīk?',
            'author_id' => 3,
            'date' => "2025-01-01 15:00:00",
        ]);
        Comment::create([
            'content' => 'Paldies par šo ierakstu!',
            'author_id' => 2,
            'post_id' => 1,
        ]);
        Comment::create([
            'content' => 'Ko jūs te vāvuļojat?',
            'author_id' => 3,
            'post_id' => 1,
        ]);
  

    }
}
