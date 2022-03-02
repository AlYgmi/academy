<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Package;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Subscriber extends Model
{


    protected $fillable = [];

    public function package()
    {
        return $this->belongsTo(Package::class,'package_id');
    }
}
