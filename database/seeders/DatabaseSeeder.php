<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
        ]);

        // ── Admin User ────────────────────────────
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@grocerease.com',
            'password' => Hash::make('Admin@1234'),
            'is_admin' => true,
        ]);

        // ── Test User ─────────────────────────────
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@grocerease.com',
            'password' => Hash::make('Test@1234'),
            'is_admin' => false,
        ]);
    }
}
