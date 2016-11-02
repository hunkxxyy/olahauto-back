<?php

namespace App\Http\Controllers;

use App\DynamicHtml;
use App\utils\LogWrapper;
use Illuminate\Http\Request;

use App\Http\Requests;

class DynamicHtmlControllers extends Controller
{
    private $logger;

    public function __construct()
    {
        $this->middleware('oauth',['except'=>['getAll','getContent']]);

        if (!$this->logger) $this->logger = new LogWrapper('DragableMenuController');
    }
    public function getAll()
    {

        $all = DynamicHtml::all();
        $r=[];
        foreach ($all as $part) {
            if ($part['contents']=='') $part['contents']="Nincsen beállított contents";
            $r[$part['index']] =['id'=>$part['id'],'content'=>$part['contents'],'title'=>$part['title']] ;
        }
        return response()->json($r);

    }
    public function getContent(Request $request)
    {
        $html=new DynamicHtml();

        $content=$html->where('route',$request['route'])->first();
        if (!$content) $content=['content'=>''];
        $this->logger->info('getContent', 'log.succesRequest');
        return $this->logger->response($content);

    }

    public function update(Request $request)
    {file_put_contents('store_kep.log', print_r($request->all(), true));
        $html=new DynamicHtml();

       $record= $html->where('route',$request['route'])->first();
        if (!$record){
            $record=  DynamicHtml::create($request->all());
        }else{
            $record->fill($request->all());
            $record->update();

        }
        return $record;


      /*





        $new->push();
        return response()->json($new);*/
    }
}
