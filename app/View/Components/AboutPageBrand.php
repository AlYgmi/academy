<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Modules\FrontendManage\Entities\Sponsor;

class AboutPageBrand extends Component
{

    public function render()
    {
        $sponsors = Cache::rememberForever('SponsorList', function () {
            return Sponsor::where('status', 1)
                ->get();
        });
        return view(theme('components.about-page-brand'),compact('sponsors'));
    }
}
