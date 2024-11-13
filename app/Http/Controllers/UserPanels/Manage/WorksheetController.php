<?php

namespace App\Http\Controllers\UserPanels\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DaftarLogin_Model;
use App\Models\DaftarTask_Model;
use App\Models\Karyawan_Model;
use App\Models\DaftarWS_Model;
use App\Models\Monitoring_Model;
use App\Models\Projects_Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Jobs\CheckExpiredWorksheetsJob;



class WorksheetController extends Controller
{
    //
    public function index(Request $request)
    {
        // dd(DaftarTask_Model::with('monitor')->withoutTrashed()->get());
        // dd(DaftarTask_Model::withoutTrashed()->get());

        $projectID = $request->input('projectID');
        $wsID = $request->input('wsID');
        $wsDate = Carbon::parse($request->input('wsDate'));

        $process = $this->setPageSession("Manage Daily Worksheet", "m-worksheet");
        if ($process) {
            // $loadDataWS = DaftarWS_Model::with([
            //     'project',
            //     'project.client',
            //     'monitoring',
            //     // 'task' => function ($query) use ($wsDate) {
            //     //     $query->whereDate('created_at', $wsDate)
            //     //     ->with('monitor');
            //     // }
            //     // ,
            //     'task' => function ($query) use ($projectID) {
            //         $query->where('id_project', $projectID);
            //     }
            //     // ,
            //     // 'task.monitor' => function ($query) use ($projectID) {
            //     //     $query->where('id_project', $projectID);
            //     // }
            // ])->where('id_ws', $wsID)
            //     ->whereDate('working_date_ws', $wsDate)
            //     ->first();

            // dd($loadDataWS->toArray());


            $loadDataWS = DaftarWS_Model::with([
                'project',
                'project.client',
                // 'task' => function ($query) use ($wsDate) {
                //     $query->whereDate('created_at', $wsDate)
                //     ->with('monitor');
                // }
                // ,
                'task' => function ($query) use ($projectID) {
                    $query->where('id_project', $projectID);
                }
                // ,
                // 'task.monitor' => function ($query) use ($projectID) {
                //     $query->where('id_project', $projectID);
                // }
            ])->where('id_ws', $wsID)
                ->whereDate('working_date_ws', $wsDate)
                ->first();
            // Get monitoring records for the tasks of the worksheet
            $loadDataWS->task->flatMap(function ($task) {
                return $task->monitor; // Assuming monitor returns a single Monitoring_Model instance
            });

            // // Dump the result
            // dd($loadDataWS->toArray());




            // $loadRelatedDailyWS = DaftarWS_Model::with('karyawan', 'project', 'monitoring')->where('id_project', $projectID)->first();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);

            $modalData = ['modal_edit' => '#edit_taskModal', 'modal_delete' => '#delete_taskModal', 'modal_reset' => '#reset_taskModal'];
            if ($loadDataWS->status_ws === 'OPEN') {
                $modalData['modal_add'] = '#add_taskModal';
            }

            $projectId = $request->input('projectID');
            $project = Projects_Model::with(['client', 'pcoordinator', 'team', 'monitor', 'task', 'worksheet'])
                ->where('id_project', $projectId)
                ->first();



            $data = [
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                'currentRouteName' => Route::currentRouteName(),
                // 'loadDaftarWorksheetFromDB' => $loadDaftarWorksheetFromDB,
                'modalData' => $modalData,
                'loadDataWS' => $loadDataWS,
                // 'loadRelatedDailyWS' => $loadRelatedDailyWS,
                'employee_list' => Karyawan_Model::withoutTrashed()->get(),
                'taskCategoryList' => Monitoring_Model::where('id_project', $projectID)->withoutTrashed()->get(),
                'prjmondws' => $project,
                'authenticated_user_data' => $authenticated_user_data,
            ];
            return $this->setReturnView('pages/userpanels/pm_daftartaskws', $data);
        }
    }


