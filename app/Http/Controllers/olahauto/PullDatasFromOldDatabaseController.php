<?php

namespace App\Http\Controllers\olahauto;

use App\Ckeditor;
use App\models\olahauto\Dragable_menu;
use App\Termek;
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
//        $this->middleware('oauth',[]);


        $this->NEW_DB = DB::connection('mysql');
        $this->OLD_DB = DB::connection('old_mysql');

    }

    public function seed()
    {
        $this->NEW_DB->table('dynamic_htmls')->truncate();
        $this->NEW_DB->table('dragable_menu')->truncate();
        $this->createFixMenus();
        $this->createAdminMenus(1);
        $this->createTermekek(2);
        $this->pullPages(4);
        $this->createTopMenus(3);
        $this->createFooter(5);
        $this->pullTermekImages();

    }

    private function pullTermekImages()
    {
        Ckeditor::truncate();
        $termekek=Termek::all();
       foreach ($termekek as $t){
           $obj=['index'=>'termek-images-'.$t['id'],'filename'=>$t['kep'],'file'=>'images/termekek/kicsi/'.$t['kep'],'pos'=>0];
           Ckeditor::create($obj);
       }
    }

    private function pullPages($parentId)
    {

        $parents = $this->OLD_DB->table('pages')->get();

        foreach ($parents as $p) {
            $parent = $this->checkIsNewParent($p->fomenu);
            $route = $p->fomenu . '/' . $p->almenu;
            $route = '/' . CommonFunction::hungarianToEnglishConvert($route);
            $p->almenu = $this->setHungarianBadCharacters($p->almenu);
            $p->fomenu = $this->setHungarianBadCharacters($p->fomenu);
            if ($parent) {

                $obj = ['name' => $p->almenu, 'parent_id' => $parent->id, 'top' => $p->pos, 'route' => $route];
                $inserted = Dragable_menu::create($obj);

                //  $this->NEW_DB->table('dragable_menu')->insert($obj);
            } else {
                $route = $p->fomenu;
                $route = '/' . CommonFunction::hungarianToEnglishConvert($route);
                $obj = ['name' => $p->fomenu, 'top' => $p->pos, 'parent_id' => $parentId, 'route' => $route];
                //$this->NEW_DB->table('dragable_menu')->insert($obj);

                $inserted = Dragable_menu::create($obj);

                //file_put_contents('store_kep.log',$inserted->id);
                if ($p->almenu) {
                    $parent = $this->checkIsNewParent($p->fomenu);
                    $obj = ['name' => $p->almenu, 'parent_id' => $parent->id, 'top' => $p->pos, 'route' => $route];
                    //$this->NEW_DB->table('dragable_menu')->insert($obj);
                    $inserted = Dragable_menu::create($obj);
                    // file_put_contents('store_kep.log',print_r($obj,true),FILE_APPEND);

                }

            }
            $this->pullContents($p->id, $inserted->id, $route);
            if ($route == '/Nyito-lap') {
                $this->pullContents($p->id, $inserted->id, '');
                /*file_put_contents('pullContents.log', $route."\n",FILE_APPEND);*/
            }


        }
        return response()->json($parents);
    }

    private function setHungarianBadCharacters($word)
    {
        $hungarianABC = ['&#337;', '&#336;', '&#369;', '&#368;'];
        //Angol ékezetes betűk
        $englishABC = ['ő', 'Ő', 'ű', 'Ű'];


        return str_replace($hungarianABC, $englishABC, $word);
    }

    private function createFixMenus()
    {

        //$style=json_decode("{'background-color':'blue','color':'white'}");
        $style = ['background-color' => '#6495ed', 'color' => 'white'];
        $styleBoj = json_encode($style);
        $menus = [
            ['name' => 'ADMIN', 'parent_id' => '0', 'top' => '0', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => 1],
            ['name' => 'TERMÉKEK', 'parent_id' => '0', 'top' => '1', 'editable' => '0'],

            ['name' => 'FELSŐ MENÜ', 'parent_id' => '0', 'top' => '2', 'editable' => '0', 'admin_only' => 0],
            ['name' => 'BAL MENÜ', 'parent_id' => '0', 'top' => '3', 'editable' => '0', 'admin_only' => 0],
            ['name' => 'LÁB MENÜ', 'parent_id' => '0', 'top' => '4', 'editable' => '0', 'admin_only' => 0]

        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }

    }

    private function createAdminMenus($parentId)
    {
        $style = ['background-color' => '#2d6bd1', 'color' => 'white'];
        $styleBoj = json_encode($style);

        $menus = [
            ['name' => 'LINKEK / MENÜK BEÁLLÍTÁSA', 'parent_id' => $parentId, 'top' => '1', 'route' => 'admin/routes', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],
            ['name' => 'Főoldali banner', 'parent_id' => $parentId, 'top' => '2', 'route' => 'admin/banner', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],
            ['name' => 'Termékek', 'parent_id' => $parentId, 'top' => '3', 'route' => 'admin/termekek', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],
            ['name' => 'Kijelentkezés', 'parent_id' => $parentId, 'top' => '4', 'route' => 'auth/destroy', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],

        ];
        foreach ($menus as $m) {

            $newData = $this->NEW_DB->table('dragable_menu')->insert($m);
            if ($m['route'] == 'admin/termekek') {
                $this->createtermekekSubmenus();
            }
        }
        /*$subMenus = [
            ['name' => 'Termékek', 'parent_id' => $parentId, 'top' => '3', 'route' => 'admin/termekek', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],
            ['name' => 'Kijelentkezés', 'parent_id' => $parentId, 'top' => '4', 'route' => 'auth/destroy', 'style' => $styleBoj, 'editable' => '0', 'admin_only' => '1'],

        ];*/
    }

    private function createtermekekSubmenus()
    {
        $parent = Dragable_menu::where('route', '=', 'admin/termekek')->first();
        $parentId = $parent['id'];
        $menus = [
            ['name' => ' - Termékek listája', 'parent_id' => $parentId, 'top' => '0', 'route' => '/admin/termekek/list'],
            ['name' => ' - Új termék felvétele', 'parent_id' => $parentId, 'top' => '1', 'route' => '/admin/termekek/new'],


        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }
    }

    private function createTopMenus($parentId)
    {

        $menus = [
            ['name' => 'KAPCSOLAT', 'parent_id' => $parentId, 'top' => '0', 'route' => 'kapcsolat'],
            ['name' => 'VÁSÁRLÓI INFORMÁCIÓK', 'parent_id' => $parentId, 'top' => '1', 'route' => '/vasarloi-informacio'],
            ['name' => 'SZÁLLÍTÁSI INFORMÁCIÓK', 'parent_id' => $parentId, 'top' => '2', 'route' => '/szallitasi-informaciok'],
            ['name' => 'CÉGINFORMÁCIÓ', 'parent_id' => $parentId, 'top' => '3', 'route' => '/ceginformacio'],

        ];
        foreach ($menus as $m) {
            $this->NEW_DB->table('dragable_menu')->insert($m);
        }
    }

    private function createTermekek($parentId)
    {
        $termekek = new Termek();
        $parents = $termekek->getMenuForDragable();

        foreach ($parents as $p) {
            //file_put_contents('store_kep.log', $p['alcsoport'],FILE_APPEND);
            $p['alcsoport'] = $this->setHungarianBadCharacters($p['alcsoport']);
            $p['csoport'] = $this->setHungarianBadCharacters($p['csoport']);
            Termek::where('id', $p['id'])->update(['alcsoport' => $p['alcsoport'], 'csoport' => $p['csoport']]);
            $parent = $this->checkIsNewParent($p['csoport']);
            $mainRoute = 'termekek/';
            $route = $mainRoute . $p['csoport'];

            $route .= ($p['alcsoport'] != '') ? '/' . $p['alcsoport'] : '';

            $route = '/' . CommonFunction::hungarianToEnglishConvert($route);

            if ($parent) {

                $obj = ['related_id' => $p['id'], 'name' => ($p['alcsoport'] != '') ? $p['alcsoport'] : $p['termeknev'], 'parent_id' => $parent->id, 'top' => $p->id, 'route' => $route];
                $inserted = Dragable_menu::create($obj);

                //  $this->NEW_DB->table('dragable_menu')->insert($obj);
            } else {
                $route = $mainRoute . $p['csoport'];
                $route = '/' . CommonFunction::hungarianToEnglishConvert($route);
                $obj = ['related_id' => $p['id'], 'name' => $p['csoport'], 'top' => $p->id, 'parent_id' => $parentId, 'route' => $route];
                //$this->NEW_DB->table('dragable_menu')->insert($obj);

                $inserted = Dragable_menu::create($obj);

                //file_put_contents('store_kep.log',$inserted->id);
                if ($p['alcsoport']) {
                    $route = $mainRoute . $p['csoport'] . '/' . $p['alcsoport'];
                    $route = '/' . CommonFunction::hungarianToEnglishConvert($route);
                    $parent = $this->checkIsNewParent($p['csoport']);
                    $obj = ['related_id' => $p['id'], 'name' => $p['alcsoport'], 'parent_id' => $parent->id, 'top' => $p->id, 'route' => $route];
                    //$this->NEW_DB->table('dragable_menu')->insert($obj);
                    $inserted = Dragable_menu::create($obj);
                    // file_put_contents('store_kep.log',print_r($obj,true),FILE_APPEND);

                }

            }


        }
        return response()->json($parents);


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

        //   file_put_contents('store_kep.log', $name."\n",FILE_APPEND);
        $parent = $this->NEW_DB->table('dragable_menu')->where('name', $name)->first();
        // file_put_contents('store_kep.log', print_r($parent,true),FILE_APPEND);
        return $parent;
    }

    private function pullContents($oldParent, $id, $route)
    {
        $content = $this->OLD_DB->table('nyito_blokk')->where('parent', $oldParent)->first();
        $contentObj = ['route' => $route, 'content' => $content->szoveg];
        $this->NEW_DB->table('dynamic_htmls')->insert($contentObj);
        //file_put_contents('store_kep.log', $id."\n",FILE_APPEND);

    }
}
