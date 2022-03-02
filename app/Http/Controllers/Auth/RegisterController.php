<?php

namespace App\Http\Controllers\Auth;

use App\StudentCustomField;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Foundation\Auth\RegistersUsers;
use Modules\FrontendManage\Entities\LoginPage;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (env('nocaptcha_for_reg')) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'g-recaptcha-response' => 'required|captcha'
            ];
        } else {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'phone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:1|unique:users',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];
        }

        return Validator::make($data, $rules,
            validationMessage($rules)
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        if (isset($data['type']) && $data['type'] == "Instructor") {
            $role = 2;
        } else {
            $role = 3;
        }

        if (empty($data['phone'])) {
            $data['phone'] = null;
        }
        return $this->userRepository->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'role_id' => $role,
            'password' => Hash::make($data['password']),
            'language_id' => Settings('language_id') ?? '19',
            'language_name' => Settings('language_name') ?? 'English',
            'language_code' => Settings('language_code') ?? 'en',
            'language_rtl' => Settings('language_rtl') ?? '0',
            'country' => Settings('country_id'),
            'username' => null,
        ]);
    }

    public function RegisterForm()
    {
        abort_if(!Settings('student_reg'), 404);
        $page = LoginPage::first();
        $custom_field = StudentCustomField::first();
        return view(theme('auth.register'), compact('page', 'custom_field'));
    }

    public function showRegistrationForm()
    {
        $page = LoginPage::first();
        return view(theme('auth.register'), compact('page'));
    }


}
