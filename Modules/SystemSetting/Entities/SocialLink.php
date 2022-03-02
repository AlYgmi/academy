<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class SocialLink extends Model
{


    protected $fillable = [];


    public static function boot()
    {
        parent::boot();
        self::created(function ($model)  {
            Cache::forget('social_links');
        });
        self::updated(function ($model) {
            Cache::forget('social_links');
        });
    }
}
