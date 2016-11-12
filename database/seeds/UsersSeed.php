<?php

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;
class UsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {




        User::create([

            'name' => 'hunk',
            'email' => 'hunk74@gmail.com',
            'privilegium' => 'ADMIN',
            'password' => \Illuminate\Support\Facades\Hash::make("editke76")

        ]);
        User::create([

            'name' => 'admin',
            'email' => 'info@olahauto.hu',
            'privilegium' => 'ADMIN',
            'password' => \Illuminate\Support\Facades\Hash::make("OlahAuto2016")

        ]);




    }
}
