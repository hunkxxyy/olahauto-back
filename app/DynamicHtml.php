<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DynamicHtml extends Model
{
    protected $table='dynamic_htmls';
    protected $fillable=['content','title','route'];
    protected $hidden=['created_at','updated_at'];
}
