<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Permission extends Model
{


    protected $fillable = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permission','permission_id','role_id');
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
