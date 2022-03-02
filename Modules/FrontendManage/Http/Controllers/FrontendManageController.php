<?php

namespace Modules\FrontendManage\Http\Controllers;

use Exception;
use Throwable;
use App\AboutPage;
use App\Http\Controllers\Controller;
use App\Traits\ImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use Modules\FrontendManage\Entities\FrontPage;
use Modules\SystemSetting\Entities\SocialLink;
use Modules\FrontendManage\Entities\HomeSlider;
use Modules\SystemSetting\Entities\Testimonial;
use Modules\FrontendManage\Entities\HomeContent;
use Modules\FrontendManage\Entities\CourseSetting;
use Modules\FrontendManage\Entities\PrivacyPolicy;
use Modules\FrontendManage\Entities\TopbarSetting;
use Modules\SystemSetting\Entities\FrontendSetting;

class FrontendManageController extends Controller
{
    use ImageStore;

    public function index()
    {
        return 'Frontend Manage';
    }


    // HomeContent
    public function HomeContent()
    {
        try {
            $home_content = HomeContent::find(1);
            $pages = FrontPage::where('status', 1)->get();
            $blocks = DB::table('homepage_block_positions')->orderBy('order', 'asc')->get();
            return view('frontendmanage::home_content', compact('home_content', 'pages', 'blocks'));
        } catch (Throwable $th) {
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function HomeContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        try {
            $home_content = HomeContent::find($request->id);
            $home_content->slider_title = $request->slider_title;
            $home_content->slider_text = $request->slider_text;
            $home_content->testimonial_title = $request->testimonial_title;
            $home_content->category_title = $request->category_title;
            $home_content->category_sub_title = $request->category_sub_title;

            if ($request->instructor_banner != null) {

                if ($request->file('instructor_banner')->extension() == "svg") {
                    $file = $request->file('instructor_banner');
                    $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName;
                    $file->move(public_path('uploads/settings'), $fileName);
                } else {
                    $url1 = $this->saveImage($request->instructor_banner);
                }
                $home_content->instructor_banner = $url1;
            }
            if ($request->best_category_banner != null) {

                if ($request->file('best_category_banner')->extension() == "svg") {
                    $file1 = $request->file('best_category_banner');
                    $fileName1 = md5(rand(0, 9999) . '_' . time()) . '.' . $file1->clientExtension();
                    $url2 = 'public/uploads/settings/' . $fileName1;
                    $file1->move(public_path('uploads/settings'), $fileName1);

                } else {
                    $url2 = $this->saveImage($request->best_category_banner);
                }

                $home_content->best_category_banner = $url2;
            }


            $home_content->instructor_title = $request->instructor_title;
            $home_content->instructor_sub_title = $request->instructor_sub_title;
            $home_content->course_title = $request->course_title;
            $home_content->course_sub_title = $request->course_sub_title;

            $home_content->subscription_title = @$request->subscription_title;
            $home_content->subscription_sub_title = @$request->subscription_sub_title;

            $home_content->best_category_title = $request->best_category_title;
            $home_content->best_category_sub_title = $request->best_category_sub_title;
            $home_content->quiz_title = $request->quiz_title;
            $home_content->live_class_title = $request->live_class_title;
            $home_content->testimonial_sub_title = $request->testimonial_sub_title;
            $home_content->article_title = $request->article_title;
            $home_content->article_sub_title = $request->article_sub_title;


            if ($request->subscribe_logo != null) {

                if ($request->file('subscribe_logo')->extension() == "svg") {
                    $file3 = $request->file('subscribe_logo');
                    $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                    $url3 = 'public/uploads/settings/' . $fileName3;
                    $file3->move(public_path('uploads/settings'), $fileName3);

                } else {
                    $url3 = $this->saveImage($request->subscribe_logo);
                }

                $home_content->subscribe_logo = $url3;
            }

            $home_content->subscribe_title = $request->subscribe_title;
            $home_content->subscribe_sub_title = $request->subscribe_sub_title;


            if ($request->become_instructor_logo != null) {

                if ($request->file('become_instructor_logo')->extension() == "svg") {
                    $file4 = $request->file('become_instructor_logo');
                    $fileName4 = md5(rand(0, 9999) . '_' . time()) . '.' . $file4->clientExtension();
                    $url4 = 'public/uploads/settings/' . $fileName4;
                    $file4->move(public_path('uploads/settings'), $fileName4);

                } else {
                    $url4 = $this->saveImage($request->become_instructor_logo);
                }

                $home_content->become_instructor_logo = $url4;
            }
            if ($request->slider_banner != null) {

                if ($request->file('slider_banner')->extension() == "svg") {
                    $file5 = $request->file('slider_banner');
                    $fileName5 = md5(rand(0, 9999) . '_' . time()) . '.' . $file5->clientExtension();
                    $url5 = 'public/uploads/settings/' . $fileName5;
                    $file5->move(public_path('uploads/settings'), $fileName5);

                } else {
                    $url5 = $this->saveImage($request->slider_banner);
                }

                $home_content->slider_banner = $url5;
            }

            $home_content->become_instructor_title = $request->become_instructor_title;
            $home_content->become_instructor_sub_title = $request->become_instructor_sub_title;


            if ($request->key_feature_logo1 != null) {

                if ($request->file('key_feature_logo1')->extension() == "svg") {
                    $file6 = $request->file('key_feature_logo1');
                    $fileName6 = md5(rand(0, 9999) . '_' . time()) . '.' . $file6->clientExtension();
                    $url6 = 'public/uploads/settings/' . $fileName6;
                    $file6->move(public_path('uploads/settings'), $fileName6);

                } else {
                    $url6 = $this->saveImage($request->key_feature_logo1);
                }

                $home_content->key_feature_logo1 = $url6;
            }

            if ($request->key_feature_logo2 != null) {

                if ($request->file('key_feature_logo2')->extension() == "svg") {
                    $file7 = $request->file('key_feature_logo2');
                    $fileName7 = md5(rand(0, 9999) . '_' . time()) . '.' . $file7->clientExtension();
                    $url7 = 'public/uploads/settings/' . $fileName7;
                    $file7->move(public_path('uploads/settings'), $fileName7);

                } else {
                    $url7 = $this->saveImage($request->key_feature_logo2);
                }
                $home_content->key_feature_logo2 = $url7;
            }

            if ($request->key_feature_logo3 != null) {

                if ($request->file('key_feature_logo3')->extension() == "svg") {
                    $file8 = $request->file('key_feature_logo3');
                    $fileName8 = md5(rand(0, 9999) . '_' . time()) . '.' . $file8->clientExtension();
                    $url8 = 'public/uploads/settings/' . $fileName8;
                    $file8->move(public_path('uploads/settings'), $fileName8);

                } else {
                    $url8 = $this->saveImage($request->key_feature_logo3);
                }

                $home_content->key_feature_logo3 = $url8;
            }


            if ($request->banner_logo != null) {

                if ($request->file('banner_logo')->extension() == "svg") {
                    $file11 = $request->file('banner_logo');
                    $fileName11 = md5(rand(0, 9999) . '_' . time()) . '.' . $file11->clientExtension();
                    $url11 = 'public/uploads/settings/' . $fileName11;
                    $file11->move(public_path('uploads/settings'), $fileName11);

                } else {
                    $url11 = $this->saveImage($request->banner_logo);
                }

                $home_content->banner_logo = $url11;
            }

            if ($request->show_menu_search_box == 1) {
                $home_content->show_menu_search_box = 1;
            } else {
                $home_content->show_menu_search_box = 0;
            }

            if ($request->show_subscription_plan == 1) {
                $home_content->show_subscription_plan = 1;
            } else {
                $home_content->show_subscription_plan = 0;
            }

            if ($request->show_banner_search_box == 1) {
                $home_content->show_banner_search_box = 1;
            } else {
                $home_content->show_banner_search_box = 0;
            }


            if ($request->show_key_feature == 1) {
                $home_content->show_key_feature = 1;
            } else {
                $home_content->show_key_feature = 0;
            }

//            -------
            if ($request->show_banner_section == 1) {
                $home_content->show_banner_section = 1;
            } else {
                $home_content->show_banner_section = 0;
            }

            if ($request->show_category_section == 1) {
                $home_content->show_category_section = 1;
            } else {
                $home_content->show_category_section = 0;
            }
            if ($request->show_instructor_section == 1) {
                $home_content->show_instructor_section = 1;
            } else {
                $home_content->show_instructor_section = 0;
            }
            if ($request->show_course_section == 1) {
                $home_content->show_course_section = 1;
            } else {
                $home_content->show_course_section = 0;
            }
            if ($request->show_best_category_section == 1) {
                $home_content->show_best_category_section = 1;
            } else {
                $home_content->show_best_category_section = 0;
            }
            if ($request->show_quiz_section == 1) {
                $home_content->show_quiz_section = 1;
            } else {
                $home_content->show_quiz_section = 0;
            }
            if ($request->show_testimonial_section == 1) {
                $home_content->show_testimonial_section = 1;
            } else {
                $home_content->show_testimonial_section = 0;
            }
            if ($request->show_article_section == 1) {
                $home_content->show_article_section = 1;
            } else {
                $home_content->show_article_section = 0;
            }
            if ($request->show_subscribe_section == 1) {
                $home_content->show_subscribe_section = 1;
            } else {
                $home_content->show_subscribe_section = 0;
            }
            if ($request->show_become_instructor_section == 1) {
                $home_content->show_become_instructor_section = 1;
            } else {
                $home_content->show_become_instructor_section = 0;
            }
            if ($request->show_sponsor_section == 1) {
                $home_content->show_sponsor_section = 1;
            } else {
                $home_content->show_sponsor_section = 0;
            }

            $home_content->key_feature_title1 = $request->key_feature_title1;
            $home_content->key_feature_subtitle1 = $request->key_feature_subtitle1;
            $home_content->key_feature_link1 = $request->key_feature_link1;
            $home_content->key_feature_title2 = $request->key_feature_title2;
            $home_content->key_feature_subtitle2 = $request->key_feature_subtitle2;
            $home_content->key_feature_link2 = $request->key_feature_link2;
            $home_content->key_feature_title3 = $request->key_feature_title3;
            $home_content->key_feature_subtitle3 = $request->key_feature_subtitle3;
            $home_content->key_feature_link3 = $request->key_feature_link3;
            $home_content->show_about_lms_section = $request->show_about_lms_section;
            $home_content->about_lms_header = $request->about_lms_title;
            $home_content->about_lms = $request->about_lms;


            $home_content->save();
            if ($home_content) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('frontend.homeContent');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function PageContent()
    {
        try {
            $page_content = HomeContent::first();
            return view('frontendmanage::page_content', compact('page_content'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());

        }
    }

    public function showTopBarSettings()
    {
        try {
            $data = TopbarSetting::first();
            return view('frontendmanage::topbarSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function showCourseSettings()
    {
        try {
            $data = CourseSetting::first();
            return view('frontendmanage::courseSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function saveCourseSettings(Request $request)
    {
        // return $request;
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            if (isset($request->show_enrolled_or_level_section) && !isset($request->enrolled_or_level)) {
                Toastr::warning(trans('frontendmanage.Required Data Not Selected'), trans('common.Failed'));
                return redirect()->back();
            }
            $data = CourseSetting::first();
            $data->show_rating = $request->show_rating;
            $data->show_cart = $request->show_cart;
            $data->show_enrolled_or_level_section = $request->show_enrolled_or_level_section;
            $data->enrolled_or_level = $request->enrolled_or_level;
            $data->show_cql_left_sidebar = $request->show_cql_left_sidebar;
            $data->size_of_grid = $request->size_of_grid;
            $data->show_mode_of_delivery = $request->show_mode_of_delivery;

            $data->show_review_option = $request->show_review_option;
            $data->show_rating_option = $request->show_rating_option;
            $data->show_search_in_category = $request->show_search_in_category;

            $data->show_instructor_rating = $request->show_instructor_rating;
            $data->show_instructor_review = $request->show_instructor_review;
            $data->show_instructor_enrolled = $request->show_instructor_enrolled;
            $data->show_instructor_courses = $request->show_instructor_courses;
            $data->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('frontendmanage::courseSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function saveTopBarSettings(Request $request)
    {
        // return $request;
        if (demoCheck()) {
            return redirect()->back();

        }
        try {
            $data = TopbarSetting::first();

            $data->left_side_text_show = $request->left_side_text_show;
            $data->left_side_logo = $request->left_side_logo;
            $data->left_side_text = $request->left_side_text;
            $data->left_side_text_link = $request->left_side_text_link;

            $data->right_side_text_1_show = $request->right_side_text_1_show;
            $data->reight_side_logo_1 = $request->reight_side_logo_1;
            $data->right_side_text_1 = $request->right_side_text_1;
            $data->right_side_text_1_link = $request->right_side_text_1_link;

            $data->right_side_text_2_show = $request->right_side_text_2_show;
            $data->reight_side_logo_2 = $request->reight_side_logo_2;
            $data->right_side_text_2 = $request->right_side_text_2;
            $data->right_side_text_2_link = $request->right_side_text_2_link;

            $data->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return view('frontendmanage::topbarSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function ContactPageContent()
    {
        try {
            $page_content = HomeContent::first();
            return view('frontendmanage::contact_page_content', compact('page_content'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function PageContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {
            $home_content = HomeContent::find($request->id);
            $home_content->course_page_title = $request->course_page_title;
            $home_content->course_page_sub_title = $request->course_page_sub_title;
            $home_content->class_page_title = $request->class_page_title;
            $home_content->class_page_sub_title = $request->class_page_sub_title;
            $home_content->quiz_page_title = $request->quiz_page_title;
            $home_content->quiz_page_sub_title = $request->quiz_page_sub_title;
            $home_content->instructor_page_title = $request->instructor_page_title;
            $home_content->instructor_page_sub_title = $request->instructor_page_sub_title;

            $home_content->become_instructor_page_title = $request->become_instructor_page_title;
            $home_content->become_instructor_page_sub_title = $request->become_instructor_page_sub_title;


            $home_content->about_page_title = $request->about_page_title;
            $home_content->about_sub_title = $request->about_sub_title;

            $home_content->blog_page_title = $request->blog_page_title;
            $home_content->blog_page_sub_title = $request->blog_page_sub_title;


            if ($request->blog_page_banner != null) {
                if ($request->file('blog_page_banner')->extension() == "svg") {
                    $file10 = $request->file('blog_page_banner');
                    $fileName10 = md5(rand(0, 9999) . '_' . time()) . '.' . $file10->clientExtension();
                    $url10 = 'public/uploads/settings/' . $fileName10;
                    $file10->move(public_path('uploads/settings'), $fileName10);
                } else {
                    $url10 = $this->saveImage($request->blog_page_banner);
                }
                $home_content->blog_page_banner = $url10;
            }


            if ($request->about_page_banner != null) {
                if ($request->file('about_page_banner')->extension() == "svg") {
                    $file1 = $request->file('about_page_banner');
                    $fileName1 = md5(rand(0, 9999) . '_' . time()) . '.' . $file1->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName1;
                    $file1->move(public_path('uploads/settings'), $fileName1);
                } else {
                    $url1 = $this->saveImage($request->about_page_banner);
                }
                $home_content->about_page_banner = $url1;
            }


            if ($request->instructor_page_banner != null) {
                if ($request->file('instructor_page_banner')->extension() == "svg") {
                    $file3 = $request->file('instructor_page_banner');
                    $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                    $url3 = 'public/uploads/settings/' . $fileName3;
                    $file3->move(public_path('uploads/settings'), $fileName3);
                } else {
                    $url3 = $this->saveImage($request->instructor_page_banner);
                }
                $home_content->instructor_page_banner = $url3;
            }
            if ($request->become_instructor_page_banner != null) {
                if ($request->file('become_instructor_page_banner')->extension() == "svg") {
                    $file8 = $request->file('become_instructor_page_banner');
                    $fileName8 = md5(rand(0, 9999) . '_' . time()) . '.' . $file8->clientExtension();
                    $url8 = 'public/uploads/settings/' . $fileName8;
                    $file8->move(public_path('uploads/settings'), $fileName8);
                } else {
                    $url8 = $this->saveImage($request->become_instructor_page_banner);
                }
                $home_content->become_instructor_page_banner = $url8;
            }

            if ($request->quiz_page_banner != null) {
                if ($request->file('quiz_page_banner')->extension() == "svg") {
                    $file4 = $request->file('quiz_page_banner');
                    $fileName4 = md5(rand(0, 9999) . '_' . time()) . '.' . $file4->clientExtension();
                    $url4 = 'public/uploads/settings/' . $fileName4;
                    $file4->move(public_path('uploads/settings'), $fileName4);
                } else {
                    $url4 = $this->saveImage($request->quiz_page_banner);
                }
                $home_content->quiz_page_banner = $url4;
            }

            if ($request->class_page_banner != null) {
                if ($request->file('class_page_banner')->extension() == "svg") {
                    $file5 = $request->file('class_page_banner');
                    $fileName5 = md5(rand(0, 9999) . '_' . time()) . '.' . $file5->clientExtension();
                    $url5 = 'public/uploads/settings/' . $fileName5;
                    $file5->move(public_path('uploads/settings'), $fileName5);
                } else {
                    $url5 = $this->saveImage($request->class_page_banner);
                }
                $home_content->class_page_banner = $url5;
            }

            if ($request->course_page_banner != null) {
                if ($request->file('course_page_banner')->extension() == "svg") {
                    $file6 = $request->file('course_page_banner');
                    $fileName6 = md5(rand(0, 9999) . '_' . time()) . '.' . $file6->clientExtension();
                    $url6 = 'public/uploads/settings/' . $fileName6;
                    $file6->move(public_path('uploads/settings'), $fileName6);
                } else {
                    $url6 = $this->saveImage($request->course_page_banner);
                }
                $home_content->course_page_banner = $url6;
            }

            if (isModuleActive('Subscription')) {
                $home_content->subscription_page_title = $request->subscription_page_title;
                $home_content->subscription_page_sub_title = $request->subscription_page_sub_title;
                if ($request->subscription_page_banner != null) {
                    if ($request->file('subscription_page_banner')->extension() == "svg") {
                        $file9 = $request->file('subscription_page_banner');
                        $fileName9 = md5(rand(0, 9999) . '_' . time()) . '.' . $file9->clientExtension();
                        $ur9 = 'public/uploads/settings/' . $fileName9;
                        $file9->move(public_path('uploads/settings'), $fileName9);
                    } else {
                        $url9 = $this->saveImage($request->subscription_page_banner);
                    }
                    $home_content->subscription_page_banner = $url9;
                }
            }


            $home_content->save();
            Cache::forget('HomeContentList');
            Cache::forget('frontendContent');
            if ($home_content) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('frontend.pageContent');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }


        } catch (\Exception $e) {
            Cache::forget('frontendContent');
            Cache::forget('HomeContentList');
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }


    public function ContactPageContentUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }


        try {


            $home_content = HomeContent::find($request->id);

            $home_content->contact_page_title = $request->contact_page_title;
            $home_content->contact_sub_title = $request->contact_sub_title;

            if ($request->show_map == 1) {
                $home_content->show_map = 1;
            } else {
                $home_content->show_map = 0;
            }
            if ($request->contact_page_banner != null) {
                if ($request->file('contact_page_banner')->extension() == "svg") {
                    $file2 = $request->file('contact_page_banner');
                    $fileName2 = md5(rand(0, 9999) . '_' . time()) . '.' . $file2->clientExtension();
                    $url2 = 'public/uploads/settings/' . $fileName2;
                    $file2->move(public_path('uploads/settings'), $fileName2);
                } else {
                    $url2 = $this->saveImage($request->contact_page_banner);
                }
                $home_content->contact_page_banner = $url2;
            }

            $home_content->save();
            Cache::forget('HomeContentList');
            Cache::forget('frontendContent');
            if ($home_content) {

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }


        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    // PrivacyPolicy
    public function PrivacyPolicy()
    {
        try {
            $privacy_policy = PrivacyPolicy::find(1);
            return view('frontendmanage::privacy_policy', compact('privacy_policy'));
        } catch (Throwable $th) {
            $errorMessage = $th->getMessage();
            Log::error($errorMessage);
            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function PrivacyPolicyUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;

        try {
            $privacy_policy = PrivacyPolicy::find($request->id);
            $privacy_policy->details = $request->details;
            $privacy_policy->page_banner_title = $request->page_banner_title;
            $privacy_policy->page_banner_sub_title = $request->page_banner_sub_title;
            $privacy_policy->page_banner_status = $request->page_banner_status;


            if ($request->page_banner != null) {

                if ($request->file('page_banner')->extension() == "svg") {
                    $file = $request->file('page_banner');
                    $fileName = md5(rand(0, 9999) . '_' . time()) . '.' . $file->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName;
                    $file->move(public_path('uploads/settings'), $fileName);
                } else {
                    $url1 = $this->saveImage($request->page_banner);
                }
                $privacy_policy->page_banner = $url1;
            }

            $privacy_policy->save();
            if ($privacy_policy) {
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->route('frontend.privacy_policy');
            } else {
                Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
                return redirect()->back();
            }

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function testimonials()
    {
        try {
            $data['testimonials'] = Testimonial::latest()->get();
            return view('frontendmanage::testimonials', compact('data'));
        } catch (Throwable $th) {

            Toastr::error(trans('common.Operation failed'), trans('common.Failed'));
            return redirect()->back();
        }
    }

    public function testimonials_store(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = [
            'body' => 'required',
            'author' => 'required|max:255',
            'profession' => 'required|max:255',
            'image' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));

        try {
            $testimonial = new Testimonial();
            $testimonial->body = $request->body;
            $testimonial->star = $request->star;
            $testimonial->author = $request->author;
            $testimonial->profession = $request->profession;

            $image = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $image = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/testimonial/', $image);
                $image = 'public/uploads/testimonial/' . $image;
                $testimonial->image = $image;
            }

            $testimonial->status = $request->status;
            $testimonial->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'body' => 'required',
            'author' => 'required|max:255',
            'profession' => 'required|max:255',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            $testimonial = Testimonial::find($request->id);
            $testimonial->body = $request->body;
            $testimonial->author = $request->author;
            $testimonial->profession = $request->profession;
            $testimonial->star = $request->star;

            $image = "";
            if ($request->file('image') != "") {
                $file = $request->file('image');
                $image = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
                $file->move('public/uploads/testimonial/', $image);
                $image = 'public/uploads/testimonial/' . $image;
                $testimonial->image = $image;
            }

            $testimonial->status = $request->status;
            $testimonial->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_edit($id)
    {
        try {
            $data['testimonials'] = Testimonial::all();
            $edit = Testimonial::find($id);
            return view('frontendmanage::testimonials', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function testimonials_delete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $testimonial = Testimonial::find($id);
            $testimonial->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.testimonials');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function sectionSetting()
    {
        try {
            $data['frontends'] = FrontendSetting::whereNotIn('id', [1, 2])->latest()->get();
            return view('frontendmanage::sectionSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function sectionSettingEdit($id)
    {
        try {
            $edit = FrontendSetting::find($id);
            $data['frontends'] = FrontendSetting::whereNotIn('id', [1, 2])->latest()->get();

            return view('frontendmanage::sectionSetting', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function sectionSetting_update(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        $rules = [
            'title' => 'required|max:255',
            'description' => 'required|max:255',
        ];

        $this->validate($request, $rules, validationMessage($rules));

        try {
            $frontend = FrontendSetting::find($request->id);
            $frontend->title = $request->title;
            $frontend->description = $request->description;
            $frontend->btn_name = $request->btn_name;
            $frontend->btn_link = $request->btn_link;
            $frontend->url = $request->url;
            if ($request->icon) {
                $frontend->icon = $request->icon;
            }
            $frontend->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->route('frontend.sectionSetting');

        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSetting()
    {
        try {
            $data['social_links'] = SocialLink::latest()->get();
            return view('frontendmanage::socialSetting', compact('data'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSettingEdit($id)
    {
        try {
            $data['social_links'] = SocialLink::latest()->get();
            $edit = SocialLink::find($id);
            return view('frontendmanage::socialSetting', compact('data', 'edit'));
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function socialSettingDelete($id)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {

            $delete = SocialLink::find($id)->delete();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect('frontend/social-setting');
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function AboutPage()
    {
        $about = AboutPage::first();
        return view('frontendmanage::about', compact('about'));
    }

    public function saveAboutPage(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        try {
            $about = AboutPage::firstOrNew(array('id' => 1));

            $about->who_we_are = $request->who_we_are;
            $about->banner_title = $request->banner_title;
            $about->story_title = $request->story_title;
            $about->story_description = $request->story_description;
            $about->teacher_title = $request->teacher_title;
            $about->teacher_details = $request->teacher_details;
            $about->course_title = $request->course_title;
            $about->course_details = $request->course_details;
            $about->student_title = $request->student_title;
            $about->student_details = $request->student_details;

            $about->total_student = $request->total_student;
            $about->total_teacher = $request->total_teacher;
            $about->total_courses = $request->total_courses;

            $about->show_testimonial = $request->show_testimonial;
            $about->show_brand = $request->show_brand;
            $about->show_become_instructor = $request->show_become_instructor;


            if ($request->image1 != null) {

                if ($request->file('image1')->extension() == "svg") {
                    $file1 = $request->file('image1');
                    $fileName1 = md5(rand(0, 9999) . '_' . time()) . '.' . $file1->clientExtension();
                    $url1 = 'public/uploads/settings/' . $fileName1;
                    $file1->move(public_path('uploads/settings'), $fileName1);

                } else {
                    $url1 = $this->saveImage($request->image1);
                }

                $about->image1 = $url1;
            }

            if ($request->image2 != null) {

                if ($request->file('image2')->extension() == "svg") {
                    $file2 = $request->file('image2');
                    $fileName2 = md5(rand(0, 9999) . '_' . time()) . '.' . $file2->clientExtension();
                    $url2 = 'public/uploads/settings/' . $fileName2;
                    $file2->move(public_path('uploads/settings'), $fileName2);

                } else {
                    $url2 = $this->saveImage($request->image2);
                }

                $about->image2 = $url2;
            }


            if ($request->image3 != null) {

                if ($request->file('image3')->extension() == "svg") {
                    $file3 = $request->file('image3');
                    $fileName3 = md5(rand(0, 9999) . '_' . time()) . '.' . $file3->clientExtension();
                    $url3 = 'public/uploads/settings/' . $fileName3;
                    $file3->move(public_path('uploads/settings'), $fileName3);

                } else {
                    $url3 = $this->saveImage($request->image3);
                }

                $about->image3 = $url3;
            }

            if ($request->image4 != null) {

                if ($request->file('image4')->extension() == "svg") {
                    $file4 = $request->file('image4');
                    $fileName4 = md5(rand(0, 9999) . '_' . time()) . '.' . $file4->clientExtension();
                    $url4 = 'public/uploads/settings/' . $fileName4;
                    $file4->move(public_path('uploads/settings'), $fileName4);

                } else {
                    $url4 = $this->saveImage($request->image4);
                }

                $about->image4 = $url4;
            }

            $about->save();
            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function socialSettingSave(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }
        // return $request;

        $rules = [
            'icon' => 'required',
            'name' => 'required',
            'btn_link' => 'required',
            'status' => 'required',
        ];
        $this->validate($request, $rules, validationMessage($rules));


        try {
            $social_link = new SocialLink();
            $social_link->icon = $request->icon;
            $social_link->name = $request->name;
            $social_link->link = $request->btn_link;
            $social_link->status = $request->status;
            $social_link->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect()->back();

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }

    public function socialSettingUpdate(Request $request)
    {
        if (demoCheck()) {
            return redirect()->back();
        }

        $rules = ['id' => 'required',
            'name' => 'required',
            'icon' => 'required',
            'btn_link' => 'required',
            'status' => 'required',
        ];

        $this->validate($request, $rules, validationMessage($rules));


        try {
            $social_link = SocialLink::find($request->id);
            $social_link->icon = $request->icon;
            $social_link->name = $request->name;
            $social_link->link = $request->btn_link;
            $social_link->status = $request->status;
            $social_link->save();

            Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            return redirect('frontend/social-setting');

        } catch (Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function changeHomePageBlockOrder(Request $request)
    {
        $ids = $request->get('ids');

        foreach ($ids as $index => $id) {
            DB::table('homepage_block_positions')->where('id', $id)->limit(1)->update(['order' => $index]);
        }

        Cache::forget('blocks');
        return true;
    }
}
