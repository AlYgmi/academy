<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\Lesson;
use Rennokki\QueryCache\Traits\QueryCacheable;

class LessonComplete extends Model
{
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
