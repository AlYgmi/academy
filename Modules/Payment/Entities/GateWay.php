<?php

namespace Modules\Payment\Entities;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class GateWay extends Model
{


    protected $fillable = [];
    protected $table = 'gateways';
}
