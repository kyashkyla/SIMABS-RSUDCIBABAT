<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'pegawai'],
            [
                'name' => 'Dr. Ahmad Fauzi',
                'email' => 'pegawai@rsudcibabat.com',
                'password' => Hash::make('pegawai123'),
                'role' => 'pegawai',
            ]
        );
    }
}