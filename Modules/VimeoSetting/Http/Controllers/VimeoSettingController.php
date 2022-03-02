<?php

namespace Modules\VimeoSetting\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\VimeoSetting\Entities\Vimeo;

class VimeoSettingController extends Controller
{

    public function index()
    {
        $videoSetting = Vimeo::where('active_status', 1)->where('created_by', Auth::user()->id)->first();
        return view('vimeosetting::index', compact('videoSetting'));
    }


    public function update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        try {

            $vimeoSetting = Vimeo::where('active_status', 1)->where('created_by', Auth::user()->id)->first();
            if (empty($vimeoSetting)) {
                $vimeoSetting = new Vimeo();
            }
            $vimeoSetting->vimeo_app_id = $request->vimeo_app_id;
            $vimeoSetting->vimeo_client = $request->vimeo_client;
            $vimeoSetting->vimeo_secret = $request->vimeo_secret;
            $vimeoSetting->vimeo_access = $request->vimeo_access;
            $vimeoSetting->created_by = Auth::user()->id;
            $vimeoSetting->updated_by = Auth::user()->id;
            $results = $vimeoSetting->save();

            if (Auth::user()->role_id == 1) {
                $key1 = 'VIMEO_CLIENT';
                $key2 = 'VIMEO_SECRET';
                $key3 = 'VIMEO_ACCESS';
                $key4 = 'VIMEO_COMMON_USE';
                $key5 = 'VIMEO_UPLOAD_TYPE';


                $value1 = $request->vimeo_client;
                $value2 = $request->vimeo_secret;
                $value3 = $request->vimeo_access;
                $value4 = $request->common_use;
                $value5 = $request->upload_type;

                putEnvConfigration($key1, $value1);
                putEnvConfigration($key2, $value2);
                putEnvConfigration($key3, $value3);
                putEnvConfigration($key5, $value5);

                if ($value4 == 1) {
                    $value4 = "true";
                } else {
                    $value4 = "false";
                }
                putEnvConfigration($key4, $value4);
            }

            if ($results) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }
        } catch (Exception $e) {

            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


}
