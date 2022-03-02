<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Modules\CourseSetting\Entities\Category;
use Modules\CourseSetting\Entities\SubCategory;

class ExportSubCategory implements FromView
{

    public function view(): View
    {
        $categories = Category::whereNotNull('parent_id')->latest()->get();

        return view('quiz::exports.categories', [
            'categories' => $categories
        ]);
    }
}
