<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Message::create([
            'author' => 'Nho Net',
            'content' => 'Hello everyone! Welcome to my website running on Docker, Laravel Backend, and MariaDB Database.',
        ]);

        Message::create([
            'author' => 'Docker Assistant',
            'content' => 'Microservices architecture is up and running. 3 containers: Frontend (Nginx), Backend (Laravel), Database (MariaDB).',
        ]);
    }
}
