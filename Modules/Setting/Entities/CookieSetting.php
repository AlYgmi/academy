<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CookieSetting extends Model
{
    protected $fillable = [];


    public static function boot()
    {
        parent::boot();


        self::created(function ($model) {
            Cache::forget('cookie');
        });


        self::updated(function ($model) {
            Cache::forget('cookie');
        });


    }
}
