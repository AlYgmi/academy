<?php

namespace App\Exports;

use App\Country;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CountryList implements FromView
{
    public function view(): View
    {
        $countries = Country::where('active_status', 1)->get(['id', 'name']);
        return view('studentsetting::exports.country', [
            'countries' => $countries
        ]);
    }
}
