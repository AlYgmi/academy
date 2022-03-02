<?php

namespace App\Providers;

use App\User;
use Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Modules\Chat\Entities\Status;
use Modules\CourseSetting\Entities\Category;
use Modules\FooterSetting\Entities\FooterWidget;
use Modules\FrontendManage\Entities\HeaderMenu;
use Modules\Localization\Entities\Language;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Setting\Entities\CookieSetting;
use Session;
use Spatie\Valuestore\Valuestore;

class CompactThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('frontendContent', function () {
            $frontendContent = DB::table('home_contents')
                        ->where('active_status', 1)
                        ->first();
            return $frontendContent;
        });
        $this->app->singleton('topbarSetting', function () {
            $topbarSetting = DB::table('topbar_settings')
                        ->first();
            return $topbarSetting;
        });



        $this->app->singleton('categories', function () {
            $categories=Category::select('id', 'name', 'title', 'description', 'image', 'thumbnail', 'parent_id')
                ->with('childs')
                ->where('status', 1)
                ->whereNull('parent_id')
                ->withCount('courses')
                ->orderBy('position_order', 'ASC')
                ->get();
            return $categories;
        });
        $this->app->singleton('menus', function () {
            $menus=HeaderMenu::orderBy('position', 'asc')
            ->select('id', 'type', 'element_id', 'title', 'link', 'parent_id', 'position', 'show', 'is_newtab')
            ->with('childs')
            ->get();
            return $menus;
        });
        $this->app->singleton('color', function () {
            $color = DB::table('themes')
            ->select(
                'theme_customizes.primary_color',
                'theme_customizes.secondary_color',
                'theme_customizes.footer_background_color',
                'theme_customizes.footer_headline_color',
                'theme_customizes.footer_text_color',
            )
            ->join('theme_customizes', 'themes.id', '=', 'theme_customizes.theme_id')
            ->where('themes.is_active', '=', 1)
            ->where('theme_customizes.is_default', '=', 1)
            ->first();
            return $color;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
