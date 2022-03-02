<?php

namespace App\Repositories;


use App\User;
use App\Subscription;
use App\Traits\ImageStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Modules\Setting\Model\BusinessSetting;
use Modules\Coupons\Entities\UserWiseCoupon;
use Modules\Newsletter\Entities\NewsletterSetting;
use Modules\Newsletter\Http\Controllers\AcelleController;

class UserRepository implements UserRepositoryInterface
{
    use ImageStore;


    public function create(array $data)
    {

        $user = User::create($data);

        $user->dob = $data['dob'] ?? null;
        $user->gender = $data['gender'] ?? null;
        $user->student_type = $data['student_type'] ?? null;
        $user->job_title = $data['job_title'] ?? null;
        $user->identification_number = $data['identification_number'] ?? null;
        $user->company_id = $data['company_id'] ?? null;

        $user->referral = Str::random(10);
        $user->save();

        if (session::get('referral') != null) {
            $invited_by = User::where('referral', session::get('referral'))->first();
            $user_coupon = new UserWiseCoupon();
            $user_coupon->invite_by = $invited_by->id;
            $user_coupon->invite_accept_by = $user->id;
            $user_coupon->invite_code = session::get('referral');
            $user_coupon->save();
        }

        $mailchimpStatus = env('MailChimp_Status') ?? false;
        $getResponseStatus = env('GET_RESPONSE_STATUS') ?? false;
        $acelleStatus = env('ACELLE_STATUS') ?? false;
        if (hasTable('newsletter_settings')) {
            $setting = NewsletterSetting::first();
            if ($data['role_id'] == 2) {

                if ($setting->instructor_status == 1) {
                    $list = $setting->instructor_list_id;
                    if ($setting->instructor_service == "Mailchimp") {

                        if ($mailchimpStatus) {
                            try {
                                $MailChimp = new MailChimp(env('MailChimp_API'));
                                $MailChimp->post("lists/$list/members", [
                                    'email_address' => $data['email'],
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
                                    'email' => $data['email'],
                                    'campaign' => array('campaignId' => $list),
                                ));
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->instructor_service == "Acelle") {
                        if ($acelleStatus) {

                            try {
                                $email=$data['email'];
                                $make_action_url='/subscribers?list_uid='.$list.'&EMAIL='.$email;
                                $acelleController=new AcelleController();
                                $response=$acelleController->curlPostRequest($make_action_url);
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->instructor_service == "Local") {
                        try {
                            $check = Subscription::where('email', '=', $data['email'])->first();
                            if (empty($check)) {
                                $subscribe = new Subscription();
                                $subscribe->email = $data['email'];
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


            } elseif ($data['role_id'] == 3) {
                if ($setting->student_status == 1) {
                    $list = $setting->student_list_id;
                    if ($setting->student_service == "Mailchimp") {

                        if ($mailchimpStatus) {
                            try {
                                $MailChimp = new MailChimp(env('MailChimp_API'));
                                $MailChimp->post("lists/$list/members", [
                                    'email_address' => $data['email'],
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
                                    'email' => $data['email'],
                                    'campaign' => array('campaignId' => $list),
                                ));
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->instructor_service == "Acelle") {
                        if ($acelleStatus) {

                            try {
                                $email=$data['email'];
                                $make_action_url='/subscribers?list_uid='.$list.'&EMAIL='.$email;
                                $acelleController=new AcelleController();
                                $response=$acelleController->curlPostRequest($make_action_url);
                            } catch (\Exception $e) {

                            }
                        }
                    } elseif ($setting->student_service == "Local") {
                        try {
                            $check = Subscription::where('email', '=', $data['email'])->first();
                            if (empty($check)) {
                                $subscribe = new Subscription();
                                $subscribe->email = $data['email'];
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
        }


        if (Settings('email_verification') != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        } else {
            $user->sendEmailVerificationNotification();
        }

        return $user;
    }

    public function store(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->username = $data['username'];
        $user->role_id = $data['role_id'];
        $user->country = $data['country'];
        if (isset($data['photo'])) {
            $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));
            $user->image = $data['avatar'];
        }
        $user->password = Hash::make($data['password']);
        if (Settings('email_verification') != 1) {
            $user->email_verified_at = date('Y-m-d H:m:s');
            $user->save();
        } else {
            $user->sendEmailVerificationNotification();
        }
        return $user;
    }


    public function update(array $data, $id)
    {
        $user = User::findOrFail($id);
        if (Hash::check($data['password'], Auth::user()->password)) {
            if (isset($data['photo'])) {
                $data = Arr::add($data, 'avatar', $this->saveAvatar($data['photo']));
                $user->image = $data['avatar'];
            }
            $user->name = $data['name'];
            $user->username = $data['username'];
            $user->role_id = $data['role_id'];
            $user->password = Hash::make($data['password']);
            if ($user->save()) {
                $staff = $user->staff;
                $staff->user_id = $user->id;
                $staff->department_id = $data['department_id'];
                $staff->employee_id = $data['employee_id'];
                $staff->showroom_id = $data['showroom_id'];
                // $staff->warehouse_id = $data['warehouse_id'];
                $staff->phone = $data['phone'];
                if ($staff->save()) {
                    if (Settings('email_verification') != 1) {
                        $user->email_verified_at = date('Y-m-d H:m:s');
                        $user->save();
                    } else {
                        $user->sendEmailVerificationNotification();
                    }
                }
                return $user;
            }
        }
    }


}
