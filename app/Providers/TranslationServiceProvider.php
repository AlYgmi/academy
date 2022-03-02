<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (DB::connection()->getDatabaseName() != '') {
                if (Schema::hasTable('languages')) {
                    if (is_null(app('general_settings')->get('chat_language_cache'))) {
                        Cache::forget('translations');
                        app('general_settings')->put('chat_language_cache', 'cached');
                    }
                    Cache::remember('translations', Carbon::now()->addHours(6), function () {
                        return $this->getTranslations();
                    });
                }
            }
        } catch (\Exception $exception) {

        }


    }

    private function getTranslations()
    {
        $translations = collect();

        try {
            $ln = DB::table('languages')->where('status', 1)->pluck('code')->toArray();
            foreach ($ln as $locale) {
                $translations[$locale] = [
                    'php' => $this->phpTranslations($locale),
                    'json' => $this->jsonTranslations($locale),
                ];
            }
        } catch (\Exception $exception) {

        }

        return $translations;
    }

    private function phpTranslations($locale)
    {
        try {
            $path = resource_path("lang/$locale");

            return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
                $key = ($translation = $file->getBasename('.php'));

                return [$key => trans($translation, [], $locale)];
            });
        } catch (\Exception $exception) {
            $path = resource_path("lang/en");
            return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
                $key = ($translation = $file->getBasename('.php'));
                return [$key => trans($translation, [], $locale)];
            });
        }
    }

    private function jsonTranslations($locale)
    {
        try {
            $path = resource_path("lang/$locale/$locale.json");
            if (is_string($path) && is_readable($path)) {
                return json_decode(file_get_contents($path), true);
            }
        } catch (\Exception $exception) {

        }

        return [];
    }
}
