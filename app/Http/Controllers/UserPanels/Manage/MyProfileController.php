<?php

namespace App\Http\Controllers\UserPanels\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\TheApp_Model;
use App\Models\Karyawan_Model;
use App\Models\Absen_Model;
use App\Models\DaftarLogin_Model;
use App\Models\Kustomer_Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use App\Jobs\CheckExpiredWorksheetsJob;



class MyProfileController extends Controller
{
    //
    public function index(Request $request)
    {
        $process = $this->setPageSession("MyProfile", "my-profile");
        if ($process) {
            $idKaryawan = $this->getCurrentUserID();

            // $user = auth()->user();
            // $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->idKaryawan);
            // if (!$authenticated_user_data){
            //     $authenticated_user_data = Session::get('authenticated_user_data');
            // }
            // // dd($authenticated_user_data->toArray());

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'daftar_login_4get.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);

            if (!$authenticated_user_data) {
                $authenticated_user_data = Kustomer_Model::with('daftar_login.client', 'daftar_login_4get.client')->find($user->id_client);
                if ($authenticated_user_data == null){
                    return redirect()->back(); // Redirect to the previous page or handle the case when authenticated_user_data is not available
                }
            }


            $data = [
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                'currentRouteName' => Route::currentRouteName(),
                'loadDataKaryawanFromDB' => DaftarLogin_Model::with(['karyawan'])->where('id_karyawan', $idKaryawan)->get(),
                // 'site_name' => TheApp_Model::where('na_setting', 'CompanyName')->withoutTrashed()->first(),
                // 'site_year' => TheApp_Model::where('na_setting', 'SiteCopyrightYear')->withoutTrashed()->first(),
                // 'aboutus_data' => TheApp_Model::where('na_setting', 'AboutUSText')->withoutTrashed()->first(),
                // 'company_addr' => TheApp_Model::where('na_setting', 'CompanyAddress')->withoutTrashed()->first(),
                // 'company_phone' => TheApp_Model::where('na_setting', 'CompanyPhone')->withoutTrashed()->first(),
                // 'company_email' => TheApp_Model::where('na_setting', 'CompanyEmail')->withoutTrashed()->first(),
                'authenticated_user_data' => $authenticated_user_data,
                'loadDataUserFromDB' => $this->profile_load_accdata($idKaryawan),
            ];
            Session::put('loadDataUserFromDB', $this->profile_load_accdata($idKaryawan));

            return $this->setReturnView('pages/userpanels/pm_myprofile', $data);
        }
    }

    public function profile_load_accdata($idKaryawan)
    {
        // dd(DaftarLogin_Model::with(['karyawan'])->where('id_karyawan', $idKaryawan)->get());
        return DaftarLogin_Model::with(['karyawan'])->where('id_karyawan', $idKaryawan)->get();
    }


    public function getCurrentUserID()
    {
        $user = auth()->user();
        $idKaryawan = null;
        if ($user) {
            $karyawan = Karyawan_Model::where('id_karyawan', $user->id_karyawan)->first();
            if ($karyawan) {
                $idKaryawan = $karyawan->id_karyawan;
            }
        }
        return $idKaryawan;
    }

    public function profile_load_biodata(Request $request)
    {
        $process = $this->setPageSession("Dashboard", "my-profile/load-biodata");
        if ($process) {
            $idKaryawan = $this->getCurrentUserID();
            $data = [
                'loadDataKaryawanFromDB' => DaftarLogin_Model::with(['karyawan'])->where('id_karyawan', $idKaryawan)->get(),
            ];
            return $this->setReturnView('pages/userpanels/p_dashboard', $data);
        }
    }



