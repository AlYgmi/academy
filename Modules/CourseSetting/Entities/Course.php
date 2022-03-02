<?php

namespace Modules\CourseSetting\Entities;

use App\LessonComplete;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Localization\Entities\Language;
use Modules\Payment\Entities\Cart;
use Modules\Quiz\Entities\OnlineQuiz;
use Modules\Subscription\Entities\SubscriptionCourseList;
use Modules\VirtualClass\Entities\VirtualClass;

class Course extends Model
{


    protected $fillable = [];

    protected $appends = ['dateFormat', 'publishedDate', 'sumRev', 'purchasePrice', 'enrollCount'];

//    protected $casts = [
//        'meta_keywords' => 'object'
//    ];

    public function enrollUsers()
    {
        return $this->belongsToMany(User::class, 'course_enrolleds', 'course_id', 'user_id');
    }


    public function cartUsers()
    {
        return $this->belongsToMany(User::class, 'carts', 'course_id', 'user_id');
    }

    public function BookmarkUsers()
    {
        return $this->belongsToMany(User::class, 'bookmark_courses', 'course_id', 'user_id');
    }

    public function quiz()
    {

        return $this->belongsTo(OnlineQuiz::class, 'quiz_id', 'id')
            ->withDefault([
                'title' => ' '
            ]);
    }

    public function class()
    {

        return $this->belongsTo(VirtualClass::class, 'class_id', 'id')->withDefault();
    }

    public function category()
    {

        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault([
            'name' => ' '
        ]);
    }

    public function user()
    {

        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault([
            'name' => ' '
        ]);
    }

    public function subCategory()
    {

        return $this->belongsTo(Category::class, 'subcategory_id', 'id')->withDefault();
    }

    public function chapters()
    {

        return $this->hasMany(Chapter::class)->orderBy('position', 'asc');
    }

    public function lessons()
    {

        return $this->hasMany(Lesson::class, 'course_id')
            ->select(
                'id',
                'course_id',
                'chapter_id',
                'quiz_id',
                'name',
                'description',
                'video_url',
                'host',
                'duration',
                'is_lock',
                'is_quiz',
            )->orderBy('position', 'asc');
    }

    public function enrolls()
    {

        return $this->hasMany(CourseEnrolled::class, 'course_id', 'id');
    }

    public function comments()
    {

        return $this->hasMany(CourseComment::class, 'course_id')
            ->select(
                'id',
                'user_id',
                'course_id',
                'instructor_id',
                'status',
                'comment',
                'created_at',
            );
    }

    public function reviews()
    {

        return $this->hasMany(CourseReveiw::class, 'course_id')
            ->select(
                'user_id',
                'course_id',
                'status',
                'comment',
                'star',
            );
    }

    public function files()
    {

        return $this->hasMany(CourseExercise::class, 'course_id');
    }

    public function getdateFormatAttribute()
    {
        return Carbon::parse($this->created_at)->format(Settings('active_date_format') ?? 'jS M, Y');
    }

