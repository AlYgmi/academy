<?php

namespace Modules\SystemSetting\Http\Controllers;

use Image;
use App\User;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DrewM\MailChimp\MailChimp;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Newsletter\Http\Controllers\AcelleController;


class InstructorSettingController extends Controller
{
    public function index()
    {

        try {
            $instructors = [];

            return view('systemsetting::instructor', compact('instructors'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('systemsetting::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Session::flash('type', 'store');

        if (demoCheck()) {
            return redirect()->back();
        }


        $rules = [
            'name' => 'required',
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:5|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];


        $this->validate($request, $rules, validationMessage($rules));


        try {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = null;
            $user->password = bcrypt($request->password);
            $user->about = $request->about;
            $user->dob = $request->dob;

            if (empty($request->phone)) {
                $user->phone = null;
            } else {
                $user->phone = $request->phone;
            }
            $user->language_id = Settings('language_id');
            $user->language_code = Settings('language_code');
            $user->language_name = Settings('language_name');
            $user->language_rtl = Settings('language_rtl');
            $user->country = Settings('country_id');
            $user->username = null;
            $user->facebook = $request->facebook;
            $user->twitter = $request->twitter;
            $user->linkedin = $request->linkedin;
            $user->instagram = $request->instagram;
            $user->added_by = 1;
            $user->email_verify = 1;
            $user->email_verified_at = now();

            if ($request->file('image') != "") {
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/instructors/', $fileName);
                $fileName = 'public/uploads/instructors/' . $fileName;
                $user->image = $fileName;
            }

            $user->role_id = 2;
            $user->save();


            if (Schema::hasTable('users') && Schema::hasTable('chat_statuses')) {
                if (isModuleActive('Chat')) {
                    userStatusChange($user->id, 0);
                }
            }


            $mailchimpStatus = env('MailChimp_Status') ?? false;
            $getResponseStatus = env('GET_RESPONSE_STATUS') ?? false;
            $acelleStatus = env('ACELLE_STATUS') ?? false;
            if (hasTable('newsletter_settings')) {
                $setting = NewsletterSetting::first();


                if ($setting->instructor_status == 1) {
                    $list = $setting->instructor_list_id;
                    if ($setting->instructor_service == "Mailchimp") {

                        if ($mailchimpStatus) {
                            try {
                                $MailChimp = new MailChimp(env('MailChimp_API'));
                                $MailChimp->post("lists/$list/members", [
                                    'email_address' => $user->email,
                                    'status' => 'subscribed',
                                ]);

                            } catch (\Exception $e) {
                            }
                        }
                    } elseif ($setting->instructor_service == "GetResponse") {
                        if ($getResponseStatus) {

                            try {
                                $getResponse = new \GetResponse(env('GET_RESPONSE_API'));
                                $getResponse->addContact(array(
                                    'email' => $user->email,
                                    'campaign' => array('campaignId' => $list),
                                ));
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->instructor_service == "Acelle") {
                        if ($acelleStatus) {

                            try {
                                $email=$user->email;
                                $make_action_url='/subscribers?list_uid='.$list.'&EMAIL='.$email;
                                $acelleController=new AcelleController();
                                $response=$acelleController->curlPostRequest($make_action_url);
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->instructor_service == "Local") {
                        try {
                            $check = Subscription::where('email', '=', $user->email)->first();
                            if (empty($check)) {
                                $subscribe = new Subscription();
                                $subscribe->email = $user->email;
                                $subscribe->type = 'Instructor';
                                $subscribe->save();
                            } else {
                                $check->type = "Instructor";
                                $check->save();
                            }
                        } catch (\Exception $e) {

                        }
                    }
                }


            }


            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function show($id)
    {
        return view('systemsetting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

    }

    public function update(Request $request)
    {
        Session::flash('type', 'update');

        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'name' => 'required',
            'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users,phone,' . $request->id,
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'bail|nullable|min:8|confirmed',

        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return back();
            } else {

                $user = User::find($request->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = null;
                $user->facebook = $request->facebook;
                $user->twitter = $request->twitter;
                $user->linkedin = $request->linkedin;
                $user->instagram = $request->instagram;
                $user->about = $request->about;
                $user->dob = $request->dob;
                if (empty($request->phone)) {
                    $user->phone = null;
                } else {
                    $user->phone = $request->phone;
                }
                if ($request->password)
                    $user->password = bcrypt($request->password);


                $fileName = "";
                if ($request->file('image') != "") {
                    $file = $request->file('image');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/instructors/', $fileName);
                    $fileName = 'public/uploads/instructors/' . $fileName;
                    $user->image = $fileName;
                }

                $user->role_id = 2;
                $user->save();

            }

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'id' => 'required'
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {

            if (Config::get('app.app_sync')) {
                Toastr::error('For demo version you can not change this !', 'Failed');
                return redirect()->back();
            } else {
                $success = trans('lang.Instructor') . ' ' . trans('lang.Updated') . ' ' . trans('lang.Successfully');

                $user = User::findOrFail($request->id);
                $user->delete();

            }
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function getAllInstructorData(Request $request)
    {


        $query = User::where('role_id', 2);

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($query) {
                return " <div class=\"profile_info\"><img src='" . getInstructorImage($query->image) . "'   alt='" . $query->name . " image'></div>";
            })->editColumn('name', function ($query) {
                return $query->name;

            })->editColumn('email', function ($query) {
                return $query->email;

            })->addColumn('status', function ($query) {

                $checked = $query->status == 1 ? "checked" : "";
                $view = '<label class="switch_toggle" for="active_checkbox' . $query->id . '">
                                                    <input type="checkbox" class="status_enable_disable"
                                                           id="active_checkbox' . $query->id . '" value="' . $query->id . '"
                                                             ' . $checked . '><i class="slider round"></i></label>';

                return $view;


            })->addColumn('action', function ($query) {

                if (permissionCheck('instructor.edit')) {
                    $instructor_edit = '  <button
                                                                                        data-item-id =\'' . $query->id. '\'
                                                                    class="dropdown-item editInstructor"
                                                                    type="button">' . trans('common.Edit') . '</button>';
                } else {
                    $instructor_edit = "";
                }


                if (permissionCheck('instructor.delete')) {

                    $instructor_delete = '<button class="dropdown-item deleteInstructor"
                                                                    data-id="' . $query->id . '"
                                                                    type="button">' . trans('common.Delete') . '</button>';
                } else {
                    $instructor_delete = "";
                }

                $actioinView = ' <div class="dropdown CRM_dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown"
                                                            aria-haspopup="true"
                                                            aria-expanded="false">
                                                        ' . trans('common.Action') . '
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         aria-labelledby="dropdownMenu2">
                                                        ' . $instructor_edit . '
                                                        ' . $instructor_delete . '




                                                    </div>
                                                </div>';

                return $actioinView;


            })->rawColumns(['status', 'image', 'action'])->make(true);
    }

}
