<?php

namespace Modules\Localization\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Language extends Model
{


    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('LanguageList');
        });
        self::updated(function ($model) {
            Cache::forget('LanguageList');
        });
    }
}
