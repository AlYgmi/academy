<?php

namespace Modules\Setting\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Localization\Entities\Language;
use Modules\SystemSetting\Entities\Currency;
use Rennokki\QueryCache\Traits\QueryCacheable;

class GeneralSetting extends Model
{


    protected $hidden = ['id', 'created_at', 'updated_at'];


    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function date_format()
    {
        return $this->belongsTo(DateFormat::class, 'date_format_id')->withDefault();
    }

    public function timeZone()
    {
        return $this->belongsTo(TimeZone::class, 'time_zone_id')->withDefault();
    }


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
