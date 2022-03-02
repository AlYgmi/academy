<?php

namespace App\View\Components;

use Carbon\Carbon;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Modules\CourseSetting\Entities\Course;
use Modules\Setting\Entities\StudentSetup;
use Modules\CourseSetting\Entities\CourseEnrolled;

class MyDashboardPageSection extends Component
{

    public function __construct()
    {
        //
    }

    public function render()
    {
        $data['user'] = Auth::user();
        $total_spent = CourseEnrolled::where('user_id', Auth::user()->id)->sum('purchase_price');
        $total_purchase = CourseEnrolled::where('user_id', Auth::user()->id)->count() ?? 0;


        $Hour = date('G');

        if ($Hour >= 5 && $Hour <= 11) {
            $wish_string = trans("student.Good Morning");
        } else if ($Hour >= 12 && $Hour <= 18) {
            $wish_string = trans("student.Good Afternoon");
        } else if ($Hour >= 19 || $Hour <= 4) {
            $wish_string = trans("student.Good Evening");
        }
        $date = Carbon::now(Settings('active_time_zone'))->format("jS F Y \, l");

        $mycourse = CourseEnrolled::where('user_id', Auth::user()->id)
            ->whereHas('course', function ($query) {
                $query->where('type', '=', 1);
            })
            ->with('course', 'course.lessons', 'course.activeReviews', 'course.completeLessons', 'course.completeLessons')->take(3)->get();

        $student_setup = StudentSetup::first();
        $courses = Course::where('type', 1)->where('status', 1)->inRandomOrder()->limit(3)->with('lessons', 'enrollUsers', 'cartUsers')->get();
        $quizzes = Course::where('type', 2)->where('status', 1)->inRandomOrder()->limit(3)->with('quiz', 'quiz.assign', 'enrollUsers', 'cartUsers')->get();
        $classes = Course::where('type', 3)->where('status', 1)->inRandomOrder()->limit(3)->with('class', 'enrollUsers', 'cartUsers')->get();

        return view(theme('components.my-dashboard-page-section'), compact('quizzes', 'courses', 'classes', 'data', 'mycourse', 'wish_string', 'date', 'total_purchase', 'student_setup', 'total_spent'));
    }
}
