<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CourseSetting\Entities\Course;
use Modules\CourseSetting\Entities\CourseComment;
use Modules\CourseSetting\Entities\CourseCommentReply;
use Modules\CourseSetting\Entities\CourseReveiw;
use Modules\CourseSetting\Entities\Notification;
use Modules\SystemSetting\Entities\GeneralSettings;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('maintenanceMode');
    }


    public function saveComment(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'comment' => 'required',
        ]);


        try {
            $course = Course::where('id', $request->course_id)->first();

            if (isset($course)) {
                $settings = GeneralSettings::first();
                $comment = new CourseComment();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $request->course_id;
                $comment->instructor_id = $course->user_id;
                $comment->comment = $request->comment;
                $comment->status = 1;
                $comment->save();

                $notification = new Notification();
                $notification->author_id = Auth::user()->id;
                $notification->user_id = $course->user_id;
                $notification->course_id = $course->id;
                $notification->course_comment_id = $comment->id;
                $notification->save();


                send_email($course->user, 'Course_comment', [
                    'time' => Carbon::now()->format('d-M-Y, s:i A'),
                    'course' => $course->title,
                    'comment' => $comment->comment,
                ]);

                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error('Invalid Action !', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }

    public function submitCommnetReply(Request $request)
    {

        $request->validate([
            'comment_id' => 'required',
            'reply' => 'required'
        ]);
        try {
            $comment = CourseComment::find($request->comment_id);
            $course = $comment->course;


            if (isset($course)) {
                $settings = GeneralSettings::first();

                $comment = new CourseCommentReply();
                $comment->user_id = Auth::user()->id;
                $comment->course_id = $course->id;
                if (!empty($request->reply_id)) {
                    $comment->reply_id = $request->reply_id;
                } else {
                    $comment->reply_id = null;
                }
                $comment->comment_id = $request->comment_id;
                $comment->reply = $request->reply;
                $comment->status = 1;
                $comment->save();


                send_email($course->user, 'Course_comment_Reply', [
                    'time' => Carbon::now()->format('d-M-Y ,s:i A'),
                    'course' => $course->title,
                    'comment' => $comment->comment,
                    'reply' => $comment->reply,
                ]);


                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
                return redirect()->back();
            } else {
                Toastr::error('Invalid Action !', 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }
    }


    public function deleteComment($id)
    {
        try {
            $comment = CourseComment::find($id);
            $user = Auth::user();
            if ($comment->user_id == $user->id || $user->role_id == 1 || $comment->instructor_id == $user->id) {
                $comment->delete();
                if (isset($comment->replies)) {
                    foreach ($comment->replies as $replay) {
                        $replay->delete();
                    }
                }
                return true;
            } else {
                return false;
            }


        } catch (\Exception $exception) {
            return false;

        }
    }

    public function deleteReview($id)
    {
        try {
            $review = CourseReveiw::findOrFail($id);
            $user = Auth::user();
            if ($review->user_id == $user->id || $user->role_id == 1 || $review->instructor_id == $user->id) {
                $review->delete();
                return true;
//                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            } else {
                return false;
//                Toastr::error('Invalid access', trans('common.Failed'));
            }


        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deleteCommnetReply($id)
    {
        try {

            $reply = CourseCommentReply::find($id);
            $course = Course::find($reply->course_id);
            $user = Auth::user();

            if ($reply->user_id == $user->id || $user->role_id == 1 || $course->user_id == $user->id) {
                $reply->delete();
                Toastr::success(trans('common.Operation successful'), trans('common.Success'));
            } else {
                Toastr::error('Invalid access', trans('common.Failed'));
            }
            return redirect()->back();
        } catch (\Exception $e) {
            GettingError($e->getMessage(), url()->current(), request()->ip(), request()->userAgent());
        }

    }
}
