<?php

namespace App\Http\Controllers;

use App\Ckeditor;

use App\utils\CommonFunction;
use Illuminate\Http\Request;
use App\Http\Requests\CreateCkeditorRequest;
use App\Http\Requests;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Input;

class CkeditorController extends Controller
{
    var $indexForImage='';
    public function listOfImages(Request $request){
        $ck=new Ckeditor();

        return response()->json($ck->getImages($request['index']));
    }
    public function store(CreateCkeditorRequest $request)
    {
        file_put_contents('store_kep.log', print_r($request->all(), true));


        $values = $request->all();
        $this->indexForImage=$values['INDEX'];
      //  file_put_contents('store_kep.log', print_r($values, true));

        $fileName = $this->setName($request);


        $currentPath = env('HOST_URL', 'forge');
        $tmpfilePath = 'images/'.$request['PATH'].'/original/';
        $filePath = 'images/'.$request['PATH'].'/';
        $params=json_decode($request['params'],true);
        if (Input::file('file')->isValid()) {
            $savePath=$tmpfilePath;

            Input::file('file')->move($savePath, $fileName);
          //  file_put_contents('store_kep.log',$savePath.$fileName, true);
            $dbFields=['file'=>$request['PATH'], 'filename'=>$fileName, 'index'=>$this->indexForImage];
           //kihagytam  az eredti adatbázis tárolását// Ckeditor::create($dbFields);
            if (isset($params))
            {
                 $this->createPaths($filePath,$params);
                $this->saveKepek($filePath,$params, $fileName);

            }
        }
        $ck=new Ckeditor();
        return  $ck->getImages($this->indexForImage);


    }
    private function saveKepek($path, $params,$file)
    {

        list($width, $height) = getimagesize($path . '/original/' . $file);
        foreach (  $params as $param) {
            $ratio=$width/$height;
            $newHeight=$param['width']/$ratio;
            $fileSavePath= $path . CommonFunction::hungarianToEnglishConvert($param['subdir']) . "/" . $file;
            $cmd = "convert  -limit thread 1 -colorspace RGB " . $path . "original/" . $file . " -resize " . $param['width'] . "x" .$newHeight . "! " . $fileSavePath;
         /*   file_put_contents('hunk2.log', $cmd);*/
            system($cmd);

        }
        $dbFields=['file'=>$fileSavePath, 'filename'=>$file, 'index'=>$this->indexForImage];
        Ckeditor::create($dbFields);


    }
    private function createPaths($mainDir,$params)
    {

        foreach ($params as $param) {
            $dir= $mainDir . '/' . CommonFunction::hungarianToEnglishConvert($param['subdir']);
            File::makeDirectory($dir, 0777,true,true);
            chmod($dir, 0777);

        }
        return  $mainDir;


    }
    private function setName($request)
    {
        // file_put_contents ( 'hunk2.log' ,print_r($_FILES,true) );

        $extension = \Illuminate\Support\Facades\File::extension($_FILES['file']['name']);
        $name = ($request['filename'] && $request['filename'] != 'undefined') ? CommonFunction::hungarianToEnglishConvert($request['filename']) . '.' . $extension : CommonFunction::hungarianToEnglishConvert($_FILES['file']['name']);


        return $name;

    }
    public function filedelete($id){
        Ckeditor::destroy($id);
        return response()->json(['msg'=>'sikeres törlés']);
    }
}
