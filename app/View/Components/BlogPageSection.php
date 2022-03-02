<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Blog\Entities\Blog;

class BlogPageSection extends Component
{
    public function render()
    {
        $blogs = Blog::where('status', 1)->with('user')->latest()->paginate(10);
        return view(theme('components.blog-page-section'), compact('blogs'));
    }
}
