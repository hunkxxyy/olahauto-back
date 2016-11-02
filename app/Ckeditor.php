<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ckeditor extends Model
{
    protected $table='images';
    protected $fillable=['index','filename','file','pos'];
    /*ezek a képbeállítási globál paraméterek
    minden méretszám a magasságot jelzi*/
    public static $kepmeretek = [
        [
            'nev' => 'kicsi',
            'meret' => 100
        ],
        [
            'nev' => 'kozepes',
            'meret' => 154
        ],
        [
            'nev' => 'nagy',
            'meret' => 460
        ]
        ,
        [
            'nev' => 'kurvanagy',
            'meret' => 1460
        ]
    ];
    public function getImages($index){
        $response=$this->where('index',$index)->orderBy('pos', 'asc')->get();
        return $response;

    }

}
