<?php

namespace Modules\Setting\Http\Controllers;

use App\Traits\ImageStore;
use App\Traits\SendMail;
use App\Traits\SendSMS;
use App\Traits\UploadTheme;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Modules\Setting\Model\GeneralSetting;
use Modules\Setting\Model\TimeZone;
use Modules\Setting\Repositories\GeneralSettingRepositoryInterface;
use Modules\SystemSetting\Entities\EmailTemplate;
use Modules\SystemSetting\Entities\GeneralSettings;
use Spatie\Valuestore\Valuestore;


class GeneralSettingsController extends Controller
{
    use ImageStore, SendSMS, SendMail, UploadTheme;

    protected $generalsettingRepository;

    public function __construct(GeneralSettingRepositoryInterface $generalsettingRepository)
    {
        $this->generalsettingRepository = $generalsettingRepository;
    }

    public function update(Request $request)
    {

        if (appMode()) {
            $general_setting = GeneralSetting::first();
            $general_setting->time_zone_id = $request->time_zone_id;
            $general_setting->currency_id = $request->currency_id;
            $general_setting->save();
            return 2;
        }


        if ($request->site_logo != null) {
            $site_log_sizes = [
                ['640', '1136'],
                ['750', '1334'],
                ['828', '1792'],
                ['1125', '2436'],
                ['1242', '2208'],
                ['1242', '2688'],
                ['1536', '2048'],
                ['1668', '2224'],
                ['1668', '2388'],
                ['2048', '2732'],
            ];
            if ($request->file('site_logo')->extension() == "svg") {
                $file = $request->file('site_logo');
                $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                $url1 = 'public/uploads/settings/' . $fileName;
                $file->move(public_path('uploads/settings'), $fileName);



            } else {
                $url1 = $this->saveImage($request->site_logo);

                foreach ($site_log_sizes as $size) {
                    $rowImage = Image::canvas($size[0], $size[1],'#fff');
                    $rowImage->insert( $request->file('site_logo'),'center');
                    $rowImage->save(public_path("images/icons/splash-{$size[0]}x{$size[1]}.png"));
                }
            }



            $request->merge(["logo" => $url1]);
        }
        if ($request->site_logo2 != null) {

            if ($request->file('site_logo2')->extension() == "svg") {
                $file1 = $request->file('site_logo2');
                $fileName1 = md5(rand(0, 9999) . '_' . time()) . '.' . $file1->clientExtension();
                $url2 = 'public/uploads/settings/' . $fileName1;
                $file1->move(public_path('uploads/settings'), $fileName1);

            } else {
                $url2 = $this->saveImage($request->site_logo2);
            }

            $request->merge(["logo2" => $url2]);
        }
        if ($request->favicon_logo != null) {
            $fav_icon_sizes = [72, 96, 128, 144, 152, 192, 384, 512];
            if ($request->file('favicon_logo')->extension() == "svg") {
                $file3 = $request->file('favicon_logo');
                $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                $url = 'public/uploads/settings/' . $fileName3;
                $file3->move(public_path('uploads/settings'), $fileName3);


                foreach ($fav_icon_sizes as $size) {
                    $file3->move(public_path('images/icons/'), "icon-{$size}x{$size}.svg");
                }

            } else {
                $url = $this->saveImage($request->favicon_logo);

                foreach ($fav_icon_sizes as $size) {
                    Image::make($request->file('favicon_logo'))->resize($size, $size)->save(public_path("images/icons/icon-{$size}x{$size}.png"));
                }


            }
            $request->merge(["favicon" => $url]);


        }
        if ($request->address != null) {
            $this->generalsettingRepository->address = $request->address;

            $this->generalsettingRepository->update(['address' => $request->address]);

        }

        $key1 = 'TIME_ZONE';

        if ($request->time_zone_id) {
            $time_zone = TimeZone::find($request->time_zone_id);
            $value1 = $time_zone->code ?? 83;

            $path = base_path() . "/.env";
            $TIME_ZONE = env($key1);
            if (file_exists($path)) {
                file_put_contents($path, str_replace(
                    "$key1=" . $TIME_ZONE,
                    "$key1=" . $value1,
                    file_get_contents($path)
                ));
            }
        }


        if ($request->site_title) {
            putEnvConfigration('APP_NAME', $request->site_title);
        }

        try {


            $this->generalsettingRepository->update($request->except("_token", "favicon_logo", "site_logo", "site_logo2"));

            $setting = GeneralSetting::first();
            $setting->language_code = $setting->language->code;
            $setting->language_name = $setting->language->name;
            $setting->currency_symbol = $setting->currency->symbol;
            $setting->currency_code = $setting->currency->code;
            $setting->language_rtl = $setting->language->rtl;
            $setting->active_date_format = $setting->date_format->format;
            $setting->active_time_zone = $setting->timeZone->code;
            $setting->language_rtl = $setting->language->rtl;
            $setting->save();

            $user = Auth::user();
            $user->language_id = $request->language_id;
            $user->language_name = $setting->language_name;
            $user->language_code = $setting->language_code;
            $user->language_rtl = $setting->language_rtl;
            $user->save();


            session()->forget('settings');


            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }


    public function smtp_gateway_credentials_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $general_setting = GeneralSetting::first();
        $general_setting->mail_protocol = $request->mail_protocol;
        $general_setting->mail_signature = $request->mail_signature;
        $general_setting->mail_header = $request->mail_header;
        $general_setting->mail_footer = $request->mail_footer;
        $general_setting->save();
        session()->forget('settings');

        if ($request->mail_protocol == 'sendmail') {
            $request->merge(["MAIL_MAILER" => "smtp"]);
        } else {
            $request->merge(["MAIL_MAILER" => $request->mail_protocol]);
        }
        foreach ($request->types as $key => $type) {
            $this->overWriteEnvFile($type, $request[$type]);
        }
        // return back()->with('message-success', __('setting.SMTP Gateways Credentials has been updated Successfully'));
        Toastr::success(__('setting.SMTP Gateways Credentials has been updated Successfully'), trans('common.Success'));
        return redirect()->back();
    }

