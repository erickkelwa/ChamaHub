<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::updateOrCreate(
            ['role' => 'admin'],  // find existing admin
            [
                'name'     => 'System Admin',
                'email'    => 'erickkelwa9@gmail.com',
                'phone'    => '0700000000',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );
    }
}
