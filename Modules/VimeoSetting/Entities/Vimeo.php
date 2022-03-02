<?php

namespace Modules\VimeoSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Vimeo extends Model
{


    protected $fillable = [];


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('vimeoSetting');
        });
        self::updated(function ($model) {
            Cache::forget('vimeoSetting');
        });
    }
}