    public function test_mail_send(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        if (env('MAIL_USERNAME') != null) {
            $this->sendMailTest($request);
            // return back()->with('message-success', __('setting.Mail has been sent Successfully'));
            Toastr::success(__('setting.Mail has been sent Successfully'), trans('common.Success'));
            return redirect()->back();
        }
        // return back()->with('message-warning', __('setting.Please Configure SMTP settings first'));
        Toastr::warning(__('setting.Please Configure SMTP settings first'), 'Warning');
        return redirect()->back();
    }

    public function socialCreditional(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        if ($request->fbLogin == 1) {
            $request->validate([

                'facebook_client' => "required",
                'facebook_secret' => "required",
            ]);
        } elseif ($request->googleLogin == 1) {
            $request->validate([
                'google_client' => "required",
                'google_secret' => "required"
            ]);
        } else {
            $request->validate([
                'google_client' => "required",
                'google_secret' => "required",
                'facebook_client' => "required",
                'facebook_secret' => "required",
            ]);

        }

        try {

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                $success = trans('lang.Settings') . ' ' . trans('lang.Updated') . ' ' . trans('lang.Successfully');

                $social = GeneralSettings::first();
                $social->google_client = $request->google_client;
                $social->google_secret = $request->google_secret;
                $social->facebook_client = $request->facebook_client;
                $social->facebook_secret = $request->facebook_secret;
                $social->fbLogin = $request->fbLogin;
                $social->googleLogin = $request->googleLogin;
                $social->save();
                session()->forget('settings');

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function seoSetting(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $request->validate([
            'meta_keywords' => 'required',
            'meta_description' => 'required',

        ]);
        try {
            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {

                $generalSettData = GeneralSettings::first();

                $generalSettData->meta_keywords = $request->meta_keywords;
                $generalSettData->meta_description = $request->meta_description;
                $generalSettData->save();

                session()->forget('settings');

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function footerEmailConfig()
    {
        $eTemplate = GeneralSettings::first();
        return view('setting::emails.email_template', compact('eTemplate'));
    }

    public function EmailTemp()
    {

        $templates = EmailTemplate::get();
        return view('setting::emails.email_temp', compact('templates'));
    }

    public function aboutSystem()
    {
        return view('setting::aboutSystem');
    }


}
