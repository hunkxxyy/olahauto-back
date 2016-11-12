<?php

use Illuminate\Database\Seeder;

class Oauth2ClientsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients=[
           [
                'name'=>'olahauto.hu',
                'secret'=>'olahauto2016'
           ]

        ];

        $i=0;
        foreach ($clients as $c) {
            $i++;
            DB::table('oauth_clients')->insert([
                'id'=>$i,
                'secret'=>$c['secret'],
                'name'=>$c['name']
            ]);

        }
    }
}
