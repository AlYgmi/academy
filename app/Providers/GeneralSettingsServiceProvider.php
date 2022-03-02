<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Modules\ModuleManager\Entities\InfixModuleManager;
use Modules\ModuleManager\Entities\Module;
use Modules\RolePermission\Entities\Role;
use Modules\Setting\Model\GeneralSetting;
use Modules\Setting\Model\BusinessSetting;
use Modules\Localization\Entities\Language;

class GeneralSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ModuleList', function () {
            return Cache::rememberForever('ModuleList', function () {
                return Module::select('name', 'status', 'order', 'details')->get();
            });
        });

        $this->app->singleton('ModulePackageList', function () {
            return \Nwidart\Modules\Facades\Module::all();
        });

        $this->app->singleton('ModuleManagerList', function () {
            return Cache::rememberForever('ModuleManagerList', function () {
                return InfixModuleManager::select('name', 'email', 'notes', 'version', 'update_url', 'purchase_code', 'installed_domain', 'activated_date', 'checksum')->get();
            });
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
