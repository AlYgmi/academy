<?php

namespace Modules\Setting\Http\Controllers;

use App\Country;
use App\Traits\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Model\Currency;
use Modules\Setting\Model\TimeZone;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Setting\Model\DateFormat;
use Modules\Setting\Model\GeneralSetting;
use Modules\Setting\Entities\StudentSetup;
use Modules\Setting\Model\BusinessSetting;
use Modules\Localization\Entities\Language;
use Illuminate\Contracts\Support\Renderable;
use Modules\Setting\Entities\InstructorSetup;
use Modules\FrontendManage\Entities\HomeContent;
use Modules\SystemSetting\Entities\EmailSetting;

class SettingController extends Controller
{
    use ImageStore;

    public function activation()
    {
        $business_settings = BusinessSetting::all();
        $setting = GeneralSetting::first();
        return view('setting::activation', compact('business_settings', 'setting'));
    }


    public function general_settings()
    {
        $business_settings = BusinessSetting::all();
        $setting = GeneralSetting::first();
        $date_formats = DateFormat::all();
        $languages = Language::where('status', 1)->get();
        $countries = Country::where('active_status', 1)->get();
        $currencies = Currency::where('status', 1)->get();
        $timeZones = TimeZone::all();
        return view('setting::general_settings', compact('timeZones', 'currencies', 'countries', 'languages', 'business_settings', 'setting', 'date_formats'));
    }

    public function email_setup()
    {
        $business_settings = BusinessSetting::all();
        $setting = GeneralSetting::first();
        $emailSettings = EmailSetting::all();
        $send_mail_setting = $emailSettings->where('id', 1)->first();
        $smtp_mail_setting = $emailSettings->where('id', 2)->first();
        $send_grid_mail_setting = $emailSettings->where('id', 3)->first();
        return view('setting::email_setup2', compact('emailSettings', 'business_settings', 'setting', 'send_mail_setting', 'smtp_mail_setting', 'send_grid_mail_setting'));
    }

    public function seo_setting()
    {
        $business_settings = BusinessSetting::all();
        $setting = GeneralSetting::first();
        return view('setting::seo_setting', compact('business_settings', 'setting'));
    }


    public function index()
    {
        return redirect()->route('home');
    }


    public function update_activation_status(Request $request)
    {
        if (demoCheck()) {
            return 2;
        }
        $id = $request->id;
        $business_setting = BusinessSetting::findOrFail($id);
        if ($business_setting != null) {
            $business_setting->status = $request->status;
            $business_setting->save();

            $setting = GeneralSetting::first();

            if ($id == 1) {
                $setting->email_verification = $request->status;
            } elseif ($id == 2) {
                $setting->language_translation = $request->status;
            }
            $setting->save();

            return 1;
        }
        return 0;
    }

    public function maintenance()
    {
        $setting = HomeContent::first();
        return view('setting::maintenance', compact('setting'));
    }

    public function maintenanceAction(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {


            $content = HomeContent::first();
            if ($request->maintenance_banner != null) {

                if ($request->file('maintenance_banner')->extension() == "svg") {
                    $file = $request->file('maintenance_banner');
                    $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName;
                    $file->move(public_path('uploads/settings'), $fileName);
                } else {
                    $url1 = $this->saveImage($request->maintenance_banner);
                }
                $content->maintenance_banner = $url1;
            }
            $content->maintenance_title = $request->maintenance_title;
            $content->maintenance_sub_title = $request->maintenance_sub_title;
            $content->maintenance_status = $request->maintenance_status;
            $setting = GeneralSetting::first();
            $setting->maintenance_status = $request->maintenance_status;
            $setting->save();
            $content->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function captcha()
    {
        return view('setting::captcha');
    }

    public function captchaStore(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $site_key = $request->get('site_key');
        $secret_key = $request->get('secret_key');
        $login_status = $request->get('login_status');
        $reg_status = $request->get('reg_status');
        $contact_status = $request->get('contact_status');
        $is_invisible = $request->get('is_invisible');

        putEnvConfigration('NOCAPTCHA_SITEKEY', $site_key);
        putEnvConfigration('NOCAPTCHA_SECRET', $secret_key);

        if ($is_invisible == 1) {
            $is_invisible = 'true';
        } else {
            $is_invisible = 'false';
        }
        putEnvConfigration('NOCAPTCHA_IS_INVISIBLE', $is_invisible);


        if ($login_status == 1) {
            $login_status = 'true';
        } else {
            $login_status = 'false';
        }
        putEnvConfigration('NOCAPTCHA_FOR_LOGIN', $login_status);

        if ($reg_status == 1) {
            $reg_status = 'true';
        } else {
            $reg_status = 'false';
        }
        putEnvConfigration('NOCAPTCHA_FOR_REG', $reg_status);

        if ($contact_status == 1) {
            $contact_status = 'true';
        } else {
            $contact_status = 'false';
        }
        putEnvConfigration('NOCAPTCHA_FOR_CONTACT', $contact_status);

        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }
    public function student_setup(){
        try {
            $data= StudentSetup::first();
            return view('setting::studentSetup', compact('data'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }
    public function student_setup_update(Request $request){
        try {
            $data= StudentSetup::first();
            $data->show_running_course_thumb= $request->show_running_course_thumb;
            $data->show_recommended_section= $request->show_recommended_section;
            $data->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('setting::studentSetup', compact('data'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }
    public function instructor_setup(){
        try {
            $data= InstructorSetup::first();
            return view('setting::instructorSetup', compact('data'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }
    public function instructor_setup_update(Request $request){
        try {
            $data= InstructorSetup::first();
            $data->show_instructor_page_banner= $request->show_instructor_page_banner;
            $data->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('setting::instructorSetup', compact('data'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    public function socialLogin()
    {
        return view('setting::socialLogin');
    }

    public function socialLoginStore(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $allow_google_login = $request->get('allow_google_login');
        $google_client_id = $request->get('google_client_id');
        $google_secret_key = $request->get('google_secret_key');

        $allow_facebook_login = $request->get('allow_facebook_login');
        $facebook_client_id = $request->get('facebook_client_id');
        $facebook_secret_key = $request->get('facebook_secret_key');

        putEnvConfigration('GOOGLE_CLIENT_ID', $google_client_id);
        putEnvConfigration('GOOGLE_CLIENT_SECRET', $google_secret_key);


        putEnvConfigration('FACEBOOK_CLIENT_ID', $facebook_client_id);
        putEnvConfigration('FACEBOOK_CLIENT_SECRET', $facebook_secret_key);


        if ($allow_google_login == 1) {
            $login_status = 'true';
        } else {
            $login_status = 'false';
        }
        putEnvConfigration('ALLOW_GOOGLE_LOGIN', $login_status);

        if ($allow_facebook_login == 1) {
            $allow_facebook_login = 'true';
        } else {
            $allow_facebook_login = 'false';
        }
        putEnvConfigration('ALLOW_FACEBOOK_LOGIN', $allow_facebook_login);


        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }




}