    public function add_ws(Request $request)
    {
        // dd(Carbon::parse($request->input('ws-working_date')));
        $validator = Validator::make(
            $request->all(),
            [
                'ws-working_date'   => ['required', 'date'],
                'ws-arrival_time'   => 'required',
                'ws-finish_time'    => 'required|after_or_equal:ws-arrival_time',
                'ws-id_karyawan'    => 'required',
                'ws-id_project'     => 'required'
            ],
            [
                'ws-working_date.required'  => 'The working_date field is required.',
                'ws-working_date.date'      => 'The working_date must be a valid date.',
                'ws-arrival_time.required'  => 'The arrival_time field is required.',
                'ws-finish_time.required'   => 'The finish_time must be equal or not lower than arrival_time!',
                'ws-id_karyawan.required'   => 'The id_karyawan field is not filled by system!',
                'ws-id_project.required'    => 'The id_project field is not filled by system!',
            ]
        );

        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if the worksheet already exists
        $existingWorksheets = DaftarWS_Model::where('working_date_ws', $request->input('ws-working_date'))
            ->where('id_karyawan', $request->input('ws-id_karyawan'))
            ->where('id_project', $request->input('ws-id_project'))
            ->get();

        if ($existingWorksheets->isNotEmpty()) {
            $hasOpenStatus = false;
            foreach ($existingWorksheets as $worksheet) {
                if ($worksheet->status_ws === 'OPEN') {
                    if ($worksheet->expired_at_ws) {
                        if (now()->isBefore($worksheet->expired_at_ws)) {
                            // If there's an OPEN status and it is not expired, show message and return
                            Session::flash('n_errors', ['The worksheet for this employee already exists and is OPEN.']);
                            return redirect()->back();
                        } else {
                            // If expired, allow to insert duplicate data & set the old to locked
                            // how to do lock at the old data which expired here?
                            // If expired, lock the old data
                            $worksheet->status_ws = 'LOCKED'; // Change the status to LOCKED
                            $worksheet->save(); // Save the changes to the database
                            $hasOpenStatus = true;
                        }
                    } else {
                        Session::flash('n_errors', ['The worksheet for this employee already exists and is OPEN.']);
                        return redirect()->back();
                    }
                }
            }

            // If there are OPEN worksheets but they are expired, insert duplicate
            if ($hasOpenStatus) {
                $this->insertDuplicateWorksheet($request);
                Session::flash('success', ['Duplicated worksheet added successfully!']);
                return redirect()->back();
            }
        } else {
            // If no existing worksheet, insert the new data
            $this->insertNewWorksheet($request);
            Session::flash('success', ['New worksheet added successfully!']);
            return redirect()->back();
        }

        // Handle case for CLOSED status
        foreach ($existingWorksheets as $worksheet) {
            if ($worksheet->status_ws === 'CLOSED') {
                if (now()->isAfter($worksheet->expired_at_ws)) {
                    // If expired, insert the duplicate data
                    $this->insertDuplicateWorksheet($request);
                    Session::flash('success', ['Duplicated worksheet added successfully!']);
                    return redirect()->back();
                }
            }
        }

        // If we reach here, it means no action was taken
        Session::flash('n_errors', ['Failed to insert duplicate worksheet.']);
        return redirect()->back();
    }

    private function insertDuplicateWorksheet($request)
    {
        $worksheet = new DaftarWS_Model();
        $worksheet->working_date_ws = Carbon::parse($request->input('ws-working_date'));
        $worksheet->arrival_time_ws = $request->input('ws-arrival_time');
        $worksheet->finish_time_ws = $request->input('ws-finish_time');
        $worksheet->status_ws = "OPEN";
        $worksheet->expired_at_ws = Carbon::tomorrow()->setTime(12, 1);
        $worksheet->id_karyawan = $request->input('ws-id_karyawan');
        $worksheet->id_project = $request->input('ws-id_project');
        $worksheet->save();
    }

    private function insertNewWorksheet($request)
    {
        $worksheet = new DaftarWS_Model();
        $worksheet->working_date_ws = Carbon::parse($request->input('ws-working_date'));
        $worksheet->arrival_time_ws = $request->input('ws-arrival_time');
        $worksheet->finish_time_ws = $request->input('ws-finish_time');
        $worksheet->status_ws = "OPEN";
        $worksheet->expired_at_ws = Carbon::tomorrow()->setTime(12, 1);
        $worksheet->id_karyawan = $request->input('ws-id_karyawan');
        $worksheet->id_project = $request->input('ws-id_project');
        $worksheet->save();
    }


