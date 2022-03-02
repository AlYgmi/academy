<?php

namespace Modules\Newsletter\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NewsletterSetting extends Model
{
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model)  {
            Cache::forget('newsletterSetting');
        });
        self::updated(function ($model) {
            Cache::forget('newsletterSetting');
        });
    }
}
