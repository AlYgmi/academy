<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Blog\Entities\Blog;
use Modules\CourseSetting\Entities\Course;
use Modules\FrontendManage\Entities\FrontPage;
use Modules\FrontendManage\Entities\Sponsor;

class FrontendHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }

    public function index()
    {

        try {
            $blocks = Cache::rememberForever('blocks', function () {
                return DB::table('homepage_block_positions')->orderBy('order', 'asc')->get();
            });
            $homeContent = Cache::rememberForever('HomeContentList', function () {
                return DB::table('home_contents')
                    ->where('active_status', 1)
                    ->first();
            });

            return view(theme('pages.index'), compact('blocks', 'homeContent'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);

        }
    }
}
