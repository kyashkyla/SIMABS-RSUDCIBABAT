<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'Administrator',
            'email'=>'admin@rscibabat.com',
            'password'=>Hash::make('admin123'),
            'role'=>'admin'
        ]);
    }
}
