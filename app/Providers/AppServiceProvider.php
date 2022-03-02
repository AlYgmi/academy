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

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('general_settings', function () {
            return Valuestore::make((base_path() . '/general_settings.json'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('FORCE_HTTPS')) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);

        try {
            $datatable = DB::connection()->getDatabaseName();
            if ($datatable) {
                if (Schema::hasTable('chat_notifications')) {
                    view()->composer([
                        'backend.partials.menu',
                        theme('partials._dashboard_master'),
                        theme('partials._dashboard_menu'),
                        theme('pages.fullscreen_video'),
                    ], function ($view) {
                        $notifications = DB::table('chat_notifications')->where('notifiable_id', auth()->id())
                            ->where('read_at', null)
                            ->get();

                        foreach ($notifications as $notification) {
                            $notification->data = json_decode($notification->data);
                        }
                        $notifications = $notifications->sortByDesc('created_at');

                        $view->with(['notifications_for_chat' => $notifications]);
                    });
                }


                view()->composer('*', function ($view) {

                    $seed = session()->get('user_status_seedable');
                    if (isModuleActive('Chat') && auth()->check() && is_null($seed)) {
                        $users = User::all();
                        foreach ($users as $user) {
                            if (Schema::hasTable('chat_statuses')) {
                                Status::firstOrCreate([
                                    'user_id' => $user->id,
                                ], [
                                    'user_id' => $user->id,
                                    'status' => 0
                                ]);
                            }

                        }

                        session()->put('user_status_seedable', 'false');
                    }
                });

                view()->composer('*', function ($view) {
                    if (auth()->check()) {
                        $this->app->singleton('extend_view', function ($app) {
                            if (auth()->user()->role_id == 3) {
                                return theme('layouts.dashboard_master');
                            } else {
                                return 'backend.master';
                            }
                        });
                    }
                });

            }

            View::composer([theme('auth.layouts.app'),
                theme('layouts.full_screen_master'), theme('layouts.master'), theme('layouts.dashboard_master'), 'backend.partials._header'], function ($view) {
                if (session()->has('color_theme')) {
                    $color = session('color_theme');
                } else {
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
                    session()->put('color_theme', $color);
                }

                $view->with('color', $color);
            });


            View::composer([theme('partials._footer'),theme('partials._footer_content'), theme('partials._menu')], function ($view) {
                $data['newsletterSetting'] = Cache::rememberForever('newsletterSetting', function () {
                    return NewsletterSetting::select('home_status', 'home_service', 'home_list_id', 'student_status', 'student_service', 'student_list_id', 'instructor_status',
                        'instructor_status', 'instructor_service', 'instructor_list_id')->first();
                });
                $data['social_links'] = Cache::rememberForever('social_links', function () {
                    return DB::table('social_links')
                        ->select('link', 'icon', 'name')
                        ->where('status', '=', 1)
                        ->get();
                });

                if (Settings('cookie_status') == 0) {
                    $data['cookie'] = Cache::rememberForever('cookie', function () {
                        return CookieSetting::select('image', 'details', 'btn_text', 'text_color', 'bg_color', 'allow')->first();
                    });
                } else {
                    $data['cookie'] = Cache::rememberForever('cookie', function () {
                        return CookieSetting::select('image', 'details', 'btn_text', 'text_color', 'bg_color', 'allow')->first();
                    });
                }

                $sectionWidgets = Cache::rememberForever('sectionWidgets', function () {
                    return FooterWidget::where('status', 1)
                        ->with('frontpage')
                        ->get();
                });
                $data['sectionWidgets']['one'] = $sectionWidgets->where('section', '1');
                $data['sectionWidgets']['two'] = $sectionWidgets->where('section', '2');
                $data['sectionWidgets']['three'] = $sectionWidgets->where('section', '3');

                $data['homeContent'] = Cache::rememberForever('HomeContentList', function () {
                    return DB::table('home_contents')
                        ->where('active_status', 1)
                        ->first();
                });
                $view->with($data);
            });

            View::composer([
                theme('partials._dashboard_menu'),
                theme('pages.fullscreen_video'),
                theme('pages.index'),
                theme('pages.courses'),
                theme('pages.free_courses'),
                theme('partials._menu'),
                theme('pages.quizzes'),
                theme('pages.classes'),
                theme('pages.search'),
                theme('components.home-page-course-section')
            ], function ($view) {

                $data['categories'] = Cache::rememberForever('categories', function () {
                    return Category::select('id', 'name', 'title', 'description', 'image', 'thumbnail', 'parent_id')
                        ->with('childs')
                        ->where('status', 1)
                        ->whereNull('parent_id')
                        ->withCount('courses')
                        ->orderBy('position_order', 'ASC')->with('activeSubcategories', 'childs')
                        ->get();
                });


                $data['languages'] = Cache::rememberForever('languages', function () {
                    return Language::select('id', 'name', 'code', 'rtl', 'status', 'native')
                        ->where('status', 1)
                        ->get();
                });
                $data['menus'] = Cache::rememberForever('menus', function () {
                    return HeaderMenu::orderBy('position', 'asc')
                        ->select('id', 'type', 'element_id', 'title', 'link', 'parent_id', 'position', 'show', 'is_newtab')
                        ->with('childs')
                        ->get();
                });
                $view->with($data);
            });

            View::composer([
                theme('pages.blogs'),
                theme('pages.contact')
                , theme('*')
            ], function ($view) {
                $data['frontendContent'] = Cache::rememberForever('frontendContent', function () {
                    return DB::table('home_contents')
                        ->where('active_status', 1)
                        ->first();
                });
                $view->with($data);
            });

            if (isModuleActive('Tax')) {
                View::composer(['tax::country_wish', theme('checkout')], function ($view) {
                    $data['countryWishTaxList'] = Cache::rememberForever('countryWishTaxList', function () {
                        return DB::table('country_wish_taxes')
                            ->select('country_id', 'tax')
                            ->where('status', 1)
                            ->get();
                    });
                    $view->with($data);
                });
            }

        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }

    }
}
