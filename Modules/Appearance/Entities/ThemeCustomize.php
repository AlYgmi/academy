<?php

namespace Modules\Appearance\Entities;

use Illuminate\Database\Eloquent\Model;

class ThemeCustomize extends Model
{
    protected $guarded = ['id'];

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id')->withDefault();
    }
}