    public function get_ws(Request $request)
    {
        $wsID = $request->input('wsID');
        $projectID = $request->input('projectID');
        $karyawanID = $request->input('karyawanID');

        $daftarWorksheet = DaftarWS_Model::where('id_ws', $wsID)->first();
        if ($daftarWorksheet) {
            return response()->json([    // Return queried data as a JSON response
                'id_ws'         => $daftarWorksheet->id_ws,
                'work_date'     => $daftarWorksheet->working_date_ws,
                'arrival_time'  => $daftarWorksheet->arrival_time_ws,
                'finish_time'   => $daftarWorksheet->finish_time_ws,
                'id_karyawan'   => $daftarWorksheet->id_karyawan,
                'id_project'    => $daftarWorksheet->id_project
            ]);
        } else {    // Handle the case when the Jabatan_Model with the given jabatanID is not found
            return response()->json(['error' => 'Worksheet_Model not found'], 404);
        }
    }





    public function edit_ws(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'edit-ws_id'  => 'required',
                'edit-ws_working_date'   => ['required', 'date'],
                'edit-ws_arrival_time'  => 'required',
                'edit-ws_finish_time'  => 'required|after_or_equal:edit-ws_arrival_time',
                'edit-ws_project_id'  => 'required',
                'edit-ws_id_karyawan'  => 'required',
                'bsvalidationcheckbox1'  => 'required',
            ],
            [
                'edit-ws_id.required'  => 'The ws_id field is required.',
                'edit-ws_working_date.required'  => 'The working_date field is required.',
                'edit-ws_arrival_time.required'  => 'The arrival_time field is required.',
                'edit-ws_finish_time.required'   => 'The finish_time must be equal or not lower than arrival_time!',
                'edit-ws_project_id.required' => 'The id_project field is not filled by system!',
                'edit-ws_id_karyawan.required' => 'The id_karyawan field is not filled by system!',
                'bsvalidationcheckbox1.required'  => 'The saving agreement field is required.',
            ]
        );

        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Retrieve the worksheet to check its expiration
        $ws = DaftarWS_Model::find($request->input('edit-ws_id'));

        if ($ws) {
            // Check if the current date and time is greater than the expired_at_ws
            if (now()->isAfter($ws->expired_at_ws)) {
                Session::flash('n_errors', ['Error: The worksheet has expired and cannot be edited. Please add new worksheet, then donot forget to lock your worksheet!']);
                return redirect()->back();
            }

            // Check if the working_date related to id_karyawan already exists and exclude soft-deleted records
            $existingWorksheet = DaftarWS_Model::where('working_date_ws', $request->input('edit-ws_working_date'))
                ->where('id_karyawan', $request->input('edit-ws_id_karyawan'))
                ->where('id_project', $request->input('edit-ws_project_id'))
                ->whereNull('deleted_at') // Exclude soft-deleted records
                ->exists();

            if ($existingWorksheet) {
                $work_date = Carbon::parse($request->input('edit-ws_working_date'))->isoFormat("ddd, DD MMM YYYY");
                Session::flash('n_errors', ['The worksheet data for this employee already exists at ' . $work_date . '.']);
                return redirect()->back();
            }

            // Proceed to update the worksheet
            $ws->working_date_ws = $request->input('edit-ws_working_date');
            $ws->arrival_time_ws = $request->input('edit-ws_arrival_time');
            $ws->finish_time_ws = $request->input('edit-ws_finish_time');
            $ws->id_project = $request->input('edit-ws_project_id');
            $ws->id_karyawan = $request->input('edit-ws_id_karyawan');
            $ws->save();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'daftar_login_4get.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);


            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Worksheet updated successfully!']);
            return Redirect::back();
        } else {
            Session::flash('errors', ['Err[404]: Worksheet update failed!']);
        }
    }




    // public function edit_ws(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'edit-ws_id'  => 'required',
    //             'edit-ws_working_date'   => ['required', 'date'],
    //             'edit-ws_arrival_time'  => 'required',
    //             'edit-ws_finish_time'  => 'required|after_or_equal:edit-ws_arrival_time',
    //             'edit-ws_project_id'  => 'required',
    //             'edit-ws_id_karyawan'  => 'required',
    //             'bsvalidationcheckbox1'  => 'required',
    //         ],
    //         [
    //             'edit-ws_id.required'  => 'The ws_id field is required.',
    //             'edit-ws_working_date.required'  => 'The working_date field is required.',
    //             'edit-ws_arrival_time.required'  => 'The arrival_time field is required.',
    //             'edit-ws_finish_time.required'   => 'The finish_time must be equal or not lower than arrival_time!',
    //             'edit-ws_project_id.required' => 'The id_project field is not filled by system!',
    //             'edit-ws_id_karyawan.required' => 'The id_karyawan field is not filled by system!',
    //             'bsvalidationcheckbox1.required'  => 'The saving agreement field is required.',
    //         ]
    //     );
    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }


    //     // Add funct to check *compare current date time with  DaftarWS_Model -> expired_at_ws is it expired or not, if *NOT expired* continue to saving edited data, if expired show error
    //     // Check if the working_date related to id_karyawan already exists and exclude soft-deleted records
    //     $existingWorksheet = DaftarWS_Model::where('working_date_ws', $request->input('edit-ws_working_date'))
    //         ->where('id_karyawan', $request->input('edit-ws_id_karyawan'))
    //         ->where('id_project', $request->input('edit-ws_project_id'))
    //         ->whereNull('deleted_at') // Exclude soft-deleted records
    //         ->exists();

    //     if ($existingWorksheet) {
    //         $work_date = Carbon::parse($request->input('edit-ws_working_date'))->isoFormat("ddd, DD MMM YYYY");
    //         Session::flash('n_errors', ['The worksheet data for this employee already exists at ' . $work_date . '.']);
    //         return redirect()->back();
    //     }



    //     $ws = DaftarWS_Model::find($request->input('edit-ws_id'));
    //     if ($ws) {
    //         $ws->working_date_ws = $request->input('edit-ws_working_date');
    //         $ws->arrival_time_ws = $request->input('edit-ws_arrival_time');
    //         $ws->finish_time_ws = $request->input('edit-ws_finish_time');
    //         $ws->id_project = $request->input('edit-ws_project_id');
    //         $ws->id_karyawan = $request->input('edit-ws_id_karyawan');
    //         $ws->save();

    //         $user = auth()->user();
    //         $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //         Session::put('authenticated_user_data', $authenticated_user_data);

    //         Session::flash('success', ['Worksheet updated successfully!']);
    //         return Redirect::back();
    //     } else {
    //         Session::flash('errors', ['Err[404]: Worksheet update failed!']);
    //     }
    // }


    // public function lock_ws(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'lock-ws_id'            => 'required'
    //         ],
    //         [
    //             'lock-ws_id.required'   => 'The ws_id field is not filled by system!'
    //         ]
    //     );
    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     $ws = DaftarWS_Model::find($request->input('lock-ws_id'));
    //     if ($ws) {
    //         if (auth()->user()->type == 'Superuser') {
    //             $ws->status_ws = 'CLOSED';
    //             $ws->expired_at_ws = null;
    //             $ws->save();

    //             $user = auth()->user();
    //             $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //             Session::put('authenticated_user_data', $authenticated_user_data);

    //             Session::flash('success', ['Worksheet locked successfully!']);
    //         } else {
    //             // check is theres tasks (DaftarTask_Model) related with worksheet (DaftarWS_Model) by id_ws ?

    //             // Check if there are related tasks
    //             $relatedTasks = DaftarTask_Model::where('id_ws', $ws->id)->get();
    //             dd($relatedTasks);
    //             if ($relatedTasks->isNotEmpty()) {
    //                 // If no related tasks, allow locking
    //                 $ws->status_ws = 'CLOSED';
    //                 $ws->expired_at_ws = null;
    //                 $ws->save();

    //                 Session::flash('success', ['Worksheet locked successfully!']);
    //             } else {
    //                 // If there are no related tasks, prevent locking
    //                 Session::flash('n_errors', ['Can\'t lock the worksheet because there\'s no tasks in this worksheet!']);
    //             }
    //         }


    //         return Redirect::back();
    //     } else {
    //         Session::flash('n_errors', ['Err[404]: Failed to lock worksheet!']);
    //     }
    // }



    public function lock_ws(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'lock-ws_id' => 'required'
            ],
            [
                'lock-ws_id.required' => 'The ws_id field is not filled by system!'
            ]
        );

        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ws = DaftarWS_Model::find($request->input('lock-ws_id'));
        if ($ws) {
            if (auth()->user()->type == 'Superuser') {
                // Allow superuser to lock the worksheet
                $ws->status_ws = 'CLOSED';
                $ws->expired_at_ws = null;
                $ws->closed_at_ws = Carbon::now();
                $ws->save();

                $user = auth()->user();
                $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
                Session::put('authenticated_user_data', $authenticated_user_data);

                Session::flash('success', ['Worksheet locked successfully!']);
            } else {
                // Create an instance of DaftarTask_Model
                $taskModel = new DaftarTask_Model();

                // Check if there are related tasks
                if ($taskModel->isRelatedTaskEmpty($ws->id)) {
                    // If there are no related tasks, allow locking
                    $ws->status_ws = 'CLOSED';
                    $ws->expired_at_ws = null;
                    $ws->closed_at_ws = Carbon::now();
                    $ws->save();

                    Session::flash('success', ['Worksheet locked successfully!']);
                } else {
                    // If there are related tasks, prevent locking
                    Session::flash('n_errors', ['Can\'t lock the worksheet because there are tasks in this worksheet!']);
                }
            }

            return Redirect::back();
        } else {
            Session::flash('n_errors', ['Err[404]: Failed to lock worksheet!']);
        }
    }



    public function unlock_ws(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'unlock-ws_id'            => 'required'
            ],
            [
                'unlock-ws_id.required'   => 'The ws_id field is not filled by system!'
            ]
        );
        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ws = DaftarWS_Model::find($request->input('unlock-ws_id'));
        if ($ws) {
            $ws->status_ws = 'OPEN';
            $ws->expired_at_ws = Carbon::tomorrow()->setTime(12, 1);
            $ws->closed_at_ws = null;
            $ws->save();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Worksheet open successfully!']);
            return Redirect::back();
        } else {
            Session::flash('errors', ['Err[404]: Failed to open worksheet!']);
        }
    }




    // public function su_unlock_ws(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'unlock-ws_id'            => 'required'
    //         ],
    //         [
    //             'unlock-ws_id.required'   => 'The ws_id field is not filled by system!'
    //         ]
    //     );
    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // Check worksheet ws_status
    //     $isWorksheetLocked = DaftarWS_Model::where('id_ws', $request->input('lock-ws_id'))
    //         ->where('status_ws', "CLOSED")
    //         ->exists();
    //     $ws = DaftarWS_Model::find($request->input('unlock-ws_id'));
    //     if ($ws) {
    //         // Allow superuser to lock the worksheet
    //         if ($isWorksheetLocked) {
    //             $ws->status_ws = 'OPEN';
    //             $ws->expired_at_ws = Carbon::tomorrow()->setTime(12, 1);
    //             $ws->closed_at_ws = null;
    //         } else {
    //             $ws->status_ws = 'CLOSED';
    //             $ws->expired_at_ws = null;
    //             $ws->closed_at_ws = Carbon::now();
    //         }
    //         $ws->save();

    //         $user = auth()->user();
    //         $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //         Session::put('authenticated_user_data', $authenticated_user_data);

    //         Session::flash('success', ['Worksheet open successfully!']);
    //         return Redirect::back();
    //     } else {
    //         Session::flash('errors', ['Err[404]: Failed to open worksheet!']);
    //     }
    // }

    // public function su_lock_ws(Request $request)
    // {
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'lock-ws_id' => 'required'
    //         ],
    //         [
    //             'lock-ws_id.required' => 'The ws_id field is not filled by system!'
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         $toast_message = $validator->errors()->all();
    //         Session::flash('errors', $toast_message);
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }


    //     // Check worksheet ws_status
    //     $isWorksheetLocked = DaftarWS_Model::where('id_ws', $request->input('lock-ws_id'))
    //         ->where('status_ws', "CLOSED")
    //         ->exists();
    //     $ws = DaftarWS_Model::find($request->input('lock-ws_id'));
    //     if ($ws) {
    //         if (auth()->user()->type == 'Superuser') {
    //             // Allow superuser to lock the worksheet
    //             if ($isWorksheetLocked) {
    //                 $ws->status_ws = 'OPEN';
    //                 $ws->expired_at_ws = Carbon::tomorrow()->setTime(12, 1);
    //                 $ws->closed_at_ws = null;
    //             } else {
    //                 $ws->status_ws = 'CLOSED';
    //                 $ws->expired_at_ws = null;
    //                 $ws->closed_at_ws = Carbon::now();
    //             }
    //             $ws->save();

    //             $user = auth()->user();
    //             $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
    //             Session::put('authenticated_user_data', $authenticated_user_data);

    //             Session::flash('success', ['Worksheet locked successfully!']);
    //         }
    //         return Redirect::back();
    //     } else {
    //         Session::flash('n_errors', ['Err[404]: Failed to lock worksheet!']);
    //     }
    // }


    // public function edit_mark_ws(Request $request)
    // {
    //     // $message = [
    //     //     'err_json' => [],
    //     //     'success_json' => [],
    //     // ];

    //     // $validator = Validator::make(
    //     //     $request->all(),
    //     //     [
    //     //         'ws_id_value' => 'required'
    //     //     ],
    //     //     [
    //     //         'ws_id_value.required' => 'The ws_id is not filled by system!'
    //     //     ]
    //     // );

    //     // if ($validator->fails()) {

    //     //     $toast_message = $validator->errors()->all();
    //     //     // Session::flash('errors', $toast_message);
    //     //     // return redirect()->back()->withErrors($validator)->withInput();
    //     //      $message['err_json'] = $toast_message;
    //     //     return response()->json(['message' => $message], 422);
    //     // }

    // }




    public function edit_mark_ws(Request $request)
    {
           // Validate the incoming request
    $request->validate([
        'id_ws' => 'required|integer|exists:tb_worksheet,id_ws',
        'remarkText' => 'required|string|max:5000', // Adjust max length as needed
    ]);

    // Get the worksheet ID from the request
    $wsID = $request->input('id_ws');
    $worksheet = DaftarWS_Model::find($wsID);
        if (!$worksheet) {
            $message['err_json'][] = 'Worksheet not found';
            return response()->json(['message' => $message], 404);
        }

        // Update the remark and save it
        $worksheet->remark_ws = $request->input('remarkText');
        $worksheet->save(); // Don't forget to save the changes

        // Prepare success message
        $message['success_json'][] = "*" . $worksheet->working_date_ws . ' worksheet remarks for ' . '*' . $worksheet->id_project . ' is updated!';

        return response()->json(['message' => $message], 200);
    }



    public function delete_ws(Request $request)
    {
        $wsID = $request->input('del_ws_id');
        $worksheet = DaftarWS_Model::with('karyawan', 'project')->where('id_ws', $wsID)->first();

        if ($worksheet) {
            // Check if id_ws is used in DaftarTask_Model
            $isUsedInDaftarTask = DaftarTask_Model::where('id_ws', $wsID)->whereNull('deleted_at')->exists();

            if ($isUsedInDaftarTask) {
                Session::flash('n_errors', ['Error: This worksheet cannot be deleted because it is still referenced in one or more tasks.']);
                return redirect()->back();
            }

            // Store the working_date_ws before deleting the worksheet
            $worksheetDate = $worksheet->working_date_ws;
            $worksheet->delete();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Success: Worksheet deletion with date *' . $worksheetDate . '* was successful!']);
        } else {
            $errorMessage = 'Error: Worksheet deletion failed!';

            if ($worksheet && $worksheet->working_date_ws) {
                $errorMessage = 'Error: Worksheet deletion with date *' . $worksheet->working_date_ws . '* failed!';
            }

            Session::flash('n_errors', [$errorMessage]);
        }

        return redirect()->back();
    }

    public function reset_ws(Request $request)
    {
        DaftarWS_Model::query()->delete();
        DB::statement('ALTER TABLE tb_worksheet AUTO_INCREMENT = 1');

        $user = auth()->user();
        $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
        Session::put('authenticated_user_data', $authenticated_user_data);

        Session::flash('success', ['All worksheet data reset successfully!']);
        return redirect()->back();
    }
}
