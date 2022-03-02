<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Role extends Model
{


    protected $guarded = [''];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permission','role_id','permission_id');
    }


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('PermissionList');
        });
        self::updated(function ($model) {
            Cache::forget('PermissionList');
        });
    }
}
