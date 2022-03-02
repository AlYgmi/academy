<?php

namespace Modules\FrontendManage\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Sponsor extends Model
{
    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('SponsorList');
        });
        self::updated(function ($model) {
            Cache::forget('SponsorList');
        });
    }
}
