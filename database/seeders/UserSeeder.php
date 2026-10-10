<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@alboptic.ma'],
            [
                'name' => 'Directeur ALBOPTIC',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'optician@alboptic.ma'],
            [
                'name' => 'Opticien Optométriste',
                'password' => Hash::make('password'),
                'role' => UserRole::Optician,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@alboptic.ma'],
            [
                'name' => 'Assistant Magasin',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'is_active' => true,
            ]
        );
    }
}
