<?php

namespace App\Http\Controllers;

use App\models\olahauto\Dragable_menu;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    var $word;
    var $result;

    public function search($word)
    {
        $this->word = $word;
        $this->searchInTitle();
        $this->searchInContent();
        if (!count($this->result))
            $this->result=[[
                'title'=>'',
                'content'=>'A '.$word.' kulcszóra nincsen találat!',
                'route'=>''
            ]];
        return $this->result;

    }

    private function searchInContent()
    {
        $result = DB::table('dynamic_htmls')->where('content', 'like', '%' . $this->word . '%')->get();
        foreach ($result as $r) {
            $content=$this->setContent($r->content);
            if ($content)
            $this->result[]=[
                'title'=>$this->getTitle($r->route),
                'content'=>$content,
                'route'=>$r->route,
            ];
        }

    }
    private function getTitle($route){
        $menu=Dragable_menu::where('route',$route)->first();
        return $menu['name'];
    }
    private function searchInTitle()
    {
//        DB::table('dynamic_htmls')->where('')
        $result = DB::table('dragable_menu')->where('name', 'like', '%' . $this->word . '%')->get();
        foreach ($result as $r) {
            $this->result[]=[
                'title'=>$r->name,
                'content'=>$r->name,
                'route'=>$r->route,
            ];
        }
    }
    private function setContent($content){
        $charlength=300;
        $text=strip_tags($content);
         $text=str_replace('&nbsp;','',$text);
        $text=trim($text);
        $parts=explode($this->word,$text);

       // file_put_contents('store_kep.log', print_r($parts,true));
        if (isset($parts[1]))
        {
           return '<span style="color: red;font-weight: bold">'.$this->word.'</span>'.substr($parts[1],0,$charlength).'...';
        }
        else return false;

    }
}
