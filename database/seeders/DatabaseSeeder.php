<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'CEO',
            'Project Manager',
            'Sistem Analis',
            'Programmer',
            'DevOps',
            'UI/UX',
            'Marketing',
            'QA'
        ];

        foreach ($roles as $role) {
            $email = strtolower(str_replace([' ', '/'], '', $role)) . '@example.com';
            User::factory()->create([
                'name' => $role,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
            ]);
        }
    }
}
