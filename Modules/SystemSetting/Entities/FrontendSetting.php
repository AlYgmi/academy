<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class  FrontendSetting extends Model
{


    protected $fillable = ['section', 'title', 'description', 'heading', 'default_title', 'default_description', 'btn_name', 'default_btn', 'url', 'icon'];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('SectionList');
        });
        self::updated(function ($model) {
            Cache::forget('SectionList');
        });
    }
}
