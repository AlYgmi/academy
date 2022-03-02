<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
|
*/
require __DIR__ . '/table.php';


Auth::routes(['verify' => true]);

Route::group(['namespace' => 'Api'], function () {


});


Route::group([
    'namespace' => 'Api'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');


    //CourseApiController
    Route::get('/get-all-courses', 'CourseApiController@getAllCourses');
    Route::get('/get-all-classes', 'CourseApiController@getAllClasses');
    Route::get('/get-popular-courses', 'CourseApiController@getPopularCourses');
    Route::get('/get-popular-classes', 'CourseApiController@getPopularClasses');
    Route::get('/get-course-details/{id}', 'CourseApiController@getCourseDetails');
    Route::get('/get-class-details/{id}', 'CourseApiController@getClassDetails');
    Route::get('/top-categories', 'CourseApiController@topCategories');
    Route::get('/search-course', 'CourseApiController@searchCourse');
    Route::get('/filter-course', 'CourseApiController@filterCourse');
    Route::get('/payment-gateways', 'WebsiteApiController@paymentGateways');

    Route::get('categories', 'CourseApiController@categories');
    Route::get('sub-categories/{category_id}', 'CourseApiController@subCategories');
    Route::get('levels', 'CourseApiController@levels');
    Route::get('languages', 'CourseApiController@languages');


    Route::group([
        'middleware' => ['auth:api', 'verified']
    ], function () {
        //with login routes

        Route::get('/get-bbb-start-url/{meeting_id}/{user_name}', 'WebsiteApiController@getBbbMeetingUrl');

        Route::get('/cart-list', 'WebsiteApiController@cartList');
        Route::get('/add-to-cart/{id}', 'WebsiteApiController@addToCart');
        Route::get('/remove-to-cart/{id}', 'WebsiteApiController@removeCart');
        Route::post('/apply-coupon', 'WebsiteApiController@applyCoupon');

        //AuthController
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::post('change-password', 'AuthController@changePassword');
        Route::post('/update-profile', 'WebsiteApiController@updateProfile');


        //WebsiteApiController

        Route::get('/countries', 'WebsiteApiController@countries');
        Route::get('/cities/{country_id}', 'WebsiteApiController@cities');
        Route::get('/my-courses', 'WebsiteApiController@myCourses');
        Route::get('/my-quizzes', 'WebsiteApiController@myQuizzes');
        Route::get('/my-classes', 'WebsiteApiController@myClasses');
        Route::post('/submit-review', 'WebsiteApiController@submitReview');
        Route::post('/comment', 'WebsiteApiController@comment');
        Route::post('/comment-reply', 'WebsiteApiController@commentReply');
        Route::get('/payment-methods', 'WebsiteApiController@paymentMethods');

        Route::post('/make-order', 'WebsiteApiController@makeOrder');
        Route::post('/make-payment/{gateWayName}', 'WebsiteApiController@payWithGateWay');
        Route::get('/my-billing-address', 'WebsiteApiController@myBilling');

        Route::post('paytm-order-generate', 'WebsiteApiController@paytmOrderGenerate');
        Route::post('paytm-order-verify', 'WebsiteApiController@paytmOrderVerify');

    });
});
