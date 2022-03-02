<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Modules\Blog\Entities\Blog;

class HomePageBlogSection extends Component
{
    public $homeContent;

    public function __construct($homeContent)
    {
        $this->homeContent = $homeContent;
    }

    public function render()
    {
        $blogs = Cache::rememberForever('BlogPosList', function () {
            return Blog::where('status', 1)
                ->with('user')
                ->latest()
                ->take(4)
                ->get();
        });
        return view(theme('components.home-page-blog-section'), compact('blogs'));
    }
}
