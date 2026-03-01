<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $ceo = User::updateOrCreate(
            ['email' => 'bakkar.ceo@salifestyle.com'],
            [
                'name' => 'CEO',
                'password' => Hash::make('Bakkar1133##'),
                'is_admin' => true,
            ]
        );
        $ceo->assignRole('ceo');

        $cto = User::updateOrCreate(
            ['email' => 'ismam.cto@salifestyle.com'],
            [
                'name' => 'CTO',
                'password' => Hash::make('Ismam1133##'),
                'is_admin' => true,
            ]
        );
        $cto->assignRole('cto');
    }
}
