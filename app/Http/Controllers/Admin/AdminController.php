<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Subscription;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\Payment\Entities\InstructorPayout;
use Modules\Payment\Entities\Withdraw;
use Modules\Subscription\Entities\SubscriptionCheckout;
use Modules\Subscription\Entities\SubscriptionCourse;
use Yajra\DataTables\DataTables;

class AdminController extends Controller
{
    public function enrollLogs(Request $request)
    {
        if (!empty($request->course)) {
            $courseId = $request->course;
        } else {
            $courseId = '';
        }
        if (!empty($request->start_date)) {
            $start = date('Y-m-d', strtotime($request->start_date));
        } else {
            $start = '';
        }
        if (!empty($request->end_date)) {
            $end = date('Y-m-d', strtotime($request->end_date));
        } else {
            $end = '';
        }
        try {
            $enrolls = [];
            $courses = Course::all();
            $students = User::where('role_id', 3)->get();
            return view('backend.student.enroll_student', compact('courseId', 'start', 'end', 'enrolls', 'courses', 'students'));

        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function enrollFilter(Request $request)
    {

        try {
            if (!empty($request->course)) {
                $courseId = $request->course;
            } else {
                $courseId = '';
            }
            if (!empty($request->start_date)) {
                $start = date('Y-m-d', strtotime($request->start_date));
            } else {
                $start = '';
            }
            if (!empty($request->end_date)) {
                $end = date('Y-m-d', strtotime($request->end_date));
            } else {
                $end = '';
            }


            $courses = Course::all();
            $students = User::where('role_id', 3)->get();
            return view('backend.student.enroll_student', compact('courseId', 'start', 'end', 'courses', 'students'));


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function reveuneList()
    {
        try {
            $courses = Course::with('enrolls', 'user')->withCount('enrolls')->get();

            return view('payment::admin_revenue', compact('courses'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);


        }
    }

    public function reveuneListInstructor(Request $request)
    {
        try {
            if (empty($request->instructor)) {
                $search_instructor = '';
            } else {
                $search_instructor = $request->instructor;

            }
            if (empty($request->month)) {
                $search_month = '';
            } else {
                $search_month = $request->month;
            }
            if (empty($request->year)) {
                $search_year = date('Y');
            } else {
                $search_year = $request->year;

            }


            $query = CourseEnrolled::with('course', 'user', 'course.user');

            if (!empty($search_month)) {
                $from = date($search_year . '-' . $search_month . '-30');
                $to = date('Y-m-d');
                $query->whereBetween('created_at', [$from, $to]);
            }

            if (Auth::user()->role_id == 2) {
                $query->where('user_id', '=', Auth::user()->id);
            }

            $enrolls = $query->whereHas('course.user', function ($query) {
                $query->where('id', '!=', 1);
            })->latest()->get();


            $query2 = DB::table('subscription_courses')
                ->select('subscription_courses.*')
                ->selectRaw("SUM(revenue) as total_price");
            if (Auth::user()->role_id == 2) {
                $query2->where('user_id', '=', Auth::user()->id);
            }


            if (isModuleActive('Subscription')) {
                $subscriptionsData = $query2->groupBy('checkout_id')
                    ->latest()->get();;
                $subscriptions = [];
                foreach ($subscriptionsData as $key => $data) {
                    $subscriptions[$key]['checkout_id'] = $data->checkout_id;
                    $subscriptions[$key]['date'] = $data->date;
                    $subscriptions[$key]['price'] = $data->total_price;
                    $user = User::where('id', $data->instructor_id)->first();
                    $subscriptions[$key]['instructor'] = $user->name ?? '';

                    $plan = SubscriptionCheckout::where('id', $data->checkout_id)->first();

                    $subscriptions[$key]['plan'] = $plan->plan->title ?? '';
                }


            } else {
                $subscriptions = [];
            }
            $instructors = User::where('role_id', 2)->get();
            return view('payment::instructor_revenue_report', compact('search_instructor', 'search_month', 'search_year', 'instructors', 'enrolls', 'subscriptions'));
        } catch
        (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);
        }

    }

    public function sortByDiscount(Request $request)
    {

        $rules = [
            'discount' => 'required',
            'id' => 'required'
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $id = $request->id;
            $val = $request->discount;
            $start = date('Y-m-d', strtotime($request->start_date));
            $end = date('Y-m-d', strtotime($request->end_date));
            $method = $request->methods;
            if ((isset($request->end_date)) && (isset($request->start_date))) {

                if ($val == 10) {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '>', 0)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->latest()->with('user')->get();
                } else {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '=', 0)->whereDate('created_at', '>=', $start)->whereDate('created_at', '<=', $end)->latest()->with('user')->get();

                }
            } elseif (is_null($request->start_date) && is_null($request->end_date)) {

                if ($val == 10) {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '>', 0)->with('user', 'course')->latest()->get();
                } else {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '=', 0)->with('user', 'course')->latest()->get();

                }
            } elseif (isset($request->start_date) && is_null($request->end_date)) {


                if ($val == 10) {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '>', 0)->with('user', 'course')->whereDate('created_at', '>=', $start)->latest()->get();
                } else {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '=', 0)->with('user', 'course')->whereDate('created_at', '>=', $start)->latest()->get();

                }

            } elseif (isset($request->end_date) && is_null($start)) {

                if ($val == 10) {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '>', 0)->with('user', 'course')->whereDate('created_at', '<=', $end)->latest()->get();
                } else {

                    $logs = CourseEnrolled::where('course_id', $id)->where('discount_amount', '=', 0)->with('user', 'course')->whereDate('created_at', '<=', $end)->latest()->get();

                }
            }
            $course_id = $request->id;
            return view('payment::enroll_log', compact('logs', 'course_id'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function subscriberMailSingle(Request $request)
    {

        $rules = [
            'subject' => 'required',
            'body' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            $subscriber = Subscription::find($request->id);
            $receiver_name = explode('@', $subscriber->email)[0];
            send_general_email($subscriber->email, $request->subject, $request->body, $receiver_name);

            Toastr::success('Email will be sent to subscribers', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function courseEnrolls($id)
    {

        try {
            $logs = CourseEnrolled::where('course_id', $id)->with('user', 'course')->latest()->get();
            $course_id = $id;
            return view('payment::enroll_log', compact('logs', 'course_id'));
        } catch (\Exception $e) {
            return response()->json(['error' => trans("lang.Oops, Something Went Wrong")]);


        }
    }

    public function instructorPayout(Request $request)
    {
        $instructors = User::where('role_id', 2)->get();
        /*if (auth()->user()->role_id == 1) {


            $query = Withdraw::latest();

            if (isset($request->month)) {
                $query->whereMonth('created_at', '=', $request->month);
            }
            if (isset($request->year)) {
                $query->whereYear('created_at', '=', $request->year);
            }
            if (isset($request->instructor)) {
                $query->whereYear('instructor_id', '=', $request->instructor);
            }

            $withdraws = $query->with('user')->latest()->get();

        } else {
            $withdraws = Withdraw::with('user')->where('instructor_id', auth()->id())->latest()->get();

        }*/
        $next_pay = InstructorPayout::where('instructor_id', Auth::user()->id)->whereStatus('0')->sum('reveune');
        if (isModuleActive('Subscription')) {
            $subscriptionPay = SubscriptionCourse::where('instructor_id', Auth::user()->id)->whereStatus('0')->sum('revenue');
            $next_pay = $next_pay + $subscriptionPay;
        }


        return view('payment::instructor_payout', compact('next_pay', 'instructors'));
    }

    public function instructorRequestPayout()
    {

        try {
            $user = Auth::user();
            $amount = InstructorPayout::where('instructor_id', $user->id)->whereStatus('0')->sum('reveune');
            if (isModuleActive('Subscription')) {
                $subscriptionPay = SubscriptionCourse::where('instructor_id', $user->id)->whereStatus('0')->sum('revenue');
                $amount = $amount + $subscriptionPay;
            }
            $withdraw = new Withdraw();
            $withdraw->instructor_id = Auth::user()->id;
            $withdraw->amount = $amount;
            $withdraw->method = Auth::user()->payout;
            $withdraw->save();

            InstructorPayout::where('instructor_id', $user->id)->whereStatus('0')->update(['status' => 1]);
            if (isModuleActive('Subscription')) {
                SubscriptionCourse::where('instructor_id', $user->id)->whereStatus('0')->update(['status' => 1]);
            }

            Toastr::success('Payment request has been successfully submitted', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function instructorCompletePayout(Request $request)
    {
        try {
            DB::beginTransaction();
            $withdraw = Withdraw::whereId($request->withdraw_id)->whereInstructorId($request->instructor_id)->first();
            $instractor = User::find($request->instructor_id);
            $withdraw->status = 1;
            $withdraw->save();
            $instractor->balance += $withdraw->amount;
            $instractor->save();
            DB::commit();
            Toastr::success('Payment request has been Approved', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function enrollDelete($id)
    {

        if (demoCheck()) {
            return redirect()->back();
        }
        $enroll = CourseEnrolled::findOrFail($id);

        $user = Auth::user();
        if ($user->role_id == 1 || $enroll->user->id == $user->id) {
            $enroll->delete();

        }
        Toastr::success(trans('common.Operation successful'), trans('common.Success'));
        return redirect()->back();
    }

    public function getEnrollLogsData(Request $request)
    {
        $user = Auth::user();
        if ($user->role_id == 2) {
            $query = CourseEnrolled::with('user', 'course')
                ->whereHas('course', function ($query) use ($user) {
                    $query->where('user_id', '=', $user->id);
                });

        } else {
            $query = CourseEnrolled::with('user', 'course');

        }


        if (!empty($request->course)) {
            $query->where('course_id', $request->course);
        }
        if (!empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }


        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('user.image', function ($query) {
                return " <div class=\"profile_info\"><img src='" . getInstructorImage($query->user->image) . "'   alt='" . $query->user->name . " image'></div>";
            })->editColumn('user.name', function ($query) {
                return $query->user->name;

            })->editColumn('user.email', function ($query) {
                return $query->user->email;

            })
            ->editColumn('course.title', function ($query) {
                return $query->course->title;

            })
            ->editColumn('created_at', function ($query) {
                return showDate(@$query->created_at);

            })
            ->addColumn('action', function ($query) {


                if (permissionCheck('course.delete')) {
                    $deleteUrl = route('admin.enrollDelete', $query->id);
                    $course_delete = '<a onclick="confirm_modal(\'' . $deleteUrl . '\')"
                                                               class="dropdown-item edit_brand">' . trans('common.Delete') . '</a>';
                } else {
                    $course_delete = "";
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

                                                        ' . $course_delete . '




                                                    </div>
                                                </div>';

                return $actioinView;


            })->rawColumns(['user.image', 'action'])->make(true);
    }


    public function getPayoutData(Request $request)
    {
        try {


            $query = Withdraw::latest()->with('user');
            if (!empty($request->month)) {
                $query->whereMonth('created_at', '=', $request->month);
            }
            if (!empty($request->year)) {
                $query->whereYear('created_at', '=', $request->year);
            }
            if (!empty($request->instructor)) {
                $query->where('instructor_id', '=', $request->instructor);
            }
            if (Auth::user()->role_id != 1) {
                $query->where('instructor_id', '=', Auth::user()->id);
            }


            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('user.name', function ($query) {

                    return $query->user->name;

                })->editColumn('amount', function ($query) {
                    return $query->amount;

                })
                ->addColumn('requested_date', function ($query) {

                    return showDate(@$query->created_at);

                })
                ->editColumn('method', function ($query) {
                    $withdraw = $query;
                    return view('backend.partials._withdrawMethod', compact('withdraw'));

                })
                ->addColumn('status', function ($query) {

                    if ($query->status == 1) {
                        $status = 'Paid';
                    } else {
                        $status = 'Unpaid';
                    }
                    return $status;

                })
                ->addColumn('action', function ($query) {


                    if (\auth()->user()->role_id == 1 && $query->status != 1) {
                        $view = '<div class="dropdown CRM_dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenu2" data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                               ' . trans('common.Action') . '
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="dropdownMenu2">
                                                <a href="#" class="dropdown-item makeAsPaid" data-instructor_id="' . $query->instructor_id . '"
                                                data-withdraw_id="' . $query->id . '"
                                                   type="button">' . trans('common.Make Paid') . '</a>

                                            </div>
                                        </div>';
                    } else {
                        $view = '';
                    }
                    return $view;
                })->rawColumns(['method', 'user.image', 'action'])->make(true);
        } catch (\Exception $e) {

        }
    }

    public function getUserDate($id)
    {
        $user = User::find($id);
        return $user;
    }

}
