<?php

namespace Modules\FrontendManage\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class FrontPage extends Model
{


    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('FrontPageList');
        });
        self::updated(function ($model) {
            Cache::forget('FrontPageList');
        });
    }
}
