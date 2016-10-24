<?php

namespace App\Http\Controllers\olahauto;

use App\models\olahauto\Dragable_menu;
use App\utils\CommonFunction;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PullDatasFromOldDatabaseController extends Controller
{
    private $OLD_DB;
    private $NEW_DB;

    public function __construct()
    {

        $this->NEW_DB = DB::connection('mysql');
        $this->OLD_DB = DB::connection('old_mysql');

    }

    public function seed()
    {
        $this->NEW_DB->table('dynamic_htmls')->truncate();
        $this->NEW_DB->table('dragable_menu')->truncate();
        $this->createFixMenus();
        $this->createAdminMenus(1);
        $this->pullPages(3);
        $this->createTopMenus(2);
        $this->createFooter(4);
    }

    private function pullPages($parentId)
    {

        $parents = $this->OLD_DB->table('pages')->get();

        foreach ($parents as $p) {
            $parent = $this->checkIsNewParent($p->fomenu);
            $route=$p->fomenu.'/'.$p->almenu;
            $route='/'.CommonFunction::hungarianToEnglishConvert($route);
         //   $p->almenu=CommonFunction::hungarianToEnglishConvert($p->almenu);
          //  $p->fomenu=CommonFunction::hungarianToEnglishConvert($p->fomenu);
            if ($parent) {

                $obj = ['name' => $p->almenu, 'parent_id' => $parent->id, 'top' => $p->pos,'route'=>$route];
                $inserted=Dragable_menu::create($obj);

              //  $this->NEW_DB->table('dragable_menu')->insert($obj);
            } else {
                $obj = ['name' => $p->fomenu, 'top' => $p->pos, 'parent_id' => $parentId];
                //$this->NEW_DB->table('dragable_menu')->insert($obj);

                $inserted=Dragable_menu::create($obj);

                        //file_put_contents('store_kep.log',$inserted->id);
                if ($p->almenu) {
                    $parent = $this->checkIsNewParent($p->fomenu);
                    $obj = ['name' => $p->almenu, 'parent_id' => $parent->id, 'top' => $p->pos,'route'=>$route];
                    //$this->NEW_DB->table('dragable_menu')->insert($obj);
                    $inserted=Dragable_menu::create($obj);
                   // file_put_contents('store_kep.log',print_r($obj,true),FILE_APPEND);

                }

            }
            $this->pullContents($p->id,$inserted->id,$route);

        }
        return response()->json($parents);
    }

    private function createFixMenus()
    {
        $menus = [
            ['name' => 'ADMIN', 'parent_id' => '0', 'top' => '0'],
            ['name' => 'FELSŐ MENÜ', 'parent_id' => '0', 'top' => '1'],
            ['name' => 'BAL MENÜ', 'parent_id' => '0', 'top' => '2'],
            ['name' => 'LÁB MENÜ', 'parent_id' => '0', 'top' => '3']

        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }

    }

    private function createAdminMenus($parentId)
    {

        $menus = [
            ['name' => 'LINKEK / MENÜK BEÁLLÍTÁSA', 'parent_id' => $parentId, 'top' => '0', 'route' => 'admin/routes'],


        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }
    }

    private function createTopMenus($parentId)
    {

        $menus = [
            ['name' => 'KAPCSOLAT', 'parent_id' => $parentId, 'top' => '0','route'=>'kapcsolat'],
            ['name' => 'VÁSÁRLÓI INFORMÁCIÓK', 'parent_id' => $parentId, 'top' => '1','route'=>'/vasarloi-informacio'],
            ['name' => 'SZÁLLÍTÁSI INFORMÁCIÓK', 'parent_id' => $parentId, 'top' => '2','route'=>'/szallitasi-informaciok'],
            ['name' => 'CÉGINFORMÁCIÓ', 'parent_id' => $parentId, 'top' => '3','route'=>'/ceginformacio'],

        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }
    }

    private function createFooter($parentId)
    {

        $menus = [
            ['name' => 'Információk', 'parent_id' => $parentId, 'top' => '0'],
            ['name' => 'Áruház használata', 'parent_id' => $parentId, 'top' => '1'],
            ['name' => 'Kiemelt márkáink', 'parent_id' => $parentId, 'top' => '2'],
            ['name' => 'Közösségi média', 'parent_id' => $parentId, 'top' => '3']

        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }

    }

    private function checkIsNewParent($name)
    {
        $parent = $this->NEW_DB->table('dragable_menu')->where('name', $name)->first();
        return $parent;
    }

    private function pullContents($oldParent,$id,$route)
    {
        $content=$this->OLD_DB->table('nyito_blokk')->where('parent',$oldParent)->first();
        $contentObj= ['route' => $route, 'content' =>$content->szoveg];
        $this->NEW_DB->table('dynamic_htmls')->insert($contentObj);
        //file_put_contents('store_kep.log', $id."\n",FILE_APPEND);

    }
}
