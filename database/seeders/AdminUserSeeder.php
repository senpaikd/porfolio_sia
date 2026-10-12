<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($name) || blank($email) || blank($password)) {
            $this->command?->warn('Admin user not seeded: set ADMIN_NAME, ADMIN_EMAIL, and ADMIN_PASSWORD.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => mb_strtolower(trim($email))],
            [
                'name' => trim($name),
                'password' => $password,
                'is_admin' => true,
            ],
        );
    }
}
