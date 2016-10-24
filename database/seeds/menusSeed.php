<?php

use Illuminate\Database\Seeder;

class menusSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dragable_menu')->truncate();
        $menus=['Fejléc linkek','Bal menü linkek','Láb linkek','4.menüpont','5.menüpont','6.menüpont','7.menüpont','8.menüpont','9.menüpont'];

        for ($i=0 ;$i<count($menus) ;$i++)
        {

            DB::table('dragable_menu')->insert([

                'parent_id' => 0,
                'top'=>0,
                'name'=>$menus[$i]



            ]);

        }
    }
}
