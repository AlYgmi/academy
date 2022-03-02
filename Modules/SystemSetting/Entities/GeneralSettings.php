<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Localization\Entities\Language;
use Modules\Setting\Model\GeneralSetting;
use Rennokki\QueryCache\Traits\QueryCacheable;

class GeneralSettings extends Model
{


    protected $fillable = [];
    protected $table = 'general_settings';

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault();
    }

    public function timezone()
    {
        return $this->belongsTo(TimeZone::class, 'timezone_id')->withDefault();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            $path = Storage::path('settings.json');
            file_put_contents($path, GeneralSetting::first());
        });
        self::updated(function ($model) {
            $path = Storage::path('settings.json');
            file_put_contents($path, GeneralSetting::first());
        });
    }


}
