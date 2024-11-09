<?php

namespace App\Http\Controllers\UserPanels\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan_Model;
use App\Models\Projects_Model;
use App\Models\DaftarDWS_Model;
use App\Models\DaftarTask_Model;
use App\Models\Monitoring_Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use App\Jobs\CheckExpiredWorksheetsJob;



class MonitoringController extends Controller
{
    //
    public function index(Request $request)
    {
        $process = $this->setPageSession("Manage Project Monitoring", "m-prj/m-monitoring-worksheet/mondws");
        if ($process) {
            $loadDaftarMonDWSFromDB = [];
            $loadDaftarMonDWSFromDB = Projects_Model::with('client', 'team', 'dailyws')->find($request->input('projectID'));
            // $loadDaftarMonDWSFromDB = Projects_Model::with('client', 'team', 'monitor', 'dailyws')->find($request->input('projectID'));



            // // Fetch projects with their relationships and children
            // $rootProjects = Monitoring_Model::with(['karyawan', 'project', 'dailyws', 'children'])
            // ->whereNull('id_monitoring_parent') // Get only root projects
            // ->withoutTrashed()
            // ->get();



            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'daftar_login_4get.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);

            // // $modalData = [
            // //     'modal_add' => '#add_projectModal',
            // //     'modal_edit' => '#editprojectModal',
            // //     'modal_delete' => '#delete_projectModal',
            // //     'modal_reset' => '#reset_projectModal',
            // // ];

            $data = [
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                'currentRouteName' => Route::currentRouteName(),
                'loadDaftarMonDWSFromDB' => $loadDaftarMonDWSFromDB,
                // 'modalData' => $modalData,
                // 'client_list' => Kustomer_Model::withoutTrashed()->get(),
                // 'team_list' => Team_Model::withoutTrashed()->get(),
                // 'dailyws_list' => DaftarDWS_Model::withoutTrashed()->get(),

                'authenticated_user_data' => $authenticated_user_data,
                // '$dwsRecords' => $$dwsRecords,
            ];
            return $this->setReturnView('pages/userpanels/pm_mondws', $data);
        }
    }


