<?php

namespace Modules\VirtualClass\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\BBB\Entities\BbbMeeting;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\Course;
use Modules\Jitsi\Entities\JitsiMeeting;
use Modules\Localization\Entities\Language;
use Modules\Zoom\Entities\ZoomMeeting;

class VirtualClass extends Model
{


    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault();
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id')->withDefault(
            [
                'name' => ''
            ]
        );
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id')->withDefault();
    }

    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class, 'class_id')->orderBy('start_time', 'asc');
    }

    public function bbbMeetings()
    {
        return $this->hasMany(BbbMeeting::class, 'class_id')->orderBy('datetime', 'asc');
    }

    public function jitsiMeetings()
    {
        return $this->hasMany(JitsiMeeting::class, 'class_id')->orderBy('datetime', 'asc');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'class_id')->withDefault();
    }

    public function getSlugAttribute()
    {
        return Str::slug($this->title) == "" ? str_replace(' ', '-', $this->title) : Str::slug($this->title);

    }


}
