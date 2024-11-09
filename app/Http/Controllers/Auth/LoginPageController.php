<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DaftarLogin_Model;
use App\Models\Karyawan_Model;
use App\Models\Kustomer_Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;


class LoginPageController extends Controller
{
    //
    public function index()
    {
        $process = $this->setPageSession("Login", "login");
        if ($process) {
            $data = [
                'site_name' => env(key: 'APP_NAME'),
                'quote' => $this->getQuote(),
            ];
            return $this->setReturnView('pages/auths/p_login', $data);
        }
    }

    public function showLogin()
    {
        $process = $this->setPageSession("Login", "login");
        if ($process) {
            $data = [
                'site_name' => env(key: 'APP_NAME'),
                'quote' => $this->getQuote(),
            ];
            return $this->setReturnView('pages/auths/p_login', $data);
        }
    }


    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        $validator->after(function ($validator) use ($request) {        // Custom Validation: Check username/email exist
            $usernameEmail = $request->input('username-email');
            $password = $request->input('login-password');
            $user = DaftarLogin_Model::where(function ($query) use ($usernameEmail) {
                $query->where('username', $usernameEmail)
                    ->orWhere('email', $usernameEmail);
            })->first();

            if ($usernameEmail && $password) {
                if (!$user) {
                    $validator->errors()->add('username-email', 'The username or email is not registered.');
                } elseif (!Hash::check($password, $user->password)) {
                    $validator->errors()->add('login-password', 'The password is incorrect.');
                }
            } else if ($usernameEmail) {
                $validator->errors()->add('password-email', 'The password is required.');
            } else if ($password) {
                $validator->errors()->add('username-email', 'The username or email is required.');
            } else {
                $validator->errors()->add('username-email', 'The username or email is required.');
                $validator->errors()->add('password-email', 'The password is required.');
            }
        });
        $validator->validate();

        // Get Field Value
        $credentials = $request->only('username-email', 'login-password');
        $usernameEmail = $credentials['username-email'];
        $password = $credentials['login-password'];
        $rememberMe = $request->boolean('remember-me');

        // Attempt Authentication
        $authenticated = Auth::attempt(['username' => $usernameEmail, 'password' => $password, 'deleted_at' => null], $rememberMe)
            || Auth::attempt(['email' => $usernameEmail, 'password' => $password, 'deleted_at' => null], $rememberMe);

        if ($authenticated) {
            // Authentication successful
            $request->session()->regenerate();
            Session::flash('success', ['Welcome back :)']);

            // $user = auth()->user();
            // $authenticated_user_data = Karyawan_Model::with(['daftar_login.karyawan', 'daftar_login_4get.karyawan' => function ($query) {
            //     $query->orderBy('created_at', 'desc')->withoutTrashed()->take(1);
            // }, 'jabatan.karyawan'])
            //     ->find($user->id_karyawan);

            // if ($authenticated_user_data == null){
            //     $authenticated_user_data = Kustomer_Model::with(
            //         [
            //             'daftar_login_4get.client' => function ($query) {
            //                 $query->orderBy('created_at', 'desc')->withoutTrashed()->take(1);
            //             }
            //         ]
            //     )
            //         ->find($user->id_client);
            // }







            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with(['daftar_login.karyawan', 'daftar_login_4get.karyawan' => function ($query) {
                $query->orderBy('created_at', 'desc')->withoutTrashed()->take(1);
            }, 'jabatan.karyawan'])
                ->find($user->id_karyawan);

            if ($authenticated_user_data == null) {
                $authenticated_user_data = Kustomer_Model::with(
                    [
                        'daftar_login_4get.client' => function ($query) {
                            $query->orderBy('created_at', 'desc')->withoutTrashed()->take(1);
                        }
                    ]
                )->find($user->id_client);
            }

            // Access the image URL using the getImageAttribute method
            $imageUrl = $authenticated_user_data->image; // This will call the getImageAttribute method

            // Optionally store the image URL in the session
            Session::put('user_image', $imageUrl);









            if ($rememberMe) {  // keep the session for 1 years (adjust the time as needed)
                Session::put('authenticated_user_data', $authenticated_user_data);
                Session::save();
                config(['session.lifetime' => 125600]); // 1 years in minutes
            } else {
                Session::put('authenticated_user_data', $authenticated_user_data);
            }

            // Session::put('authenticated_user_data', $authenticated_user_data);
            if ($user->type === "admin") {
                return redirect()->route('userPanels.dashboard'); // Redirect to admin dashboard
            } elseif ($user->type === "karyawan") {
                return redirect()->route('userPanels.dashboard'); // Redirect to karyawan dashboard
            } else {
                return redirect()->route('login.page'); // Redirect to login page
            }
        } else {
            // Authentication failed
            Session::flash('errors', ['Invalid credentials.']);
            return redirect()->back();
        }
    }

    public function doLogoutUPanel(Request $request)
    {
        $process = $this->setPageSession("Login Page", "login");
        if ($process) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            Session::flash('success', ['Logged Out :)']);
            return Redirect::to('/login');
        }
    }
}
