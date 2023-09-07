<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'dev',
            'email' => 'dev@daysf.com',
            'password' => Hash::make('ikhsannwwi'),
            'status' => 1,
            'user_group_id' => 0,
            'remember_token' => Str::random(60),
        ]);
    }
}
