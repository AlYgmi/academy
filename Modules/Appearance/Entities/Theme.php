<?php

namespace Modules\Appearance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Model\GeneralSetting;

class Theme extends Model
{

    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        $path = Storage::path('settings.json');


        self::created(function ($model) use ($path) {

            file_put_contents($path, GeneralSetting::first());
            Cache::forget('frontend_active_theme');
        });


        self::updated(function ($model) use ($path) {
            file_put_contents($path, GeneralSetting::first());
            Cache::forget('frontend_active_theme');
        });


    }

}
