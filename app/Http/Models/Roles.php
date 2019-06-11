<?php
/**
 * User: gedongdong@
 * Date: 2019/5/5 下午7:58
 */

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany('App\Http\Models\Permission','roles_permission');
    }

}