<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class HomePageTestimonialSection extends Component
{
    public $homeContent;

    public function __construct($homeContent)
    {
        $this->homeContent = $homeContent;
    }

    public function render()
    {
        $testimonials = Cache::rememberForever('TestimonialList', function () {
            return DB::table('testimonials')
                ->select('body', 'image', 'author', 'profession', 'star')
                ->where('status', '=', 1)
                ->get();
        });
        return view(theme('components.home-page-testimonial-section'), compact('testimonials'));
    }
}
