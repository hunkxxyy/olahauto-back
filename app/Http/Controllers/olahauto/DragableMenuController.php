<?php

namespace App\Http\Controllers\olahauto;


use App\DynamicHtml;
use App\models\olahauto\Dragable_menu;
use App\utils\CommonFunction;
use Illuminate\Http\Request;
use App\Http\Requests\CreateDragableMenuRequest;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\utils\LogWrapper;

class DragableMenuController extends Controller
{


    private $logger;

    public function __construct()
    {
        $this->middleware('oauth',['except'=>['all']]);

        if (!$this->logger) $this->logger = new LogWrapper('DragableMenuController');
    }
    public function show($id)
    {
        $id=(int)$id;
        $Model=new ItemGroup();
        $ItemGroup = $Model->find($id);

        if (!$ItemGroup) {
            return $this->logger->response();
        }
        //$this->logger->info('show', 'log.succesRequest','SELECT * FROM '.$Model->getTable().'  where id=' . $id);
        return response()->json($ItemGroup);
    }
    public function listWithFilters($query)
    {

        $ItemGroup = new ItemGroup();
        $qb = new QueryBuilder();
        $qb->createQueryFields($query, $ItemGroup->getTable());

            $response = $qb->getResponse();

            return response()->json($response);


    }

    public function store(CreateDragableMenuRequest $request){
        $values = $request->all();
        // if (!$values['parent_id']) $values['parent_id']=0;
        
        $newItemGroup = Dragable_menu::create($values);

        return CommonFunction::response($newItemGroup,'Új menülelem rögzítve lett','200');
    }
    public function update(CreateDragableMenuRequest $request,$id){
       // file_put_contents('store_kep.log', print_r($request->all(), true));

        $id=(int)$id;
        $ItemGroup = Dragable_menu::find($id);
      DynamicHtml::where('route', $ItemGroup['route'])->update(['route' => $request['route']]);
        $ItemGroup->fill($request->all());
        $ItemGroup->push();

        return CommonFunction::response($ItemGroup,'Menülelem módosítva lett','200');

    }
    public function archive($id){
        $id=(int)$id;
        $dragable=new Dragable_menu();
        $forArchive=$dragable->find($id);
        $forArchive->archived = 1;

        $forArchive->save();
        return response()->json(['archived'=>$forArchive]);
    }
    public function all(){
        $ig=new Dragable_menu();
        $this->logger->info('listWithFilters', 'log.succesRequest', $ig->listAll());



        // file_put_contents('hunk2.log', print_r( $ig->listAll(), true));
       return $this->logger->response($ig->listAll());

    }
    public function move(Request $request){
        $group=new Dragable_menu();
        $group->move($request->all());
        return CommonFunction::response($request->all(),'Új menülelem poziciója változott','200');



    }
}
