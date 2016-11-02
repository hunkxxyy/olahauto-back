<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //DB::statement("TRUNCATE TABLE users ");
        $this->call('Oauth2ClientsSeed');
        $this->call('UsersSeed');
        // $this->call('menusSeed');

    }
}
