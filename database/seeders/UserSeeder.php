<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@kkba.com',
            'name' => 'Administrator',
            'mobile' => '081111111111',
            'password' => Hash::make('Admin1234'),
            'valid_from' => '2023-08-22',
            'profile_photo_path' => 'avatar/blank-avatar.png'
        ]);
    }
}
