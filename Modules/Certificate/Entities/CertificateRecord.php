<?php

namespace Modules\Certificate\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\CourseSetting\Entities\Course;

class CertificateRecord extends Model
{
    protected $fillable = [];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id')->withDefault();
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id')->withDefault();
    }
}
