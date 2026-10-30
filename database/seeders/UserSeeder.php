<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $passwordHash = Hash::make('password123');
        $faker = fake();
        $users = [];

        for ($index = 0; $index < 5; $index++) {
            $users[] = [
                'nama_lengkap' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'no_telepon' => $faker->numerify('08##########'),
                'role' => 'mahasiswa',
                'password_hash' => $passwordHash,
                'created_at' => now(),
            ];
        }

        DB::table('users')->insert($users);
    }
}
