<?php

namespace App\Http\Controllers\UserPanels\Manage;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use App\Jobs\CheckExpiredWorksheetsJob;


class ClientUserLoginController extends Controller
{
    //
    public function index(Request $request)
    {
        $process = $this->setPageSession("Manage Client Users", "m-user");
        if ($process) {
            $loadDaftarLoginClientFromDB = [];
            $loadDaftarLoginClientFromDB = DaftarLogin_Model::with(['client'])
                ->where(function ($query) {
                    $query->where('Type', 1);
                })
                ->whereNotNull('id_client')
                ->withoutTrashed()
                ->get();

            // dd($loadDaftarLoginClientFromDB);

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);

            $modalData = [
                'modal_add' => '#add_userModal',
                'modal_edit' => '#edit_userModal',
                'modal_delete' => '#delete_userModal',
                'modal_reset' => '#reset_userModal',
            ];

            $data = [
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                'currentRouteName' => Route::currentRouteName(),
                // 'site_name' => TheApp_Model::where('na_setting', 'CompanyName')->withoutTrashed()->first(),
                // 'site_year' => TheApp_Model::where('na_setting', 'SiteCopyrightYear')->withoutTrashed()->first(),
                // 'aboutus_data' => TheApp_Model::where('na_setting', 'AboutUSText')->withoutTrashed()->first(),
                // 'company_addr' => TheApp_Model::where('na_setting', 'CompanyAddress')->withoutTrashed()->first(),
                // 'company_phone' => TheApp_Model::where('na_setting', 'CompanyPhone')->withoutTrashed()->first(),
                // 'company_email' => TheApp_Model::where('na_setting', 'CompanyEmail')->withoutTrashed()->first(),
                'loadDaftarLoginClientFromDB' => $loadDaftarLoginClientFromDB,
                'modalData' => $modalData,
                'employee_list' => Kustomer_Model::withoutTrashed()->get(),
                'authenticated_user_data' => $authenticated_user_data,
            ];
            return $this->setReturnView('pages/userpanels/pm_daftarlogin_client', $data);
        }
    }



    // public function get_user(Request $request)
    // {
    //     $userID = $request->input('userID');
    //     $daftarLogin = DaftarLogin_Model::with('client')->where('user_id', $userID)->first();

    //     if ($daftarLogin) {
    //         $client = $daftarLogin->client;
    //         // Load the select input for Mark & Category (this loading is different from load_select_list_for_addmodal())
    //         $clientList = DaftarLogin_Model::all()->map(function ($user) use ($client) {
    //             $selected = ($user->id_karyawan == $client->id_client);
    //             return [
    //                 'value' => $user->client->id_client,
    //                 'text' => $user->client->na_client,
    //                 'selected' => $selected,
    //             ];
    //         });

    //         // $userTypeList = DaftarLogin_Model::all()->map(function ($user) use ($karyawan) {
    //         //     $selected = ($user->id_karyawan == $karyawan->id_karyawan);
    //         //     return [
    //         //         'value' => $user->convertUserTypeBack($user->type),
    //         //         'text' => $user->type,
    //         //         'selected' => $selected,
    //         //     ];
    //         // });

    //         // Return queried data as a JSON response
    //         return response()->json([
    //             'user_id' => $userID,
    //             'na_karyawan' => $client->na_client,
    //             'username' => $daftarLogin->username,
    //             'email' => $daftarLogin->email,
    //             'id_karyawan' => $client->id_client,
    //             'clientList' => $clientList,
    //             'userTypeList' => $daftarLogin->type,
    //         ]);
    //     } else {
    //         // Handle the case when the user with the given user_id is not found
    //         return response()->json(['error' => 'User not found'], 404);
    //     }
    // }

    // public function add_user(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'modalAddEmployee'  => 'sometimes|required',
    //             'modalAddUsername' => [
    //                 'sometimes',
    //                 'required',
    //                 'string',
    //                 Rule::unique('tb_daftar_login', 'username')->ignore($request->input('modalAddUsername'), 'username')->whereNull('deleted_at')
    //             ],
    //             'modalAddEmail' => [
    //                 'sometimes',
    //                 'required',
    //                 'email',
    //                 Rule::unique('tb_daftar_login', 'email')->ignore($request->input('modalAddEmail'), 'email')->whereNull('deleted_at')
    //             ],
    //             'modalAddUserType'  => 'sometimes|required|min:1',
    //             'modalAddPassword' => 'sometimes|required'
    //         ],
    //         [
    //             'modalAddEmployee' => 'The employee field is required.',
    //             'modalAddUsername' => 'The username field is required.',
    //             'modalAddEmail' => 'The email field is required.',
    //             'modalAddUserType' => 'The user-type field is required.',
    //             'modalAddPassword'  => 'The password field is required.'
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // Perform the uniqueness check before saving the record
    //     if (DaftarLogin_Model::withTrashed()->where('username', $request->input('modalAddUsername'))->exists()) {
    //         $toast_message = ['The username has already been taken.'];
    //         Session::flash('n_errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     if (DaftarLogin_Model::where('email', $request->input('modalAddEmail'))->exists()) {
    //         $toast_message = ['The email has already been taken.'];
    //         Session::flash('n_errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $inst = new DaftarLogin_Model();
    //     $inst->username = $request->input('modalAddUsername');
    //     $inst->email = $request->input('modalAddEmail');
    //     $inst->password = $request->input('modalAddPassword');
    //     $inst->type = $request->input('modalAddUserType');
    //     $inst->id_karyawan = $request->input('modalAddEmployee');

    //     Session::flash('success', ['User added successfully!']);
    //     $inst->save();
    //     return redirect()->back();
    // }



    // public function edit_user(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'modalEditEmployee'  => 'sometimes|required',
    //             'modalEditUsername'  => [
    //                 'sometimes',
    //                 'required',
    //                 'string',
    //                 Rule::unique('tb_daftar_login', 'username')->ignore($request->input('modalEditUsername'), 'username')
    //             ],

    //             'modalEditEmail'  => [
    //                 'sometimes',
    //                 'required',
    //                 'email',
    //                 Rule::unique('tb_daftar_login', 'email')->ignore($request->input('modalEditEmail'), 'email')
    //             ],
    //             'modalEditUserType'  => 'sometimes|required|min:1',
    //             'bsvalidationcheckbox1'  => 'required',
    //         ],
    //         [
    //             'modalEditEmployee' => 'The employee field is required.',
    //             'modalEditUsername' => 'The username field is required.',
    //             'modalEditEmail' => 'The email field is required.',
    //             'modalEditUserType' => 'The user-type field is required.',
    //             'bsvalidationcheckbox1'  => 'The saving agreement field is required.',
    //         ]
    //     );
    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $user = DaftarLogin_Model::find($request->input('user_id'));
    //     if ($user) {
    //         $user->id_karyawan = $request->input('modalEditEmployee');
    //         $user->username = $request->input('modalEditUsername');
    //         $user->email = $request->input('modalEditEmail');
    //         $user->type = $request->input('modalEditUserType');
    //         if ($request->input('modalEditPassword')) {
    //             $user->password = bcrypt($request->input('modalEditPassword'));
    //         }
    //         $user->save();

    //         $user = auth()->user();
    //         $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //         Session::put('authenticated_user_data', $authenticated_user_data);

    //         Session::flash('success', ['User updated successfully!']);
    //         return Redirect::back();
    //     } else {
    //         Session::flash('errors', ['Err[404]: User update failed!']);
    //     }
    // }



    // public function delete_user(Request $request)
    // {
    //     $userToDeleteID = $request->input('user_id');
    //     $authenticated_user_data = Session::get('authenticated_user_data');
    //     $current_user_rel_karyawan = $authenticated_user_data->id_karyawan;

    //     if ($userToDeleteID != $current_user_rel_karyawan) {
    //         $inst = DaftarLogin_Model::find($userToDeleteID);
    //         if ($inst) {
    //             $inst->delete();

    //             $user = auth()->user();
    //             $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //             Session::put('authenticated_user_data', $authenticated_user_data);

    //             Session::flash('success', ['User deletion successful!']);
    //         } else {
    //             Session::flash('n_errors', ['Err[404]: User deletion failed!']);
    //         }
    //     } else {
    //         Session::flash('n_errors', ['Err[500]: You cant delete ur own user account!']);
    //     }

    //     return redirect()->back();
    // }



    // public function reset_user(Request $request)
    // {
    //     $user = auth()->user();
    //     $karyawanID = $user->id_karyawan;

    //     // Soft delete all DaftarLogin_Model records except the one associated with the authenticated user's karyawanID
    //     DaftarLogin_Model::where('id_karyawan', '!=', $karyawanID)->delete();

    //     // Reset the auto-increment value of the table
    //     DB::statement('ALTER TABLE tb_daftar_login AUTO_INCREMENT = 1');

    //     $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($karyawanID);
    //     Session::put('authenticated_user_data', $authenticated_user_data);

    //     Session::flash('success', ['All users data (excluding me) reset successfully!']);
    //     return redirect()->back();
    // }
}
