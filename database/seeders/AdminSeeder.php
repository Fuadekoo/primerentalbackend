<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'natnaelyohannes1212@gmail.com'], // Unique field to check
            [
                'name' => 'Natnael yohanes',
                'email' => 'natnaelyohannes1212@gmail.com',
                'isAdmin'=> true,
                'password' => Hash::make('1234512345'), // Set your default password
                'role' => 'admin', // Add role or any other field if needed
            ]
        );

    }
}
