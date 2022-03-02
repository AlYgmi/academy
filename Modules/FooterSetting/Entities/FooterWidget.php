<?php

namespace Modules\FooterSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Modules\FrontendManage\Entities\FrontPage;

class FooterWidget extends Model
{


    protected $guarded = ['id'];

    public function frontpage()
    {
        return $this->belongsTo(FrontPage::class, 'page_id')->withDefault();
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model)  {
            Cache::forget('sectionWidgets');
        });
        self::updated(function ($model) {
            Cache::forget('sectionWidgets');
        });
    }
}
