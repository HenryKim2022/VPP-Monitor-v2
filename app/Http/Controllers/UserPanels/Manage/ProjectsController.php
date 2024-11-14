<?php

namespace App\Http\Controllers\UserPanels\Manage;

use App\Http\Controllers\Controller;
use App\Models\DaftarTask_Model;
use App\Models\DaftarWS_Model;
use App\Models\Karyawan_Model;
use App\Models\Kustomer_Model;
use App\Models\Monitoring_Model;
use App\Models\Projects_Model;
use App\Models\Team_Model;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Jobs\CheckExpiredWorksheetsJob;



class ProjectsController extends Controller
{
    // protected $breadcrumbService;

    // public function __construct(BreadcrumbService $breadcrumbService)
    // {
    //     $this->breadcrumbService = $breadcrumbService;
    // }

    public function index(Request $request)
    {
        $process = $this->setPageSession("Manage Projects", "m-projects");
        if ($process) {
            $loadDaftarProjectsFromDB = [];
            $loadDaftarProjectsFromDB = Projects_Model::with(['karyawan', 'client', 'team', 'worksheet', 'monitor'])->withoutTrashed()->get();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'daftar_login_4get.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);

            $modalData = [
                'modal_add' => '#add_projectModal',
                'modal_edit' => '#editprojectModal',
                'modal_delete' => '#delete_projectModal',
                'modal_reset' => '#reset_projectModal',
            ];

            $data = [
                'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                'currentRouteName' => Route::currentRouteName(),
                'loadDaftarProjectsFromDB' => $loadDaftarProjectsFromDB,
                'modalData' => $modalData,
                'client_list' => Kustomer_Model::withoutTrashed()->get(),
                'team_list' => Team_Model::withoutTrashed()->get(),
                'co_auth' =>  [$authenticated_user_data->id_karyawan, $authenticated_user_data->na_karyawan],
                'worksheet_list' => DaftarWS_Model::withoutTrashed()->get(),
                'authenticated_user_data' => $authenticated_user_data,
            ];
            return $this->setReturnView('pages/userpanels/pm_daftarproject', $data);
        }
    }



