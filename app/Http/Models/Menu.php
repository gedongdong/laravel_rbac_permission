<?php
/**
 * User: gedongdong@
 * Date: 2019/5/5 下午7:58
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany('App\Http\Models\Roles', 'menu_roles');
    }

    public static function menuTree(&$all_meuns, &$tree)
    {
        foreach($all_meuns as $key=>$menu){
            if(isset($all_meuns[$menu['pid']])){
                $all_meuns[$menu['pid']]['children'][] = &$all_meuns[$key];
            }else{
                $tree[] = &$all_meuns[$menu['id']];
            }
        }
    }
}