<?php

namespace App\models\olahauto;

use Illuminate\Database\Eloquent\Model;

class Dragable_menu extends Model
{
    protected $table = 'dragable_menu';
    protected $fillable = ['name', 'top', 'description', 'parent_id', 'archived','route'];
    protected $hidden = ['updated_at', 'created_at', 'archived'];


    public function archive()
    {
        $this->archived = 'true';

        $this->save();
    }

    public function customFind($id)
    {

        $findObject = ItemGroup::find($id);
        //$findObject = $this->where('id',$id);
        if ($findObject)
            $findObject['itemGroup'] = $this->getUri($findObject);
        return $findObject;
    }

    private function getUri($model)
    {
        //print '----'.$id;
        $uri = $model->name;
        //$id=$model->id;

        while ($model->parent_id != 0) {

            $findObject = ItemGroup::find($model->id);
            $model = $this->getparent($findObject->parent_id);
            $uri = $model->name . '/' . $uri;

        }
        return $uri;
    }

    private function getparent($id)
    {
        $findObject = ItemGroup::find($id);
        return $findObject;
    }

    public function listAll()
    {
        $list = $this->orderBy('parent_id')
            ->where('archived', '=', 'false')

            ->get()
            ->toArray();
        //add chield class every item
        foreach ($list as &$group) {
            $group['chield'] = [];
            $group['menuType'] = 'haveSubMenu';
            $group['fontsize'] = (strlen($group['name'])>25)?'13px':'16px';
            $group['style'] = ($group['menuType'])? json_decode( $group['style']) :'';

        }
        //set the order
        //$list=$this->sortAbc($list);
        $list=$this->sortTop($list);


       $list= $this->createTree($list);
        return $list;


    }

    private function createTree($list)
    {

        $tree = [];
      //  return $list;

        while(count($list) )
        {
            $lisCount=count($list);
            foreach ($list as $key => $chield) {
                if ($chield['parent_id'] == 0) {
                    $tree[] = $chield;
                    unset($list[$key]);
                } else {
                    if ($this->checkRelations($tree, $chield))
                        unset($list[$key]);;
                }
            }
            if ($lisCount==count($list))
            {
                foreach ($list as $key => $chield) {
                    $list[$key]['parent_id']=0;
                }

            }

        }

       return $tree;

    }

    private function checkRelations(&$parent, $chield)
    {

        foreach ($parent as $key => &$p) {
            if (count($p['chield'])) {
                //file_put_contents('store_kep.log', 'CHIELD:' . $chield['id'] . "\n", FILE_APPEND);

                if ($this->checkRelations($p['chield'], $chield)) return true;;
                //    file_put_contents('store_kep.log', print_r($p, true), FILE_APPEND);
            }
            if ($chield['parent_id'] == $p['id']) {
                $p['chield'][] = $chield;
                return true;
            }
        }
        return false;
    }


    private function sortAbc($all)
    {
        usort($all, function ($a, $b) {
            if (strtolower($a['name']) == strtolower($b['name'])) return 0;
            else {
                return (strtolower($a['name']) < strtolower($b['name'])) ? 0 : 1;
            }
        });
        return $all;
    }

    private  function sortTop($all)
    {
        usort($all, function ($a, $b) {
            if ($a['top'] == $b['top']) return 0;
            else {
                return ($a['top'] <$b['top']) ? 0 : 1;
            }
        });
        return $all;
    }


    public    function move($objectForMove)
    {


        $obj = $this->where('id', '=', $objectForMove['id'])->first();
        $obj->update(['parent_id' => $objectForMove['parent']]);
        foreach ($objectForMove['tops'] as $objPos) {

            $this->where('id', '=', $objPos['id'])->update(['top' => $objPos['top']]);
        }


    }

}