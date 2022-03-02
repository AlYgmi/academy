<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\SystemSetting\Entities\FooterContent;
use Rennokki\QueryCache\Traits\QueryCacheable;

class FooterCategory extends Model
{


    protected $fillable = ['title','description','placeholder'];

    public function contents()
    {
        return $this->hasMany(FooterContent::class);
    }
}
