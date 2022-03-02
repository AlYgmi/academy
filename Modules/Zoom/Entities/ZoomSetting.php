<?php

namespace Modules\Zoom\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ZoomSetting extends Model
{



    protected $guarded = ['id'];
    protected $table = 'zoom_settings';
}
