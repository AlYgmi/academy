<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class EmailSetting extends Model
{


    protected $guarded = ['id'];
    protected $fillable = [];
}
