<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('app.admin_email')],
            [
                'first_name' => 'Admin',
                'last_name' => 'Witcher',
                'password' => config('app.admin_password'),
                'role' => 'admin',
            ]
        );
    }
}
