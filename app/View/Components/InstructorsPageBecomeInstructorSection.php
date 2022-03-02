<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class InstructorsPageBecomeInstructorSection extends Component
{

    public function render()
    {
        $data['homeContent'] = Cache::rememberForever('HomeContentList', function () {
            return DB::table('home_contents')
                ->where('active_status', 1)
                ->first();
        });
        return view(theme('components.instructors-page-become-instructor-section'), $data);
    }
}
