<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class AboutPageTestimonial extends Component
{
    public $frontendContent;

    public function __construct($frontendContent)
    {
        $this->frontendContent = $frontendContent;
    }


    public function render()
    {
        $testimonials = Cache::rememberForever('TestimonialList', function () {
            return DB::table('testimonials')
                ->select('body', 'image', 'author', 'profession', 'star')
                ->where('status', '=', 1)
                ->get();
        });
        return view(theme('components.about-page-testimonial'), compact('testimonials'));
    }
}
