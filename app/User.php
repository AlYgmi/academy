<?php

namespace App;

use App\Country;
use Carbon\Carbon;
use App\Traits\UserChatMethods;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\HasApiTokens;
use Modules\Payment\Entities\Withdraw;
use Illuminate\Notifications\Notifiable;
use Modules\Payment\Entities\Subscriber;
use Modules\RolePermission\Entities\Role;
use Modules\CourseSetting\Entities\Course;
use Modules\Localization\Entities\Language;
use Modules\SystemSetting\Entities\Message;
use Modules\SystemSetting\Entities\Currency;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Modules\Payment\Entities\InstructorPayout;
use Rennokki\QueryCache\Traits\QueryCacheable;
use App\Notifications\PasswordResetNotification;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\Quiz\Entities\StudentTakeOnlineQuiz;
use Modules\CourseSetting\Entities\CourseEnrolled;
use Modules\OfflinePayment\Entities\OfflinePayment;
use Illuminate\Foundation\Auth\User as Authenticatable;

//class User extends Authenticatable
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable, UserChatMethods;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'name', 'role_id', 'username', 'email', 'phone', 'country', 'password', 'email_verified_at', 'mobile_verified_at', 'avatar', 'subscribe', 'language_id',
        'language_name', 'language_code', 'language_rtl', 'provider_id', 'provider',
        'access_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['first_name', 'last_name', 'blocked_by_me'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class)->withDefault();
    }

    public function offlinePayments()
    {
        return $this->hasMany(OfflinePayment::class, 'user_id');
    }


    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriber::class, 'user_id', 'id')->whereDate('valid', '>=', Carbon::now());
    }


    public function enrolls()
    {
        return $this->hasManyThrough(CourseEnrolled::class, Course::class);
    }

    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'instructor_id');
    }


    public function earnings()
    {
        return $this->hasMany(InstructorPayout::class, 'instructor_id');
    }

    public function gettotalEarnAttribute()
    {

        return round($this->earnings()->sum('reveune'), 2);
    }

    public function lastMessage()
    {
        $message = Message::where('sender_id', $this->id)->orWhere('reciever_id', $this->id)->orderBy('id', 'desc')->first();
        if ($message) {
            return $message;
        } else {
            return null;
        }
    }

    public function reciever()
    {
        return $this->hasOne(Message::class, 'reciever_id', 'id')->latest();
    }


    public function sender()
    {
        return $this->hasOne(Message::class, 'sender_id')->latest();
    }

    public function getmessageFormatAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function enrollCourse()
    {
        return $this->belongsToMany(Course::class, 'course_enrolleds', 'user_id', 'course_id');
    }


    public function language()
    {
        return $this->belongsTo(Language::class);
    }


    public function recievers()
    {
        return $this->hasMany(Message::class, 'reciever_id')->latest();
    }

    public function senders()
    {
        return $this->hasMany(Message::class, 'sender_id')->latest();
    }

    public function userLanguage()
    {
        return $this->belongsTo(Language::class, 'language_id')->withDefault();
    }

    public function enrollStudents()
    {
        return $this->hasMany(CourseEnrolled::class)->EnrollStudent();
    }

    public function apiKey()
    {
        return $this->zoom_api_key_of_user;
    }

    public function apiSecret()
    {
        return $this->zoom_api_serect_of_user;
    }

    public function submittedExam()
    {
        return $this->hasOne(StudentTakeOnlineQuiz::class, 'student_id')->latest();
    }
    public function userCountry()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function totalCourses()
    {
        $totalCourses = Course::where('user_id', '=', $this->id)->count();
        return $totalCourses;
    }

    public function totalEnrolled()
    {
        $totalEnrolled = Course::where('user_id', '=', $this->id)->sum('total_enrolled');
        return $totalEnrolled;
    }

    public function totalRating()
    {

        $totalRatings['rating'] = 0;
        $ReviewList = DB::table('courses')
            ->join('course_reveiws', 'course_reveiws.course_id', 'courses.id')
            ->select('courses.id', 'course_reveiws.id as review_id', 'course_reveiws.star as review_star')
            ->where('courses.user_id', $this->id)
            ->get();
        $totalRatings['total'] = count($ReviewList);

        foreach ($ReviewList as $Review) {
            $totalRatings['rating'] += $Review->review_star;
        }

        if ($totalRatings['total'] != 0) {
            $avg = ($totalRatings['rating'] / $totalRatings['total']);
        } else {
            $avg = 0;
        }

        if ($avg != 0) {
            if ($avg - floor($avg) > 0) {
                $rate = number_format($avg, 1);
            } else {
                $rate = number_format($avg, 0);
            }
            $totalRatings['rating'] = $rate;
        }


        return $totalRatings;
    }

    public function sendEmailVerificationNotification()
    {
        if (!Session::has('reg_email')) {
            try {
                Session::put('reg_email', $this->email);
                $this->notify(new VerifyEmail());

            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }

    public function sendPasswordResetNotification($token)
    {
        try {
            $this->notify(new PasswordResetNotification($token));
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    public function cityName()
    {
        $find = DB::table('spn_cities')->where('id', $this->city)->first();
        $city = '';
        if ($find) {
            $city = $find->name;
        }
        return $city;
    }


}
