<?php

namespace Modules\SystemSetting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Testimonial extends Model
{


    protected $fillable = [];

    public function course(){

        return $this->belongsTo(Course::class,'course_id' );
    }

    public function replies(){

        return $this->hasMany(CourseCommentReply::class,'comment_id' );
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id' );
    }
    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            Cache::forget('TestimonialList');
        });
        self::updated(function ($model) {
            Cache::forget('TestimonialList');
        });
    }

}
