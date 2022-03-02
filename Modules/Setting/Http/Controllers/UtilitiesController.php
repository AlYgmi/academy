<?php

namespace Modules\Setting\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class UtilitiesController extends Controller
{
    private $utilitiesRepository;


    public function index(Request $request)
    {
        if (isset($request->utilities)) {
            if (demoCheck()) {
                return redirect()->back();
            }

            $utility = $request->utilities;
            if ($utility == "optimize_clear") {
                Artisan::call('optimize:clear');

                $dirname = base_path('/bootstrap/cache/');

                if (is_dir($dirname)){
                    $dir_handle = opendir($dirname);
                }else{
                    $dir_handle=false;
                }
                if (!$dir_handle)
                    return false;
                while ($file = readdir($dir_handle)) {
                    if ($file != "." && $file != "..") {
                        if (!is_dir($dirname . "/" . $file))
                            unlink($dirname . "/" . $file);
                        else
                            $this->delete_directory($dirname . '/' . $file);
                    }
                }
                closedir($dir_handle);

            } elseif ($utility == "clear_log") {
                array_map('unlink', array_filter((array)glob(storage_path('logs/*.log'))));
                array_map('unlink', array_filter((array)glob(storage_path('debugbar/*.json'))));

            } elseif ($utility == "change_debug") {
                envu([
                    'APP_DEBUG' => env('APP_DEBUG') ? "false" : "true"
                ]);
            } elseif ($utility == "force_https") {
                putEnvConfigration('FORCE_HTTPS', env('FORCE_HTTPS') ? "false" : "true");

            } else {
                Toastr::error(trans('common.Invalid Command'), trans('common.Failed'));
                return redirect()->back();
            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        }
        return view('setting::utilities');
    }

}
