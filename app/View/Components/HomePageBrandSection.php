<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Modules\FrontendManage\Entities\Sponsor;

class HomePageBrandSection extends Component
{
    public $homeContent;

    public function __construct($homeContent)
    {
        $this->homeContent = $homeContent;
    }

    public function render()
    {
        $sponsors = Cache::rememberForever('SponsorList', function () {
            return Sponsor::where('status', 1)
                ->get();
        });
        return view(theme('components.home-page-brand-section'), compact('sponsors'));
    }
}