    public function profile_edit_avatar(Request $request)
    {
        $karyawan = Karyawan_Model::where('id_karyawan', $request->input('id_karyawan'))->first();
        if ($karyawan) {
            if ($request->hasFile('foto_karyawan')) {
                $file = $request->file('foto_karyawan');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                // Store the uploaded file in the storage/app/public directory
                // $karyawan->foto_karyawan = asset('public/avatar/uploads/' . $filename);
                // Storage::putFileAs('public/avatar/uploads', $file, $filename);
                $file->move(public_path('avatar/uploads'), $filename);
                $karyawan->foto_karyawan = $filename;
                $karyawan->save();

                $authenticated_user_data = Karyawan_Model::find($karyawan->user_id);      // Re-auth after saving
                $user = auth()->user();
                $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
                Session::put('authenticated_user_data', $authenticated_user_data);



                Session::flash('success', ['User image updated successfully']);
            } else {
                $karyawan->save();
                Session::flash('errors', ['User image update failed']);
            }
        } else {
            // Handle the case when the user is not found
            Session::flash('errors', ['Err[404]: User not found']);
        }
        return response()->json(['reload' => true]);
    }



    public function profile_edit_biodata(Request $request)
    {

        // dd($request->input('birth-date'));

        $validator = Validator::make(
            $request->all(),
            [
                'account-name'  => 'sometimes|required|min:3',
                'birth-loc'  => 'sometimes|required',
                'birth-date'  => 'sometimes|required',
                // 'religion'  => 'sometimes|required',
                'address'  => 'sometimes|required',
                'notelp'  => 'sometimes|required',
            ],
            [
                'account-name.required' => 'The account-name field is required.',
                'birth-loc.required' => 'The birth-loc field is required.',
                'birth-date.required' => 'The birth-date field is required.',
                // 'religion.required' => 'The religion field is required.',
                'address.required'  => 'The address field is required.',
                'notelp.required' => 'The notelp field is required.',
            ]
        );
        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $karyawan = Karyawan_Model::where('id_karyawan', $request->input('id_karyawan'))->first();
        if ($karyawan) {
            $karyawan->na_karyawan = $request->input('account-name');
            $karyawan->tlah_karyawan = $request->input('birth-loc');
            $karyawan->tglah_karyawan = $request->input('birth-date');
            $karyawan->agama_karyawan = $request->input('religion');
            $karyawan->alamat_karyawan = $request->input('address');
            $karyawan->notelp_karyawan = $request->input('notelp');
            $karyawan->save();

            $updatedUser = Karyawan_Model::find($karyawan->id_karyawan);
            Session::put('authenticated_user_data', $updatedUser);

            Session::flash('success', ['Your account data was updated!']);
        } else {
            Session::flash('errors', ['Err[404]: Failed to update user account!']);
        }
        return redirect()->back();
    }



    public function profile_edit_accdata(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username'  => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique('tb_daftar_login', 'username')->ignore($request->input('user_id'), 'user_id')
                ],
                'email'     => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('tb_daftar_login', 'email')->ignore($request->input('user_id'), 'user_id')
                ],
                // 'new-password'          => 'required|min:6',
                // 'confirm-new-password'  => 'required|same:new-password',
            ],
            [
                'username.required'  => 'The username field is required.',
                'email.required' => 'The email field is required.',
                // 'new-password.required' => 'The new-password field is required.',
                // 'confirm-new-password.required' => 'The password-confirmation field is required.',
            ]
        );

        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $daftarLogin = DaftarLogin_Model::find($request->input('user_id'));
        if ($daftarLogin) {
            $daftarLogin->username = $request->input('username');
            $daftarLogin->email = $request->input('email');

            if ($request->input('new-password')){
                $daftarLogin->password = bcrypt($request->input('new-password'));
            }

            $daftarLogin->type = $request->input('type');
            $daftarLogin->save();

            $authenticated_user_data = DaftarLogin_Model::find($daftarLogin->user_id);      // Re-auth after saving
            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Your account data was updated!']);
        } else {
            Session::flash('errors', ['Err[404]: Failed to update user account!']);
        }

        return redirect()->back();
    }
}