    public function getpublishedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format(Settings('active_date_format') ?? 'jS M, Y');
    }

    public function getsumRevAttribute()
    {
        return round($this->enrolls->sum('reveune'), 2);
    }

    public function getenrollCountAttribute()
    {
        return $this->enrolls->count();
    }

    public function getpurchasePriceAttribute()
    {
        return round($this->enrolls->sum('purchase_price'), 2);
    }

    public function virtualClass()
    {
        return $this->belongsTo(VirtualClass::class, 'class_id');
    }

    public function completeLessons()
    {
        if (Auth::check()) {
            return $this->hasMany(LessonComplete::class)->where('user_id', Auth::user()->id);

        } else {
            return $this->hasMany(LessonComplete::class)->whereNull('user_id');

        }
    }

    public function getPriceAttribute()
    {

        $price = $this->attributes['price'];

        if (Auth::check() && Auth::user()->role_id == 3 && isModuleActive('Subscription') && isSubscribe()) {
            $type = Settings('subscription_type');
            if ($type == 2) {
                if ($this->attributes['subscription'] == 0) {
                    return $price;
                } else {
                    $plans = userCurrentPlan();
                    if (count($plans) != 0) {
                        $check = SubscriptionCourseList::whereIn('plan_id', $plans)->where('course_id', $this->attributes['id'])->first();
                        if ($check) {
                            return 0;
                        } else {
                            return $price;
                        }
                    } else {
                        return $price;
                    }
                }
            }
            return 0;
        }
        return $price;
    }

    public function getDiscountPriceAttribute()
    {
        $price = $this->attributes['discount_price'];

        if (Auth::check() && Auth::user()->role_id == 3 && isModuleActive('Subscription') && isSubscribe()) {
            return 0;
        }
        return $price;
    }

    public function courseLevel()
    {
        return $this->belongsTo(CourseLevel::class, 'level')->withDefault();
    }

    public function activeReviews()
    {
        return $this->hasMany(CourseReveiw::class, 'course_id', 'id')->where('status', 1);
    }

    public function getTotalReviewAttribute()
    {
        return $this->total_rating;
    }

    public function getIsLoginUserEnrolledAttribute()
    {
        if (\auth()->user()->role_id == 1) {
            return true;
        }
        if (\auth()->user()->role_id == 2) {
            if ($this->user_id = \auth()->user()->id) {
                return true;
            }
        }

        if (!$this->enrollUsers->where('id', \auth()->user()->id)->count()) {
            return false;
        } else {
            return true;
        }
    }

    public function getIsLoginUserCartAttribute()
    {
        if (!$this->cartUsers->where('user_id', \auth()->user()->id)->count()) {
            return false;
        } else {
            return true;
        }
    }

    public function getIsLoginUserBookmarkAttribute()
    {
        if (!$this->BookmarkUsers->where('user_id', \auth()->user()->id)->count()) {
            return false;
        } else {
            return true;
        }
    }
    public function getIsLoginUserReviewAttribute()
    {
        if (!$this->activeReviews->where('user_id', \auth()->user()->id)->count()) {
            return false;
        } else {
            return true;
        }
    }


    public function getLoginUserTotalPercentageAttribute()
    {

        $countCourse = count($this->completeLessons->where('status', 1));
        if ($countCourse != 0) {
            $percentage = ceil($countCourse / count($this->lessons) * 100);
        } else {
            $percentage = 0;
        }
        if ($percentage>100){
            $percentage=100;
        }
        return $percentage;

    }

    public function getIsGuestUserCartAttribute()
    {
        if (session()->has('cart')) {
            foreach (session()->get('cart') as $item) {
                if ($item['course_id'] == $this->id) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getStarWiseReviewAttribute()
    {
        $data['1'] = $this->activeReviews->where('star', '1')->count();
        $data['2'] = $this->activeReviews->where('star', '2')->count();
        $data['3'] = $this->activeReviews->where('star', '3')->count();
        $data['4'] = $this->activeReviews->where('star', '4')->count();
        $data['5'] = $this->activeReviews->where('star', '5')->count();
        $data['total'] = $data['1'] + $data['2'] + $data['3'] + $data['4'] + $data['5'];
        return $data;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($course) {

            $course->slug = $course->createSlug($course->title);

            $course->save();
        });
        static::updating(function ($course) {

            $carts = Cart::where('course_id', $course->id)->get();
            foreach ($carts as $cart) {
                if ($course->discount_price != null) {
                    $price = $course->discount_price;
                } else {
                    $price = $course->price;
                }
                $cart->price = $price;
                $cart->save();
            }

        });
    }

    private function createSlug($name)
    {
        if (static::whereSlug($slug = Str::slug($name))->exists()) {

            $max = static::whereTitle($name)->latest('id')->skip(1)->value('slug');

            if (isset($max[-1]) && is_numeric($max[-1])) {

                return preg_replace_callback('/(\d+)$/', function ($mathces) {

                    return $mathces[1] + 1;
                }, $max);
            }
            return "{$slug}-2";
        }
        return $slug;
    }
    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id', 'id')->withDefault();

    }

}
