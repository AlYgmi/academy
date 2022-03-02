<?php

namespace Modules\FrontendManage\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HeaderMenu extends Model
{
    protected $guarded = ['id'];


    public function childs(){
        return $this->hasMany(HeaderMenu::class,'parent_id','id')->orderBy('position');
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('menus');
        });
        self::updated(function ($model) {
            Cache::forget('menus');
        });
    }
}