    public function add_project(Request $request)
    {
        //    dd($request->input('start-deadline'));
        // hows the validation for $request->input('start-deadline') ?
        // here is the received data :
        // ```
        // 2024-11-01 to 2024-11-30
        // ```


        $validator = Validator::make(
            $request->all(),
            [
                'project-id' => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique('tb_projects', 'id_project')->ignore($request->input('project-id'), 'id_project')->whereNull('deleted_at')
                ],
                'project-name'  => 'sometimes|required',
                'start-deadline' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/', // Check format
                    function ($attribute, $value, $fail) {
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

                        if ($startDate > $endDate) {
                            return $fail('The start date must be before the end date.');
                        }
                    },
                ],

            ],
            [
                'project-id' => 'The project-id field is required.',
                'project-name' => 'The project-name field is required.',
                'start-deadline.regex' => 'The start-deadline must be in the format YYYY-MM-DD to YYYY-MM-DD.'
            ]
        );

        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Perform the uniqueness check before saving the record
        if (Projects_Model::withTrashed()->where('id_project', $request->input('project-id'))->exists()) {
            $toast_message = ['The project-id has already been taken.'];
            Session::flash('n_errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // dd($request->input('co-id'));

        $inst = new Projects_Model();
        $inst->id_project = $request->input('project-id');
        $inst->na_project = $request->input('project-name');
        $inst->id_client = $request->input('client-id');
        $inst->id_karyawan = $request->input('co-id');
        $inst->id_team = $request->input('team-id');

        $dateRange = $request->input('start-deadline');
        $dates = explode(" to ", $dateRange);
        $inst->start_project = $dates[0];
        $inst->deadline_project = $dates[1];



        Session::flash('success', ['Project added successfully!']);
        $inst->save();
        return redirect()->back();
    }



    public function get_project(Request $request)
    {
        $prjID = $request->input('prjID');
        $prjName = $request->input('prjName');
        $clientID = $request->input('clientID');

        $daftarProjects = Projects_Model::where('id_project', $prjID)->first();
        if ($daftarProjects) {
            if ($daftarProjects->id_client) {
                $daftarProjects->load('client');
            }
            $ourClients = $daftarProjects->client;
            $clientList = [];
            if ($ourClients) {
                $clientList = Kustomer_Model::all()->map(function ($o_client) use ($ourClients) {
                    $selected = ($o_client->id_client == $ourClients->id_client);
                    return [
                        'value' => $o_client->id_client,
                        'text' => $o_client->na_client,
                        'selected' => $selected,
                    ];
                });
            } else {
                $clientList = Kustomer_Model::withoutTrashed()->get()->map(function ($o_client) {
                    return [
                        'value' => $o_client->id_client,
                        'text' => $o_client->na_client,
                        'selected' => false,
                    ];
                });
            }

            $ourTeams = $daftarProjects->team;
            $teamList = [];
            if ($ourTeams) {
                $teamList = Team_Model::all()->map(function ($o_team) use ($ourTeams) {
                    $selected = ($o_team->id_team == $ourTeams->id_team);
                    return [
                        'value' => $o_team->id_team,
                        'text' => $o_team->na_team,
                        'selected' => $selected,
                    ];
                });
            } else {
                $teamList = Team_Model::withoutTrashed()->get()->map(function ($o_team) {
                    return [
                        'value' => $o_team->id_team,
                        'text' => $o_team->na_team,
                        'selected' => false,
                    ];
                });
            }

            // Return queried data as a JSON response
            return response()->json([
                'id_project' => $prjID,
                'na_project' => $daftarProjects->na_project,
                'id_client' => $clientID,
                'clientList' => $clientList,
                'id_karyawan' => $daftarProjects->id_karyawan,
                'na_karyawan' => $daftarProjects->karyawan->na_karyawan,
                'teamList' => $teamList,
                'start_deadline' => Carbon::parse($daftarProjects->start_project)->format('Y-m-d') . ' to ' . Carbon::parse($daftarProjects->deadline_project)->format('Y-m-d'),
            ]);
        } else {
            // Handle the case when the Jabatan_Model with the given projectID is not found
            return response()->json(['error' => 'Project_Model not found'], 404);
        }
    }




    public function edit_project(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'edit-project-id'  => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique('tb_projects', 'id_project')->ignore($request->input('edit-project-id'), 'id_project')
                ],
                'edit-start-deadline' => [
                    'required',
                    'string',
                    'regex:/^\d{4}-\d{2}-\d{2} to \d{4}-\d{2}-\d{2}$/', // Check format
                    function ($attribute, $value, $fail) {
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

                        if ($startDate > $endDate) {
                            return $fail('The start date must be before the end date.');
                        }
                    },
                ],
                'bsvalidationcheckbox1' => 'required',

            ],
            [
                'edit-project-id.required'  => 'The project-id field is required.',
                'bsvalidationcheckbox1.required' => 'The saving agreement field is required.',
                'edit-start-deadline.regex' => 'The start-deadline must be in the format YYYY-MM-DD to YYYY-MM-DD.',
            ]
        );
        if ($validator->fails()) {
            $toast_message = $validator->errors()->all();
            Session::flash('errors', $toast_message);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $prj = Projects_Model::find($request->input('e-project-id'));
        if ($prj) {
            $prj->id_project = $request->input('edit-project-id');
            $prj->na_project = $request->input('edit-project-name');
            $prj->id_client = $request->input('edit-client-id');
            $prj->id_karyawan = $request->input('edit-co-id');
            $prj->id_team = $request->input('edit-team-id');

            $dateRange = $request->input('start-deadline');
            $dates = explode(" to ", $dateRange);
            $prj->start_project = $dates[0];
            $prj->deadline_project = $dates[1];

            $prj->save();

            $user = auth()->user();
            $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
            Session::put('authenticated_user_data', $authenticated_user_data);

            Session::flash('success', ['Project updated successfully!']);
            return Redirect::back();
        } else {
            Session::flash('errors', ['Err[404]: Project update failed!']);
        }

        return redirect()->back();
    }



    // public function delete_project(Request $request)
    // {
    //     $projectID = $request->input('project_id');
    //     $project = Projects_Model::with('client', 'team', 'monitor', 'worksheet', 'task')->where('id_project', $projectID)->first();
    //     if ($project) {
    //         $project->delete();
    //         Session::flash('success', ['Project deletion successful!']);
    //     } else {
    //         Session::flash('errors', ['Err[404]: Project deletion failed!']);
    //     }
    //     return redirect()->back();
    // }

    public function delete_project(Request $request)
    {
        $projectID = $request->input('project_id');
        // Check if the project is linked to any records in 'monitor', 'worksheet', or 'task'
        $hasRelatedRecords = Monitoring_Model::where('id_project', $projectID)->exists() ||
            DaftarWS_Model::where('id_project', $projectID)->exists() ||
            DaftarTask_Model::where('id_project', $projectID)->exists();

        if ($hasRelatedRecords) {
            // Session::flash('n_errors', ['Err[404]: Project deletion failed! Related records exist in monitor, worksheet, or task tables.']);
            Session::flash('n_errors', ['Err[404]: Project deletion failed! Project is ongoing.']);
        } else {
            $project = Projects_Model::find($projectID);
            if ($project) {
                $project->delete();
                Session::flash('success', ['Project deletion successful!']);
            } else {
                Session::flash('n_errors', ['Err[404]: Project not found for deletion!']);
            }
        }
        return redirect()->back();
    }


    public function reset_project(Request $request)
    {
        Projects_Model::query()->delete();
        DB::statement('ALTER TABLE tb_projects AUTO_INCREMENT = 1');

        $user = auth()->user();
        $authenticated_user_data = Karyawan_Model::with('daftar_login.karyawan', 'jabatan.karyawan')->find($user->id_karyawan);
        Session::put('authenticated_user_data', $authenticated_user_data);

        Session::flash('success', ['All project data reset successfully!']);
        return redirect()->back();
    }


    public function get_prjmondws(Request $request)
    {
        $process = $this->setPageSession("Manage Projects", "m-projects");
        if ($process) {
            $projectId = $request->input('projectID');
            if (!$projectId) {
                return back()->with('error', 'Project ID is required.');
            }

            try {
                // $project = Projects_Model::with(['client', 'pcoordinator', 'team', 'monitor', 'task', 'worksheet'])
                //     ->findOrFail($projectId);
                $project = Projects_Model::with(['client', 'pcoordinator', 'team.karyawans', 'monitor', 'task', 'worksheet'])
                    ->where('id_project', $projectId)
                    ->first();


                $project->load('worksheet');
                $ws_status = $project->worksheet->map(function ($worksheet) {
                    return $worksheet->checkAllWSStatus();
                })->contains('OPEN') ? 'OPEN' : 'CLOSED';


                $user = auth()->user();
                $authenticatedUser = Karyawan_Model::with(['daftar_login.karyawan', 'daftar_login_4get.karyawan', 'jabatan.karyawan'])
                    ->findOrFail($user->id_karyawan);

                $loadDataDailyWS = [];
                if ($loadDataDailyWS) {
                    $loadDataDailyWS = DaftarWS_Model::where('id_project', $project->monitor[0]['id_project'])->get();
                }
                // dd($loadDataDailyWS);
                // $clientData = DaftarWS_Model::with('project', 'monitoring')->where('id_project', $project->monitor[0]['id_project'])->first()->getClientData();

                $modalData = [
                    'modal_add_moni' => '#add_moniModal',
                    'modal_edit_moni' => '#edit_moniModal',
                    'modal_delete_moni' => '#delete_moniModal',
                    'modal_reset_moni' => '#reset_moniModal',
                    'modal_add_ws' => '#add_wsModal',
                    'modal_edit_ws' => '#edit_wsModal',
                    'modal_delete_ws' => '#delete_wsModal',
                    'modal_reset_ws' => '#reset_wsModal',
                    'modal_lock' => '#lock_wsModal',
                    'modal_unlock' => '#unlock_wsModal'
                ];


                $data = [
                    'breadcrumbs' => $this->getBreadcrumb($request->route()->getName()),
                    'currentRouteName' => Route::currentRouteName(),
                    'loadDaftarMonDWSFromDB' => $project, // Use the Eloquent model directly
                    'project' => $project,
                    'authenticated_user_data' => $authenticatedUser,
                    'loadDataDailyWS' => $loadDataDailyWS,
                    'wsStatus' => $ws_status,
                    'modalData' => $modalData,
                    // 'clientData' => $clientData,
                ];

                return view('pages.userpanels.pm_mondws', $data);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return Session::flash('errors', ['Err[404]: Project not found!']);
            }
        }
    }
}
