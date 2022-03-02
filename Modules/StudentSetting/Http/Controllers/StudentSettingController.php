<?php

namespace Modules\StudentSetting\Http\Controllers;


use Image;
use App\User;
use Carbon\Carbon;
use App\Subscription;
use App\StudentCustomField;
use Illuminate\Support\Str;

use App\Events\OneToOneConnection;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;


use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\CourseSetting\Entities\Notification;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Payment\Entities\InstructorPayout;
use Modules\SystemSetting\Entities\GeneralSettings;

use Yajra\DataTables\Facades\DataTables;
use Modules\Newsletter\Http\Controllers\AcelleController;


class StudentSettingController extends Controller
{


    public function index()
    {
        try {
            $students = [];

            return view('studentsetting::student_list', compact('students'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }


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

            $success = trans('lang.Student') . ' ' . trans('lang.Added') . ' ' . trans('lang.Successfully');


            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = null;
            $user->password = bcrypt($request->password);
            $user->about = $request->about;

            if (empty($request->phone)) {
                $user->phone = null;
            } else {
                $user->phone = $request->phone;
            }

            $user->dob = $request->dob;
            $user->facebook = $request->facebook;
            $user->twitter = $request->twitter;
            $user->linkedin = $request->linkedin;
            $user->youtube = $request->youtube;
            $user->gender = $request->gender;
            $user->language_id = Settings('language_id');
            $user->language_code = Settings('language_code');
            $user->language_name = Settings('language_name');
            $user->language_rtl = Settings('language_rtl');
            $user->country = Settings('country_id');
            $user->username = null;


            $user->added_by = 1;
            $user->email_verify = 1;
            $user->email_verified_at = now();
            $user->referral = Str::random(10);


            if ($request->file('image') != "") {
                $file = $request->file('image');
                $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/students/', $fileName);
                $fileName = 'public/uploads/students/' . $fileName;
                $user->image = $fileName;
            }

            $user->role_id = 3;

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

                if ($setting->student_status == 1) {
                    $list = $setting->student_list_id;
                    if ($setting->student_service == "Mailchimp") {

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
                    } elseif ($setting->student_service == "GetResponse") {
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
                                $email = $user->email;
                                $make_action_url = '/subscribers?list_uid=' . $list . '&EMAIL=' . $email;
                                $acelleController = new AcelleController();
                                $response = $acelleController->curlPostRequest($make_action_url);
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->student_service == "Local") {
                        try {
                            $check = Subscription::where('email', '=', $user->email)->first();
                            if (empty($check)) {
                                $subscribe = new Subscription();
                                $subscribe->email = $user->email;
                                $subscribe->type = 'Student';
                                $subscribe->save();
                            } else {
                                $check->type = "Student";
                                $check->save();
                            }
                        } catch (\Exception $e) {

                        }
                    }
                }


            }


            Toastr::success($success, 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function field()
    {
        $field = StudentCustomField::first();

        return view('studentsetting::field_setting', compact('field'));
    }

    public function fieldStore(Request $request)
    {


        try {
            $entry = StudentCustomField::first();
            if ($entry) {
                $entry->delete();
            }

            $request = $this->editableConfig($request);


            StudentCustomField::create($request->all());

            Toastr::success('Student custom field updated!', trans('common.Success'));
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function editableConfig(Request $request): Request
    {
        $request['editable_company'] = $request->editable_company ? 1 : 0;
        $request['editable_gender'] = $request->editable_gender ? 1 : 0;
        $request['editable_student_type'] = $request->editable_student_type ? 1 : 0;
        $request['editable_identification_number'] = $request->editable_identification_number ? 1 : 0;
        $request['editable_job_title'] = $request->editable_job_title ? 1 : 0;
        $request['editable_dob'] = $request->editable_dob ? 1 : 0;
        $request['editable_name'] = $request->editable_name ? 1 : 0;
        $request['editable_phone'] = $request->editable_phone ? 1 : 0;

        $request['show_company'] = $request->show_company ? 1 : 0;
        $request['show_gender'] = $request->show_gender ? 1 : 0;
        $request['show_student_type'] = $request->show_student_type ? 1 : 0;
        $request['show_identification_number'] = $request->show_identification_number ? 1 : 0;
        $request['show_job_title'] = $request->show_job_title ? 1 : 0;
        $request['show_dob'] = $request->show_dob ? 1 : 0;
        $request['show_name'] = $request->show_name ? 1 : 0;
        $request['show_phone'] = $request->show_phone ? 1 : 0;

        $request['required_company'] = $request->required_company ? 1 : 0;
        $request['required_gender'] = $request->required_gender ? 1 : 0;
        $request['required_student_type'] = $request->required_student_type ? 1 : 0;
        $request['required_identification_number'] = $request->required_identification_number ? 1 : 0;
        $request['required_job_title'] = $request->required_job_title ? 1 : 0;
        $request['required_dob'] = $request->required_dob ? 1 : 0;
        $request['required_name'] = $request->required_name ? 1 : 0;
        $request['required_phone'] = $request->required_phone ? 1 : 0;
        return $request;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('studentship::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
                return redirect()->back();
            } else {
                // $success = trans('lang.Student') .' '.trans('lang.Updated').' '.trans('lang.Successfully');

                $user = User::find($request->id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->username = null;
                if (empty($request->phone)) {
                    $user->phone = null;
                } else {
                    $user->phone = $request->phone;
                }
                $user->dob = $request->dob;
                $user->facebook = $request->facebook;
                $user->twitter = $request->twitter;
                $user->linkedin = $request->linkedin;
                $user->youtube = $request->youtube;
                $user->about = $request->about;
                $user->email_verify = 1;
                $user->gender = $request->gender;
                if ($request->password) {
                    $user->password = bcrypt($request->password);
                }
                if ($request->file('image') != "") {
                    $file = $request->file('image');
                    $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                    $file->move('public/uploads/students/', $fileName);
                    $fileName = 'public/uploads/students/' . $fileName;
                    $user->image = $fileName;
                }
                $user->role_id = 3;
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
            $success = trans('lang.Student') . ' ' . trans('lang.Deleted') . ' ' . trans('lang.Successfully');

            $user = User::findOrFail($request->id);
            $user->delete();

            Toastr::success($success, 'Success');
            return redirect()->back();

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function getAllStudentData(Request $request)
    {


        $query = User::where('role_id', 3)->where('teach_via', 1);

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('image', function ($query) {
                return " <div class=\"profile_info\"><img src='" . getStudentImage($query->image) . "'   alt='" . $query->name . " image'></div>";
            })->editColumn('name', function ($query) {
                return $query->name;

            })->editColumn('email', function ($query) {
                return $query->email;

            })
            ->editColumn('phone', function ($query) {
                return $query->phone;

            })
            ->editColumn('gender', function ($query) {
                return ucfirst($query->gender);

            })
            ->editColumn('dob', function ($query) {
                return showDate($query->dob);

            })
            ->editColumn('country', function ($query) {
                return $query->userCountry->name;

            })
            ->addColumn('status', function ($query) {

                $checked = $query->status == 1 ? "checked" : "";
                $view = '<label class="switch_toggle" for="active_checkbox' . $query->id . '">
                                                    <input type="checkbox" class="status_enable_disable"
                                                           id="active_checkbox' . $query->id . '" value="' . $query->id . '"
                                                             ' . $checked . '><i class="slider round"></i></label>';

                return $view;


            })->addColumn('action', function ($query) {

                if (permissionCheck('student.edit')) {

                    $student_edit = '  <button
                                                                                        data-item-id =\'' . $query->id . '\'
                                                                    class="dropdown-item editStudent"
                                                                    type="button">' . trans('common.Edit') . '</button>';
                } else {
                    $student_edit = "";
                }


                if (permissionCheck('student.delete')) {

                    $student_delete = '<button class="dropdown-item deleteStudent"
                                                                    data-id="' . $query->id . '"
                                                                    type="button">' . trans('common.Delete') . '</button>';
                } else {
                    $student_delete = "";
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
                                                        ' . $student_edit . '
                                                        ' . $student_delete . '




                                                    </div>
                                                </div>';

                return $actioinView;


            })->rawColumns(['status', 'image', 'action'])
            ->make(true);
    }


    public function newEnroll()
    {

        try {
            $courses = Course::where('status', 1)->select('id', 'title', 'type')->get();
            $students = User::where('role_id', 3)->where('status', 1)->select('id', 'name')->get();
            return view('studentsetting::new_enroll', compact('courses', 'students'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function newEnrollSubmit(Request $request)
    {


        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'student' => 'required|array',
            'course' => 'required'

        ];

        $this->validate($request, $rules, validationMessage($rules));
        try {
            $students = $request->student;

            foreach ($students as $student) {

                $user = User::find($student);
                if ($user) {
                    $course = Course::findOrFail($request->course);
                    $instractor = User::findOrFail($course->user_id);

                    $check = CourseEnrolled::where('user_id', $user->id)->where('course_id', $request->course)->first();
                    if ($check) {
                        Toastr::error($user->name . ' has already been enrolled to this course', 'Success');

                    } else {
                        $enrolled = $course->total_enrolled;
                        $course->total_enrolled = ($enrolled + 1);
                        $enrolled = new CourseEnrolled();
                        $enrolled->user_id = $user->id;
                        $enrolled->course_id = $request->course;
                        $enrolled->purchase_price = $course->discount_price != null ? $course->discount_price : $course->price;
                        $enrolled->save();


                        $itemPrice = $enrolled->purchase_price;


                        if (!is_null($course->special_commission) && $course->special_commission != 0) {
                            $commission = $course->special_commission;
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        } elseif (!is_null($instractor->special_commission) && $instractor->special_commission != 0) {
                            $commission = $instractor->special_commission;
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        } else {
                            $commission = 100 - GeneralSettings::first()->commission;
                            $reveune = ($itemPrice * $commission) / 100;
                            $enrolled->reveune = $reveune;
                        }

                        $payout = new InstructorPayout();
                        $payout->instructor_id = $course->user_id;
                        $payout->reveune = $reveune;
                        $payout->status = 0;
                        $payout->save();


                        send_email($user, 'Course_Enroll_Payment', [
                            'time' => \Illuminate\Support\Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'currency' => $user->currency->symbol ?? '$',
                            'price' => ($user->currency->conversion_rate * $itemPrice),
                            'instructor' => $course->user->name,
                            'gateway' => 'Offline',
                        ]);;
                        send_email($instractor, 'Enroll_notify_Instructor', [
                            'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                            'course' => $course->title,
                            'currency' => $instractor->currency->symbol ?? '$',
                            'price' => ($instractor->currency->conversion_rate * $itemPrice),
                            'rev' => @$reveune,
                        ]);

                        $enrolled->save();

                        $course->reveune = (($course->reveune) + ($enrolled->reveune));

                        $course->save();

                        $notification = new Notification();
                        $notification->author_id = $course->user_id;
                        $notification->user_id = $user->id;
                        $notification->course_id = $course->id;
                        $notification->course_enrolled_id = $enrolled->id;
                        $notification->status = 0;

                        $notification->save();

                        if (isModuleActive('Chat')) {
                            event(new OneToOneConnection($instractor, $user, $course));
                        }

                        //start email subscription
                        if ($instractor->subscription_api_status == 1) {
                            try {
                                if ($instractor->subscription_method == "Mailchimp") {
                                    $list = $course->subscription_list;
                                    $MailChimp = new MailChimp($instractor->subscription_api_key);
                                    $MailChimp->post("lists/$list/members", [
                                        'email_address' => Auth::user()->email,
                                        'status' => 'subscribed',
                                    ]);

                                } elseif ($instractor->subscription_method == "GetResponse") {

                                    $list = $course->subscription_list;
                                    $getResponse = new \GetResponse($instractor->subscription_api_key);
                                    $getResponse->addContact(array(
                                        'email' => Auth::user()->email,
                                        'campaign' => array('campaignId' => $list),

                                    ));
                                }
                            } catch (\Exception $exception) {
                                GettingError($exception->getMessage(), url()->current(), request()->ip(), request()->userAgent(), true);

                            }
                        }
                        Toastr::success($user->name . ' Successfully Enrolled this course', 'Success');
                    }


                }

            }


            return redirect()->to(route('admin.enrollLogs'));

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }
}
