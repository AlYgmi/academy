<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\CourseSetting\Entities\Course;

class EdumeFrontendThemeController extends Controller
{
    public function getCourseByCategory($category_id)
    {
        $courses = Course::where('category_id', $category_id)->where('status', 1)
            ->with('courseLevel', 'user', 'reviews', 'lessons')
            ->where('type', 1)->latest()->get();
        $result = '  <div class="lms_course_grid">';
        foreach ($courses as $key => $course) {
            $result .= view(theme('components.single-course-with-out-column'), compact('course'));
        }

        $result .= '  </div > ';

        return $result;
    }
}
