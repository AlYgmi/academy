<?php

namespace App\View\Components;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Modules\CourseSetting\Entities\Course;

class InstructorPageSection extends Component
{
    public $id, $instructor;

    public function __construct($id, $instructor)
    {
        $this->instructor = $instructor;
        $this->id = $id;
    }


    public function render()
    {
        $students = DB::table('course_enrolleds')
            ->join('courses', 'course_enrolleds.course_id', '=', 'courses.id')
            ->where('courses.user_id', $this->id)
            ->distinct('course_enrolleds.user_id')->count();


        $rating = DB::table('course_reveiws')
            ->select('course_reveiws.*', 'courses.user_id')
            ->join('courses', 'course_reveiws.course_id', '=', 'courses.id')
            ->where('courses.user_id', $this->id)
            ->sum('star');
        $totalRating = DB::table('course_reveiws')
            ->join('courses', 'course_reveiws.course_id', '=', 'courses.id')
            ->where('courses.user_id', $this->id)->count();
        return view(theme('components.instructor-page-section'), compact('students', 'rating', 'totalRating'));
    }
}
