<?php

namespace Modules\Blog\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Blog extends Model
{


    protected $guarded = ['id'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('BlogPosList');
        });
        self::updated(function ($model) {
            Cache::forget('BlogPosList');
        });
    }
}