    public function add_mon(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mon-category'      => 'required',
                'mon-qty'           => 'required',
                'prj-start_date'    => 'required',
                'prj-deadline_date'  => 'required|after_or_equal:prj-start_date',
                'mon-start-end-date' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/', // Check format
                    function ($attribute, $value, $fail) use ($request) {
                        $dates = explode(" to ", $value);
                        if (count($dates) !== 2) {
                            return $fail('The ' . $attribute . ' must contain two valid dates.');
                        }

                        // Validate the dates
                        $startDate = $dates[0];
                        $endDate = $dates[1];

                        if (!strtotime($startDate) || !strtotime($endDate)) {
                            return $fail('The dates must be valid dates.');
                        }

                        // Compare with project start and deadline dates
                        $prjStartDate = $request->input('prj-start_date');
                        $prjDeadlineDate = $request->input('prj-deadline_date');

                        if ($startDate < $prjStartDate) {
                            return $fail('The start date mustn\'t be before the project starting date.');
                        }

                        if ($endDate > $prjDeadlineDate) {
                            return $fail('The end date mustn\'t be after the project deadline date.');
                        }

                        if ($startDate > $endDate) {
                            return $fail('The start date must be before the end date.');
                        }
                    },
                ],
                'mon-id_karyawan'   => 'required',
                'mon-id_project'    => 'required'
            ],
            [
                'mon-category'      => 'The category field is required.',
                'mon-qty'           => 'The qty field is required.',
                'prj-start_date'    => 'The project start_date field  isn\'t filled by system!',
                'prj-deadline_date'      => 'The project deadline_date field isn\'t filled by system!',
                'mon-start-end-date.regex' => 'The start-end date must be in the format YYYY-MM-DD to YYYY-MM-DD.',
                'mon-id_karyawan'   => 'The id_karyawan field is not filled by system!',
                'mon-id_project'    => 'The id_project field is not filled by system!',
            ]
        );
        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }



        $id_project = $request->input('mon-id_project');
        $currentQty = $request->input('mon-qty');

        // Step 1: Query to sum progress_current_task
        $totalQty = Monitoring_Model::where('id_project', $id_project)
            ->whereNull('deleted_at') // Exclude soft-deleted records
            ->sum('qty'); // Sum the progress of the filtered tasks

        // Step 2: Add the current task's progress to the total
        $totalQty += $currentQty;

        // Step 3: Check if the total progress exceeds 100
        if ($totalQty > 100) {
            $errorMessage = 'Err[400]: Sorry, you can\'t add a quantity with an exceeded progress number!';
            Session::flash('n_errors', [$errorMessage]);
            return redirect()->back();
        }





        $monitor = new Monitoring_Model();
        $monitor->category = $request->input('mon-category');

        $dateRange = $request->input('mon-start-end-date');
        $dates = explode(" to ", $dateRange);
        $monitor->start_date = $dates[0];
        $monitor->end_date = $dates[1];

        // $monitor->start_date = $request->input('mon-start_date');
        // $monitor->end_date = $request->input('mon-end_date');
        $monitor->qty = $request->input('mon-qty');
        $monitor->id_karyawan = $request->input('mon-id_karyawan');
        $monitor->id_project = $request->input('mon-id_project');
        $monitor->save();

        $user = auth()->user();
        $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
        Session::put('authenticated_user_data', $authenticated_user_data);

        Session::flash('success', ['Monitoring added successfully!']);
        return redirect()->back();
    }



    public function get_mon(Request $request)
    {
        $monitorID = $request->input('monitorID');
        $projectID = $request->input('projectID');
        $karyawanID = $request->input('karyawanID');

        $daftarMonitor = Monitoring_Model::where('id_monitoring', $monitorID)->first();
        if ($daftarMonitor) {
            return response()->json([    // Return queried data as a JSON response
                'id_mon'        => $daftarMonitor->id_monitoring,
                'cat_mon'       => $daftarMonitor->category,
                'start_dmon'    => $daftarMonitor->start_date,
                'end_dmon'      => $daftarMonitor->end_date,
                'qty_mon'       => $daftarMonitor->qty,
                'id_karyawan'   => $daftarMonitor->id_karyawan,
                'id_project'    => $daftarMonitor->id_project
            ]);
        } else {    // Handle the case when the Jabatan_Model with the given jabatanID is not found
            return response()->json(['error' => 'Monitoring_Model not found'], 404);
        }
    }





    public function edit_mon(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'edit-mon_id'  => 'required',
                'edit-mon_category'  => 'required',
                'edit-mon_qty'  => 'required',
                // 'edit-mon_start_date'  => 'required',
                // 'edit-mon_end_date'  => 'required',
                'edit-prj_start_date'    => 'required',
                'edit-prj_deadline_date'  => 'required|after_or_equal:edit-prj_start_date',
                'edit-mon_start_end_date' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/', // Check format
                    function ($attribute, $value, $fail) use ($request) {
                        $dates = explode(" to ", $value);
                        if (count($dates) !== 2) {
                            return $fail('The ' . $attribute . ' must contain two valid dates.');
                        }

                        // Validate the dates
                        $startDate = $dates[0];
                        $endDate = $dates[1];

                        if (!strtotime($startDate) || !strtotime($endDate)) {
                            return $fail('The dates must be valid dates.');
                        }

                        // Compare with project start and deadline dates
                        $prjStartDate = $request->input('edit-prj_start_date');
                        $prjDeadlineDate = $request->input('edit-prj_deadline_date');

                        if ($startDate < $prjStartDate) {
                            return $fail('The start date mustn\'t be before the project starting date.');
                        }

                        if ($endDate > $prjDeadlineDate) {
                            return $fail('The end date mustn\'t be after the project deadline date.');
                        }

                        if ($startDate > $endDate) {
                            return $fail('The start date must be before the end date.');
                        }
                    },
                ],
                'edit-mon_project_id'  => 'required',
                'edit-mon_karyawan_id'  => 'required',
                'bsvalidationcheckbox1'  => 'required',
            ],
            [
                'edit-mon_id'  => 'The monitor_id field is required.',
                'edit-mon_category'  => 'The category field is required.',
                'edit-mon_qty'  => 'The qty field is required.',
                // 'edit-mon_start_date'  => 'The start_date field is required.',
                // 'edit-mon_end_date'  => 'The end_date field is required.',
                'edit-prj_start_date'    => 'The project start_date field  isn\'t filled by system!',
                'edit-prj_deadline_date'      => 'The project deadline_date field isn\'t filled by system!',
                'edit-mon_start_end_date.regex' => 'The start-end date must be in the format YYYY-MM-DD to YYYY-MM-DD.',
                'edit-mon_project_id' => 'The id_project field is not filled by system!',
                'edit-mon_karyawan_id' => 'The id_karyawann field is not filled by system!',
                'bsvalidationcheckbox1'  => 'The saving agreement field is required.',
            ]
        );
        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }


        // protected $fillable = ['category', 'start_date', 'end_date', 'achieve_date', 'qty', 'id_karyawan','id_project'];

        $mon = Monitoring_Model::find($request->input('edit-mon_id'));
        if ($mon) {
            $mon->category = $request->input('edit-mon_category');
            $mon->qty = $request->input('edit-mon_qty');

            $dateRange = $request->input('edit-mon_start_end_date');
            $dates = explode(" to ", $dateRange);
            $mon->start_date = $dates[0];
            $mon->end_date = $dates[1];

            $mon->id_project = $request->input('edit-mon_project_id');
            $mon->id_karyawan = $request->input('edit-mon_karyawan_id');
            $mon->save();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Monitoring updated successfully!']);
            return Redirect::back();
        } else {
            Session::flash('errors', ['Err[404]: Monitoring update failed!']);
        }
    }


    public function delete_mon(Request $request)
    {
        $monitorID = $request->input('del-mon_id');

        $monitor = Monitoring_Model::with('karyawan')->where('id_monitoring', $monitorID)->first();
        $category = $monitor->category ?: null;
        if ($monitor) {
            // Check if id_monitoring is used in DaftarTask_Model
            $isUsedInDaftarTask = DaftarTask_Model::where('id_monitoring', $monitorID)->whereNull('deleted_at')->exists();

            if ($isUsedInDaftarTask) {
                Session::flash('n_errors', ['Error: This monitoring record cannot be deleted because it is still referenced in one or more tasks.']);
                return redirect()->back();
            }

            // Proceed to delete the monitoring record
            $monitor->delete();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Success: The monitoring record with category *' . $category . '* has been successfully deleted.']);
        } else {
            Session::flash('n_errors', ['Error: Monitoring record not found or has already been deleted.']);
        }

        return redirect()->back();
    }


    public function reset_mon(Request $request)
    {
        Monitoring_Model::query()->delete();
        DB::statement('ALTER TABLE tb_monitoring AUTO_INCREMENT = 1');

        $user = auth()->user();
        $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
        Session::put('authenticated_user_data', $authenticated_user_data);

        Session::flash('success', ['All monitoring data reset successfully!']);
        return redirect()->back();
    }
}
