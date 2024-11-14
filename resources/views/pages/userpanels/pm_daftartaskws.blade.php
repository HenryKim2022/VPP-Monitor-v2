@php
    $page = Session::get('page');
    $page_title = $page['page_title'];
    $cust_date_format = 'ddd, DD MMM YYYY';
    $cust_time_format = 'hh:mm:ss A';
    $reset_btn = false;
    // $authenticated_user_data = Session::get('authenticated_user_data');
@endphp

@extends('layouts.userpanels.v_main')

@section('header_page_cssjs')
    <style>
        .media .mr-25.p-1.rounded {
            background-color: #30334e;
            border: 1px solid rgba(20, 21, 33, 0.175);
        }

        .dark-layout .media.mr-25.p-1.rounded {
            background-color: #ffffff;
            border: 1px solid rgba(20, 21, 33, 0.175);
        }

        .light-layout .media.mr-25.p-1.rounded {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(20, 21, 33, 0.175);
        }
    </style>

    <style>
        /* Custom CSS for Engineer Text */
        .engineer-text {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: inherit;
            /* padding: 10px; */
            border: none;
            z-index: 2;
        }
    </style>
    <style>
        /* Custom CSS for setting max-width on the image */
        @media (max-width: 924) {
            .max-width-lg {
                max-width: 24%;
            }
        }

        @media (max-width: 800px) {
            .max-width-sm {
                max-width: 12%;
            }
        }

        @media (max-width: 768px) {
            .max-width-md {
                max-width: 12%;
            }
        }
    </style>
@endsection


@section('page-content')
    {{-- @if (auth()->user()->type == 'Superuser' || auth()->user()->type == 'Supervisor')
        <h1>HI MIN :)</h1>
    @endif

    @if (auth()->user()->type == 'Engineer')
        <h1>HI WAN :)</h1>
    @endif --}}

    {{-- {{ dd($authenticated_user_data) }} --}}



    <div class="row match-height">
        <!-- QRCodeCheck-out Card -->
        <div class="col-lg-4 col-md-6 col-12">
        </div>
        <!--/ QRCodeCheck-out Card -->


        {{-- <!--  Check $data as array -->
        <div class="col-xl-12 col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    <pre style="color: white">{{ print_r($loadDataWS->toArray(), true) }}</pre>
                    <br>
                </div>
            </div>
        </div> --}}

        @php
            $authUserId = $authenticated_user_data->id_karyawan;
            $authUserType = auth()->user()->type;
            $monUserId = $prjmondws->id_karyawan;
        @endphp

        @php
            $isStatusOpen = $loadDataWS->status_ws == 'OPEN' ? true : false;
        @endphp

        <!-- TableAbsen Card -->
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card card-developer-meetup">
                <div class="card-body p-1">
                    @php
                        $ws_status = $loadDataWS->status_ws;
                        $blinkClass = $ws_status == 'OPEN' ? 'blink-text' : '';
                    @endphp

                    <div class="row match-height">
                        <!-- Left Card 1st -->
                        <div class="col-xl-12 col-md-12 col-12 d-flex align-items-center logo_eng_text px-0">
                            <div class="card mb-0 w-100">
                                <div class="card-body pt-0">
                                    <!-- Column 3: Engineer Text -->
                                    {{-- <div class="col text-end col-xl-3 col-md-6 col-12 d-flex align-items-top"> --}}


                                    {{-- <span class="btn btn-primary auth-role-eng-text">
                                        <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                    </span> --}}
                                    @if ($authUserType == 'Superuser')
                                        @php
                                            $ws_status = $loadDataWS->status_ws;
                                        @endphp
                                        @if ($ws_status == 'OPEN')
                                            @if (isset($modalData['modal_lock']))
                                                <button class="lock-ws-cmd btn btn-primary auth-role-eng-text"
                                                    lock_ws_id_value = "{{ $loadDataWS->id_ws ?: 0 }}">
                                                    <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                                </button>
                                            @endif

                                            {{-- <form class="row g-2 needs-validation d-flex justify-content-center"
                                                method="POST" action="{{ route('m.ws.status.lock') }}" id="lock_wsFORM"
                                                novalidate>
                                                @csrf
                                                <input type="hidden" id="lock-ws_id" name="lock-ws_id"
                                                    value="{{ $loadDataWS->id_ws }}" />
                                                <button id="confirmSave" class="btn btn-primary auth-role-eng-text">
                                                    <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                                </button>
                                            </form> --}}
                                        @else
                                            @if (isset($modalData['modal_unlock']))
                                                <button class="unlock-ws-cmd btn btn-primary auth-role-eng-text"
                                                    unlock_ws_id_value = "{{ $loadDataWS->id_ws ?: 0 }}">
                                                    <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                                </button>
                                            @endif

                                            {{-- <form class="row g-2 needs-validation d-flex justify-content-center"
                                                method="POST" action="{{ route('m.ws.status.unlock') }}" id="unlock_wsFORM"
                                                novalidate>
                                                @csrf
                                                <input type="hidden" id="unlock-ws_id" name="unlock-ws_id"
                                                    value="{{ $loadDataWS->id_ws }}" />
                                                <button id="confirmSave" class="btn btn-primary auth-role-eng-text">
                                                    <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                                </button>
                                            </form> --}}
                                        @endif
                                    @else
                                        @php
                                            $ws_status = $loadDataWS->status_ws;
                                        @endphp
                                        @if ($ws_status == 'OPEN')
                                            <button class="btn btn-primary auth-role-eng-text">
                                                <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                            </button>
                                        @else
                                            <button class="btn btn-primary auth-role-eng-text">
                                                <a class="mt-0 mb-0 cursor-default text-end">ENG</a>
                                            </button>
                                        @endif
                                    @endif






                                    {{-- </div> --}}
                                    <div class="row w-100 justify-content-between">
                                        <!-- Column 1: Brand Logo -->
                                        <div
                                            class="col text-start brand-logo col-xl-2 col-md-2 col-12 d-flex align-items-center">
                                            <span class="brand-logo">
                                                <img src="{{ asset('public/assets/logo/dws_header_vplogo.svg') }}"
                                                    class="img-fluid max-width-sm max-width-md max-width-lg" alt="VPLogo">
                                            </span>
                                        </div>

                                        <!-- Column 2: Project Title -->
                                        <div
                                            class="col text-center col-xl-8 col-md-5 col-12 pl-3 d-flex align-items-center justify-content-center">
                                            <span>
                                                <strong>
                                                    <h3 class="mt-0 mb-0 underline-text pt-2">PROJECT DAILY
                                                        WORKSHEET<br>(LEMBAR KERJA HARIAN)</h3>
                                                </strong>
                                                <i class="fas"></i>
                                            </span>
                                        </div>

                                        <!-- Column 3: Engineer Text -->
                                        <div class="col text-end col-xl-2 col-md-2 col-12 d-flex align-items-top">
                                            <span class="btn auth-role-eng-text">
                                                {{-- <a class="mt-0 mb-0 cursor-default text-end">ENGINEER</a> --}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Left Card 1st -->
                    </div>

                    <div class="row match-height KOP-1">
                        <!--  Check $data as array -->
                        {{-- <div class="col-xl-12 col-md-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <pre style="color: white">{{ print_r($prjmondws->toArray(), true) }}</pre>
                                    <br>
                                </div>
                            </div>
                        </div> --}}

                        <!-- Left Card -->
                        <div class="col-xl-7 col-md-7 col-12">
                            <div class="card mb-0 mb-0">
                                <div class="card-body overflow-x-scroll dis-overflow-y-hidden">
                                    <table class="w-100">
                                        <tbody>
                                            <tr>
                                                <td class="text-nowrap"><strong>DESCRIPTION</strong></td>
                                                <td class="pl-2">: </td>
                                                <td>{{ $prjmondws->id_project }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap"><strong>CLIENT'S NAME</strong></td>
                                                <td class="pl-2">: </td>
                                                <td>
                                                    {{ $loadDataWS->project->client->na_client }}
                                                </td>
                                                {{-- <td>{{ Str::limit($loadDataWS->project->client->na_client, 35, '...') }}</td> --}}
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap"><strong>DATE</strong></td>
                                                <td class="pl-2">: </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($loadDataWS->working_date_ws)->isoFormat($cust_date_format) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--/ Left Card -->

                        <!-- Right Card -->
                        <div class="col-xl-5 col-md-5 col-12">
                            <div class="card mb-0">
                                <div class="card-body overflow-x-scroll dis-overflow-y-hidden">
                                    {{-- <a class="text-end">
                                        <h6><strong>PT. VERTECH PERDANA</strong></h6>
                                    </a> --}}
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" class="text-nowrap"><strong>ARRIVAL TIME</strong></td>
                                                <td class="pl-2">: </td>
                                                <td>{{ \Carbon\Carbon::parse($loadDataWS->arrival_time_ws)->isoFormat($cust_time_format) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-nowrap"><strong>FINISH TIME</strong></td>
                                                <td class="pl-2">: </td>
                                                <td>{{ \Carbon\Carbon::parse($loadDataWS->finish_time_ws)->isoFormat($cust_time_format) }}
                                                </td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--/ Right Card -->
                    </div>

                    <div class="row match-height mb-1 px-1 DIVI-1">
                        @php
                            $authUserId = $authenticated_user_data->id_karyawan;
                            $authUserType = auth()->user()->type;
                            $authUserTeam = $authenticated_user_data->id_team;
                            $engPrjTeam = $prjmondws->id_team;
                            $exeUserId = $loadDataWS->id_karyawan;
                            // echo 'engPrjTeam: ' . $engPrjTeam . ' ------ ';
                            // echo 'authUserTeam: ' . $authUserTeam . '<br>';
                            // echo 'authUserId: ' . $authUserId . ' ------ ';
                            // echo 'exeUserId: ' . $exeUserId . '<br>';
                        @endphp

                        <div class="divider-container">
                            @php
                                $ws_status = $loadDataWS->status_ws;
                                $blinkBGClass = $ws_status == 'OPEN' ? 'blink-bg' : '';
                            @endphp
                            <div class="divider"></div> <!-- Divider line -->
                            <div class="button-wrapper">
                                <div class="nav-item">
                                    @if ($authUserType === 'Superuser' || $authUserType === 'Engineer')

                                        @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId))
                                            {{-- @if (isset($modalData['modal_add'])) --}}
                                            @if ($ws_status == 'OPEN')
                                                <button onclick="openModal('{{ $modalData['modal_add'] }}')"
                                                    class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success add-new-record"
                                                    style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                    data-popup="tooltip-custom" data-placement="bottom"
                                                    data-original-title="Add Task!">
                                                    <i class="fas fa-plus-circle fa-xs text-white"></i>
                                                </button>
                                            @else
                                                <button
                                                    class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger"
                                                    style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                    data-popup="tooltip-custom" data-placement="bottom"
                                                    data-original-title="Worksheet Locked!">
                                                    <i class="fas fa-plus-circle fa-xs text-white"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button
                                                class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger"
                                                style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                data-popup="tooltip-custom" data-placement="bottom"
                                                data-original-title="Worksheet Locked!">
                                                <i class="fas fa-plus-circle fa-xs text-white"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                                <div class="nav-item">
                                    @if ($authUserType === 'Superuser' || $authUserType === 'Engineer')
                                        @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId))
                                            @if (isset($modalData['modal_lock']) && $modalData['modal_lock'])
                                                @if ($ws_status == 'OPEN')
                                                    @if (isset($modalData['modal_lock']))
                                                        <button lock_ws_id_value = "{{ $loadDataWS->id_ws ?: 0 }}"
                                                            class="lock-ws-cmd btn mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger add-new-record {{ $blinkBGClass }}"
                                                            style="width: 3rem; height: 3rem; padding: 0;"
                                                            data-toggle="tooltip" data-popup="tooltip-custom"
                                                            data-placement="bottom" data-original-title="Lock Worksheet!">
                                                            <i class="fas fa-lock-open fa-xs text-white"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <button
                                                        class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger"
                                                        style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                        data-popup="tooltip-custom" data-placement="bottom"
                                                        data-original-title="Worksheet Locked!">
                                                        <i class="fas fa-lock-open fa-xs text-white"></i>
                                                    </button>
                                                @endif
                                            @endif
                                        @else
                                            @if ($ws_status == 'OPEN')
                                                <div>
                                                    <button
                                                        class="btn mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border bg-danger border-danger"
                                                        style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                        data-popup="tooltip-custom" data-placement="bottom"
                                                        data-original-title="Lock Worksheet!">
                                                        <i class="fas fa-lock-open fa-xs text-white"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <button
                                                    class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success"
                                                    style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                    data-popup="tooltip-custom" data-placement="bottom"
                                                    data-original-title="Worksheet Locked!">
                                                    <i class="fas fa-lock fa-xs text-white"></i>
                                                </button>
                                            @endif

                                        @endif
                                    @endif

                                </div>
                                <div class="nav-item">
                                    @if ($ws_status == 'CLOSED')
                                        @if ($authUserType === 'Superuser' || $authUserType === 'Supervisor' || $authUserType === 'Engineer')
                                            {{-- @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId)) --}}
                                            <div class="d-flex justify-content-center align-items-ce d-none">
                                                <form class="needs-validation" method="POST"
                                                    action="{{ route('m.task.printdomtask') }}" id="print_domtaskFORM"
                                                    novalidate>
                                                    @csrf
                                                    <input type="hidden" id="print-prj_id" name="print-prj_id"
                                                        value="{{ $loadDataWS->id_project }}" @readonly(true) />
                                                    <input type="hidden" id="print-ws_id" name="print-ws_id"
                                                        value="{{ $loadDataWS->id_ws }}" @readonly(true) />
                                                    <input type="hidden" id="print-ws_date" name="print-ws_date"
                                                        value="{{ $loadDataWS->working_date_ws }}" @readonly(true) />

                                                    <input type="hidden" id="print-act" name="print-act"
                                                        value="dom" />
                                                    <input type="hidden" id="print-task-title" name="print-task-title"
                                                        value="{{ $prjmondws->id_project }} {{ \Carbon\Carbon::parse($loadDataWS->working_date_ws)->isoFormat($cust_date_format) }} DAILY WORKSHEETS" />
                                                    <input type="hidden" id="print-ref" name="print-ref"
                                                        value="print" />

                                                    <button
                                                        class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success"
                                                        style="width: 3rem; height: 3rem; padding: 0;"
                                                        data-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-placement="bottom" data-original-title="Print">
                                                        <i class="fas fa-duotone fa-print text-white"></i>
                                                    </button>
                                                </form>
                                                <form class="needs-validation" method="POST"
                                                    action="{{ route('m.task.printdomtask') }}" id="print_domtaskFORM"
                                                    novalidate>
                                                    @csrf
                                                    <input type="hidden" id="print-prj_id" name="print-prj_id"
                                                        value="{{ $loadDataWS->id_project }}" @readonly(true) />
                                                    <input type="hidden" id="print-ws_id" name="print-ws_id"
                                                        value="{{ $loadDataWS->id_ws }}" @readonly(true) />
                                                    <input type="hidden" id="print-ws_date" name="print-ws_date"
                                                        value="{{ $loadDataWS->working_date_ws }}" @readonly(true) />

                                                    <input type="hidden" id="print-act" name="print-act"
                                                        value="dom" />
                                                    <input type="hidden" id="print-task-title" name="print-task-title"
                                                        value="{{ $prjmondws->id_project }} {{ \Carbon\Carbon::parse($loadDataWS->working_date_ws)->isoFormat($cust_date_format) }} DAILY WORKSHEETS" />
                                                    <input type="hidden" id="print-ref" name="print-ref"
                                                        value="pdf" />

                                                    <button
                                                        class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success"
                                                        style="width: 3rem; height: 3rem; padding: 0;"
                                                        data-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-placement="bottom" data-original-title="Download as PDF">
                                                        <i class="fad fa-download text-white"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- @else
                                                <button
                                                    class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success"
                                                    style="width: 3rem; height: 3rem; padding: 0;">
                                                    <i class="fas fa-print fa-xs text-white"></i>
                                                </button>
                                            @endif --}}
                                        @endif
                                    @else
                                        @if ($authUserType === 'Superuser' || $authUserType === 'Supervisor' || $authUserType === 'Engineer')
                                            <div class="d-flex justify-content-center align-items-ce d-none">
                                                <button
                                                    class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger"
                                                    style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                    data-popup="tooltip-custom" data-placement="bottom"
                                                    data-original-title="Worksheet Not Locked!">
                                                    <i class="fas fa-duotone fa-print text-white"></i>
                                                </button>

                                                <button
                                                    class="btn bg-danger mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-danger"
                                                    style="width: 3rem; height: 3rem; padding: 0;" data-toggle="tooltip"
                                                    data-popup="tooltip-custom" data-placement="bottom"
                                                    data-original-title="Worksheet Not Locked!">
                                                    <i class="fad fa-download text-white"></i>
                                                </button>

                                            </div>
                                        @endif
                                    @endif


                                </div>
                            </div>
                        </div>

                    </div>







                    @php
                        $totalActualAtHeader = 0;
                        $totalAtHeader = 0;
                    @endphp
                    @foreach ($prjmondws->monitor as $mon)
                        @if ($mon->qty)
                            @php
                                $qty = $mon->qty;
                                // Find the tasks related to the current monitor where the associated worksheet's expired_ws is null
$relatedTasks = collect($prjmondws->task)->filter(function ($task) use (
    $mon,
    $prjmondws,
) {
    // Find the related worksheet for the task
    $worksheet = collect($prjmondws->worksheet)->firstWhere('id_ws', $task['id_ws']);
    // Check if the task's worksheet expired_ws is null
                                    return $task['id_monitoring'] === $mon['id_monitoring'] &&
                                        ($worksheet['expired_at_ws'] ?? null) === null; // Match tasks by id_monitoring and check expired_ws
                                });

                                // Calculate the total progress from related tasks
                                $totalProgress = 0;
                                foreach ($relatedTasks as $task) {
                                    $totalProgress += $task->progress_current_task; // Sum up the progress of related tasks
                                }

                                // Assuming you want to calculate based on the average progress
                                $up = $relatedTasks->count() > 0 ? $totalProgress / $relatedTasks->count() : 0; // Average progress
                                $totalAtHeader = ($qty * $up) / 100; // Calculate total percentage
                                $totalActualAtHeader += $totalAtHeader; // Accumulate to totalActualAtHeader
                            @endphp
                        @else
                            @php
                                $totalActualAtHeader = 0; // No quantity, total remains 0
                            @endphp
                        @endif
                    @endforeach
                    @php
                        $totalActual = 0; // Initialize totalActual
                    @endphp


                    <table id="daftarTaskTable" class="table table-striped">
                        <thead>
                            <tr>
                                @if ($loadDataWS->status_ws === 'OPEN')
                                    <th rowspan="2" class="cell-fit text-center">Act</th>
                                @endif
                                <th rowspan="2" class="text-center align-middle">Time</th>
                                <th rowspan="2" class="text-center">Task</th>
                                <th rowspan="2" class="text-center">Description</th>
                                {{-- <th colspan="2" class="text-center">Progress</th> --}}
                                <th colspan="2"
                                    class="text-center {{ $totalActualAtHeader == 100 ? 'text-success' : ($totalActualAtHeader > 100 ? 'text-danger' : 'text-warning') }}">
                                    Progress ({{ number_format($totalActualAtHeader, 0) }}%)
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center align-middle">Actual</th>
                                <th class="text-center align-middle">Current</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loadDataWS['task'] as $relDWS)
                                <tr>
                                    @if ($loadDataWS->status_ws === 'OPEN')
                                        <td>
                                            <div class="dropdown d-lg-block d-sm-block d-md-block">
                                                <button class="btn btn-icon navbar-toggler pt-0" type="button"
                                                    id="tableActionDropdown" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i data-feather="align-justify" class="font-medium-5"></i>
                                                </button>
                                                <!-- dropdown menu -->
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="tableActionDropdown">
                                                    @if (auth()->user()->type === 'Superuser' ||
                                                            (auth()->user()->type === 'Engineer' && auth()->user()->id_karyawan == $loadDataWS->id_karyawan))
                                                        <a class="edit-record dropdown-item d-flex align-items-center"
                                                            edit_task_id_value = "{{ $relDWS->id_task ?: 0 }}"
                                                            edit_project_id_value = "{{ $relDWS->id_project ?: 0 }}"
                                                            onclick="openModal('{{ $modalData['modal_edit'] }}')">
                                                            <i data-feather="edit" class="mr-1"
                                                                style="color: #28c76f;"></i>
                                                            Edit
                                                        </a>
                                                        @if (auth()->user()->type === 'Superuser' ||
                                                                (auth()->user()->type === 'Engineer' && auth()->user()->id_karyawan == $loadDataWS->id_karyawan))
                                                            <a class="delete-record dropdown-item d-flex align-items-center"
                                                                del_task_id_value = "{{ $relDWS->id_task ?: 0 }}"
                                                                onclick="openModal('{{ $modalData['modal_delete'] }}')">
                                                                <i data-feather="trash" class="mr-1"
                                                                    style="color: #ea5455;"></i>
                                                                Delete
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a class="dropdown-item d-flex align-items-center">
                                                            <i data-feather="block" class="mr-1"
                                                                style="color: #ea5455;"></i>
                                                            Sorry, You don't have enough authority to manage this data :)
                                                        </a>
                                                    @endif
                                                </div>
                                                <!--/ dropdown menu -->
                                            </div>
                                        </td>
                                    @endif

                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($relDWS->start_time_task)->isoFormat('hh:mm') }}
                                    </td>
                                    <td>
                                        {{ $relDWS->monitor->category }}
                                    </td>
                                    <td>
                                        @php
                                            $descbTask = $relDWS->descb_task;
                                            if (strpos($descbTask, '*- ') !== false) {
                                                $descbTask = str_replace(
                                                    '*- ',
                                                    '<i class="fas fa-circle fs-sm"></i>&nbsp;',
                                                    $descbTask,
                                                );
                                            } elseif (strpos($descbTask, '- ') !== false) {
                                                $descbTask = str_replace(
                                                    '- ',
                                                    '<i class="fas fa-circle fs-sm"></i>&nbsp;',
                                                    $descbTask,
                                                );
                                            }
                                            $descbTask = str_replace("\n", '<br>', $descbTask);
                                        @endphp
                                        {!! $descbTask !!}
                                    </td>
                                    {{-- <td class="text-center align-middle">
                                                @if ($relDWS->progress_actual_task != null || $relDWS->progress_actual_task != 0)
                                                    {{ $relDWS->progress_actual_task }}%
                                                @else
                                                    0%
                                                @endif
                                            </td> --}}
                                    {{-- <td class="text-center align-middle">
                                        @php
                                            $qty = $relDWS->monitor->qty;
                                            $up = $relDWS->last_task_progress_update($relDWS->id_monitoring);
                                            $total = ($qty * $up) / 100;
                                            $totalActual += $total;
                                        @endphp
                                        {{ $total }}%
                                    </td> --}}

                                    <td class="text-center align-middle">
                                        @php
                                            $total = 0; // Initialize total for this monitoring entry
                                            $qty = $relDWS->monitor->qty;

                                            // Check if qty is defined and greater than zero
                                            if ($qty) {
                                                // Find the tasks related to the current monitor where the associated worksheet's expired_ws is null
    $relatedTasks = collect($prjmondws->task)->filter(function ($task) use (
        $relDWS,
        $prjmondws,
    ) {
        // Find the related worksheet for the task
        $worksheet = collect($prjmondws->worksheet)->firstWhere(
            'id_ws',
            $task['id_ws'],
        );
        // Check if the task's worksheet expired_ws is null
                                                    return $task['id_monitoring'] === $relDWS->id_monitoring &&
                                                        ($worksheet['expired_ws'] ?? null) === null; // Match tasks by id_monitoring and check expired_ws
                                                });

                                                // Calculate the total progress from related tasks
                                                $totalProgress = 0;
                                                foreach ($relatedTasks as $task) {
                                                    $totalProgress += $task->progress_current_task; // Sum up the progress of related tasks
                                                }

                                                // Assuming you want to calculate based on the average progress
                                                $up =
                                                    $relatedTasks->count() > 0
                                                        ? $totalProgress / $relatedTasks->count()
                                                        : 0; // Average progress
                                                $total = ($qty * $up) / 100; // Calculate total percentage
                                                $totalActual += $total; // Accumulate to totalActual
                                            }
                                        @endphp

                                        {{ number_format($total, 0) }}% <!-- Display total with 2 decimal places -->
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($relDWS->progress_current_task != null || $relDWS->progress_current_task != 0)
                                            {{ $relDWS->progress_current_task }}%
                                        @else
                                            0%
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                        <!-- TABLE FOOTER -->
                        <tfoot>
                            <tr>
                                <td colspan="{{ $isStatusOpen ? '6' : '5' }}" class="px-1">
                                    <strong>
                                        REMARK (CATATAN AKHIR)
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                @php
                                    $remarkWS =
                                        isset($loadDataWS->remark_ws) && !empty($loadDataWS->remark_ws)
                                            ? $loadDataWS->remark_ws
                                            : '- Tidak Ada';

                                    if ($remarkWS !== '- Tidak Ada') {
                                        // Replace bullet points with icons for display outside the textarea
                                        $formattedRemarkWS = str_replace(
                                            '*- ',
                                            '<i class="fas fa-circle fs-sm"></i>&nbsp;',
                                            $remarkWS,
                                        );
                                        $formattedRemarkWS = str_replace(
                                            '- ',
                                            '<i class="fas fa-circle fs-sm"></i>&nbsp;',
                                            $formattedRemarkWS,
                                        );
                                        // Replace new lines with line breaks for display outside the textarea
                                        $formattedRemarkWS = str_replace("\n", '<br>', $formattedRemarkWS);
                                    } else {
                                        $formattedRemarkWS = '- Tidak Ada';
                                    }

                                    // Strip HTML tags for textarea
                                    $textareaRemarkWS = strip_tags($remarkWS);
                                @endphp

                                <td colspan="{{ $isStatusOpen ? '6' : '5' }}" rowspan="1" class="p-0 align-middle">
                                    <textarea class="remark-textarea w-100 h-fit px-1 m-0 text-left border-0" ws_id_value="{{ $loadDataWS->id_ws }}"
                                        rows="8" {{ $loadDataWS->status_ws == 'OPEN' ? '' : 'disabled' }}>{!! htmlspecialchars($textareaRemarkWS, ENT_QUOTES, 'UTF-8') !!}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="{{ $isStatusOpen ? '4' : '3' }}" rowspan="5" class="px-1">

                                    <div class="d-flex flex-col justify-content-around">
                                        <div class="d-flex flex-column align-items-start">
                                            <span>
                                                <strong>
                                                    EXECUTED BY, (DIKERJAKAN OLEH)
                                                </strong>
                                            </span>
                                            <div style="height: 8em;"></div> <!-- Empty div for spacing -->
                                            <span class="justify-content-center w-100 align-text-bottom">
                                                {{ Str::limit($loadDataWS->karyawan->na_karyawan, 30, '...') }}
                                            </span>
                                            <span class="underline-text">
                                                <strong>
                                                    PT. VERTECH PERDANA
                                                </strong>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column align-items-start">
                                            <span>
                                                <strong>
                                                    ACKNOWLEDGED BY, (DIKETAHUI OLEH)
                                                </strong>
                                            </span>
                                            <div style="height: 8em;"></div> <!-- Empty div for spacing -->
                                            <span class="justify-content-center w-100 align-text-bottom">
                                                {{ Str::limit($loadDataWS->project->client->na_client, 33, '...') }}
                                            </span>
                                            <span class="underline-text">
                                                <strong>
                                                    @php
                                                        $clientName = $loadDataWS->project->client->na_client;
                                                        $clientNameLength = mb_strlen(
                                                            Str::limit($clientName, 37, '...'),
                                                        );
                                                        $clientLabelLength = mb_strlen('(CLIENT) ');
                                                        $totalLength = $clientNameLength * 3 - 2;
                                                        $dotsCount = max(
                                                            0,
                                                            $totalLength - $clientLabelLength - $clientNameLength,
                                                        );
                                                        echo '(CLIENT)' . str_repeat('.', $dotsCount);
                                                    @endphp
                                                </strong>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="2" class="px-1 text-center"><strong>Time Stamp</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-1 tfoot-date">
                                    <strong>
                                        Start Date:
                                    </strong>
                                    <br>
                                    @php
                                        $startDate = $loadDataWS->working_date_ws;
                                        $dateStart = \Carbon\Carbon::parse($startDate);
                                        // Format date in Indonesian
                                        \Carbon\Carbon::setLocale('in');
                                        $formattedDateStart = $dateStart->isoFormat('dddd, DD MMM YYYY');
                                        // Format time in English
                                        \Carbon\Carbon::setLocale('en');
                                        $formattedTimeStart = $dateStart->isoFormat('hh:mm:ss A');
                                        // Combine date and time
                                        $formattedDateTimeStart = $formattedDateStart . ' at ' . $formattedTimeStart;
                                        echo $formattedDateTimeStart;
                                    @endphp
                                </td>
                                {{-- <td class="border-0"></td> --}}
                            </tr>
                            <tr>
                                <td colspan="2" class="px-1 tfoot-date">
                                    <strong>
                                        Closed Date:
                                    </strong>
                                    <br>
                                    @if ($loadDataWS->status_ws == 'OPEN')
                                        -
                                    @else
                                        @php
                                            $closedDate = $loadDataWS->closed_at_ws;
                                            $dateClose = \Carbon\Carbon::parse($closedDate);

                                            // Format date in Indonesian
                                            \Carbon\Carbon::setLocale('in');
                                            $formattedDateClose = $dateClose->isoFormat('dddd, DD MMM YYYY');

                                            // Format time in English
                                            \Carbon\Carbon::setLocale('en');
                                            $formattedTimeClose = $dateClose->isoFormat('hh:mm:ss A');

                                            // Combine date and time
                                            $formattedDateTimeClose =
                                                $formattedDateClose . ' at ' . $formattedTimeClose;
                                            echo $formattedDateTimeClose;
                                        @endphp
                                    @endif

                                </td>
                                {{-- <td class="border-0"></td> --}}
                            </tr>
                            <tr>
                                <td colspan="2" class="px-1 text-center align-middle"><strong>Status</strong></td>
                            </tr>

                            @if ($isStatusOpen)
                                @if ($authUserType === 'Superuser' || $authUserType === 'Engineer')
                                    @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId))
                                        <tr class="lock-ws-cmd cursor-pointer"
                                            lock_ws_id_value="{{ $loadDataWS->id_ws ?: 0 }}">
                                            <td colspan="2"
                                                class="rowlock px-1 text-center {{ $blinkBGClass != '' ? $blinkBGClass : 'bg-success' }}">
                                                <button lock_ws_id_value = "{{ $loadDataWS->id_ws ?: 0 }}"
                                                    class="lock-ws-cmd btn mx-1 border-0 d-flex {{ $blinkBGClass }}"
                                                    style="padding: 0.5rem 1rem; justify-self: center;"
                                                    data-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-placement="bottom" data-original-title="Lock Worksheet!">
                                                    <h3 class="mb-0">
                                                        <strong>OPEN</strong>
                                                    </h3>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @else
                                <tr>
                                    <td colspan="2"
                                        class="rowlock px-1 text-center {{ $blinkBGClass != '' ? $blinkBGClass : 'bg-success' }}">
                                        <div>
                                            <button class="mx-1 border-0 d-flex bg-transparent text-white"
                                                style="padding: 0.5rem 1rem; justify-self: center;" data-toggle="tooltip"
                                                data-popup="tooltip-custom" data-placement="bottom"
                                                data-original-title="Worksheet Locked!">
                                                <h3 class="mb-0">
                                                    <strong>CLOSED</strong>
                                                </h3>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif




                            {{--
                            <tr>
                                <td colspan="2"
                                    class="rowlock px-1 text-center {{ $blinkBGClass != '' ? $blinkBGClass : 'bg-success' }}">
                                    @if ($isStatusOpen)
                                        @if ($authUserType === 'Superuser' || $authUserType === 'Engineer')
                                            @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId))
                                                @if (isset($modalData['modal_lock']))
                                                    <button lock_ws_id_value = "{{ $loadDataWS->id_ws ?: 0 }}"
                                                        class="lock-ws-cmd btn mx-1 border-0 d-flex {{ $blinkBGClass }}"
                                                        style="padding: 0.5rem 1rem; justify-self: center;"
                                                        data-toggle="tooltip" data-popup="tooltip-custom"
                                                        data-placement="bottom" data-original-title="Lock Task!">
                                                        <h3 class="mb-0">
                                                            <strong>OPEN</strong>
                                                        </h3>
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    @else
                                        <div>
                                            <button class="mx-1 border-0 d-flex bg-transparent text-white"
                                                style="padding: 0.5rem 1rem; justify-self: center;">
                                                <h3 class="mb-0">
                                                    <strong>CLOSED</strong>
                                                </h3>
                                            </button>
                                        </div>

                                    @endif

                                </td>
                            </tr> --}}
                        </tfoot>

                    </table>

                </div>

            </div>
        </div>
        <!--/ TableAbsen Card -->

    </div>




    <!-- BEGIN: AddTaskModal--> @include('v_res.m_modals.userpanels.m_daftartask.v_add_taskModal') <!-- END: AddTaskModal-->
    <!-- BEGIN: EditTaskModal--> @include('v_res.m_modals.userpanels.m_daftartask.v_edit_taskModal') <!-- END: EditTaskModal-->
    <!-- BEGIN: DelTaskModal--> @include('v_res.m_modals.userpanels.m_daftartask.v_del_taskModal') <!-- END: DelTaskModal-->
    @if ($reset_btn)
        <!-- BEGIN: ResetTaskModal--> @include('v_res.m_modals.userpanels.m_daftartask.v_reset_taskModal') <!-- END: ResetTaskModal-->
    @endif

    @if ($isStatusOpen)
        <!-- BEGIN: LockWSModal--> @include('v_res.m_modals.userpanels.m_daftarworksheet.v_lock_wsModal') <!-- END: LockWSModal-->
    @else
        <!-- BEGIN: UnlockWSModal--> @include('v_res.m_modals.userpanels.m_daftarworksheet.v_unlock_wsModal') <!-- END: UnlockWSModal-->
    @endif
@endsection


@section('footer_page_js')
    <script src="{{ asset('public/theme/vuexy/app-assets/js/scripts/components/components-modals.js') }}"></script>

    <script>
        $(document).ready(function() {
            var lengthMenu = [10, 50, 100, 500, 1000, 2000, 3000]; // Length menu options

            var $table = $('#daftarTaskTable').DataTable({
                lengthMenu: lengthMenu,
                pageLength: 100,
                responsive: false,
                ordering: true,
                searching: true,
                language: {
                    lengthMenu: 'Display _MENU_ records per page',
                    info: 'Showing page _PAGE_ of _PAGES_',
                    search: 'Search',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: '&rarr;',
                        previous: '&larr;'
                    }
                },
                scrollCollapse: true,
                dom: '<"card-header border-bottom p-1"<"head-label"><"d-flex justify-content-between align-items-center"<"dt-search-field"f>>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                columnDefs: [{ // Set the initial column visibility
                    targets: [], // Specify the columns to hide
                    visible: false // Set visibility to false
                }],
                initComplete: function() {
                    $(this.api().column([0]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([1]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([2]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([3]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([4]).header()).addClass('cell-fit text-center align-middle');

                    var pageInfo = this.api().page.info();
                    $('#lengthMenu').val(pageInfo.length); // Updated ID
                },
                drawCallback: function() {
                    var pageInfo = this.api().page.info();
                    $('#lengthMenu').val(pageInfo.length); // Updated ID
                },
            });

            // Create a dropdown button with nested actions
            var dropdownButton = `
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-table fa-xs"></i>
                    </button>
                    <div class="dropdown-menu p-1" style="z-index: 1052; max-height: 300px; overflow-y: auto; overflow-x: auto;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="dropdown-item">
                                <label for="lengthMenu" class="my-0">Records per page:</label>
                                <select class="select2 form-control form-select-sm" id="lengthMenu" name="lengthMenu" aria-label="Select page length">
                                    ${lengthMenu.map(function(length) {
                                        return `<option value="${length}">${length}</option>`;
                                    }).join('')}
                                </select>
                            </div>
                            <div class="dropdown-item colvis-container">
                                <label>Column Visibility:</label>
                                <div class="colvis-options my-0"></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-item d-flex justify-content-center align-content-center">Project Daily Worksheets</span>
                    </div>
                </div>
            `;

            // Wrap the dropdown button and search field in a scrollable container
            $('.head-label').prepend(`
                <div class="dropdown-search-container">
                    ${dropdownButton}
                    <div class="dt-search-field"></div>
                </div>
            `);

            // Handle length change
            $('.dropdown-item select').on('change', function() {
                var newLength = $(this).val();
                $table.page.len(newLength).draw(); // Set new page length and redraw
            });

            // Generate dynamic column visibility options
            var columnCount = $table.columns().count();
            for (var i = 0; i < columnCount; i++) {
                var column = $table.column(i);
                var columnVisible = column.visible();
                var checkbox = `
                    <label>
                        <input type="checkbox" class="colvis-checkbox" data-column="${i}" ${columnVisible ? 'checked' : ''}> ${column.header().textContent}
                    </label><br>
                `;
                $('.colvis-options').append(checkbox);
            }

            // Handle column visibility toggle
            $('.colvis-options').on('change', '.colvis-checkbox', function() {
                var column = $(this).data('column');
                var isVisible = $(this).is(':checked');
                $table.column(column).visible(isVisible); // Toggle column visibility
            });

            // Prevent dropdown from closing when interacting with select field or column visibility options
            $(document).on('click', '.dropdown-item select, .dropdown-item a, .colvis-checkbox', function(event) {
                event.stopPropagation(); // Prevent the dropdown from closing
            });


            configDoPrint();

            function configDoPrint() {
                $('#print_taskFORM button').on('click', function(event) {
                    event.preventDefault(); // Prevent default form submission

                    // Get the current page length
                    var currentLength = $table.page.len();
                    $('#print-task-length').val(currentLength);

                    // Get visible columns
                    var visibleColumns = [];
                    $table.columns().every(function(index) {
                        if (this.visible()) {
                            visibleColumns.push(this.header()
                                .textContent); // Push the column header text
                        }
                    });
                    $('#print-task-columns').val(JSON.stringify(visibleColumns)); // Store as JSON string

                    // // Optionally set the ID for printing
                    // $('#print-task_id').val($(this).attr('print_task_id_value'));

                    // Submit the form
                    $('#print_taskFORM').submit();
                });
            }



        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalId = 'edit_taskModal';
            const modalSelector = document.getElementById(modalId);
            const modalToShow = new bootstrap.Modal(modalSelector);
            const targetedModalForm = document.querySelector('#' + modalId + ' #edit_taskModalFORM');

            $(document).on('click', '.edit-record', function(event) {
                var taskID = $(this).attr('edit_task_id_value');
                var projectID = $(this).attr('edit_project_id_value');
                console.log('Edit button clicked for task_id:', taskID);
                setTimeout(() => {
                    $.ajax({
                        url: '{{ route('m.task.gettask') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Update the CSRF token here
                        },
                        data: {
                            taskID: taskID,
                            projectID: projectID
                        },
                        success: function(response) {
                            console.log(response);
                            $('#edit-task_id').val(response.id_task);
                            // $('#edit-task_work_time').val(response.task_worktime);
                            // setTaskWorkTime(response);
                            $('#edit-task_description').val(response.task_description);
                            $('#edit-task_current_progress').val(response
                                .task_currentprogress);
                            $('#edit-ws_id_ws').val(response.id_ws);
                            $('#edit-ws_id_project').val(response.id_project);
                            $('#edit-ws_arrival_time').val(response.arrivalTime);
                            $('#edit-ws_finish_time').val(response.finishTime);


                            setTaskWorkTime(response);
                            setTask(response);
                            // console.log('SHOWING MODAL');
                            modalToShow.show();
                        },
                        error: function(error) {
                            console.log("Err [JS]:\n");
                            console.log(error);
                        }
                    });
                }); // <-- Closing parenthesis for setTimeout
            });


            function setTaskWorkTime(response) {
                $('#edit-task_work_time').val(response.task_worktime);
                // Initialize Flatpickr after setting the value
                flatpickr("#edit-task_work_time", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i:S",
                    altInput: true,
                    altFormat: "h:i:s K",
                    allowInput: true,
                    enableSeconds: true,
                    time_24hr: false
                });
            }


            function setTask(response) {
                var taskSelect = $('#' + modalId +
                    ' #edit-task_id_monitoring');
                taskSelect.empty(); // Clear existing options
                taskSelect.append($('<option>', {
                    value: "",
                    text: "Select Task"
                }));
                $.each(response.taskList, function(index,
                    taskOption) {
                    var option = $('<option>', {
                        value: taskOption.value,
                        text: `[${taskOption.value}] ${taskOption.text}`
                    });
                    if (taskOption.selected) {
                        option.attr('selected',
                            'selected'); // Select the option
                    }
                    taskSelect.append(option);
                });

            }

            const saveRecordBtn = document.querySelector('#' + modalId + ' #confirmSave');
            if (saveRecordBtn) {
                saveRecordBtn.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default button behavior
                    targetedModalForm.submit(); // Submit the form if validation passes
                });
            }
        });
    </script>



    <script>
        $(document).ready(function() {
            $('.toggle-password').click(function() {
                var passwordInput = $('#Password');
                var passwordFieldType = passwordInput.attr('type');
                var passwordIcon = $('.password-icon');

                if (passwordFieldType === 'password') {
                    passwordInput.attr('type', 'text');
                    passwordIcon.attr('data-feather', 'eye-off');
                } else {
                    passwordInput.attr('type', 'password');
                    passwordIcon.attr('data-feather', 'eye');
                }

                feather.replace(); // Refresh the Feather icons after changing the icon attribute
            });
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            whichModal = "delete_taskModal";
            const modalSelector = document.querySelector('#' + whichModal);
            const modalToShow = new bootstrap.Modal(modalSelector);

            setTimeout(() => {
                $('.delete-record').on('click', function() {
                    var taskID = $(this).attr('del_task_id_value');
                    $('#' + whichModal + ' #del_task_id').val(taskID);
                    modalToShow.show();
                });
            }, 800);
        });
    </script>


    @if ($authUserType === 'Superuser' || $authUserType === 'Engineer')
        @if ($authUserType === 'Superuser' || ($authUserTeam === $engPrjTeam && $authUserId == $exeUserId))
            <script>
                $(document).ready(function() {
                    // Function to handle AJAX request
                    function sendRemarkUpdate() {
                        var remarkText = $('.remark-textarea').val();
                        var wsId = $('.remark-textarea').attr('ws_id_value'); // Get the worksheet ID

                        console.log(wsId);

                        $.ajax({
                            url: '{{ route('m.ws.remark.edit') }}',
                            type: 'POST',
                            data: {
                                id_ws: wsId,
                                remarkText: remarkText,
                                _token: '{{ csrf_token() }}' // CSRF token for security
                            },
                            success: function(response) {
                                if (response != null && response.message) {
                                    jsonToastReceiver(response
                                        .message); // Pass response.message instead of response
                                }

                                console.log('Remark updated successfully:', response);
                            },
                            error: function(xhr, status, error) {
                                if (xhr.responseJSON != null && xhr.responseJSON.message) {
                                    jsonToastReceiver(xhr.responseJSON
                                        .message); // Pass response.message instead of response
                                }
                                console.error('Error updating remark:', error);
                            }
                        });
                    }

                    // Keydown event for Ctrl + Enter or Up Arrow + Enter
                    $('.remark-textarea').on('keydown', function(event) {
                        // Check if Enter key is pressed
                        if (event.key === 'Enter') {
                            // Check if Ctrl key is pressed or Up Arrow key was pressed
                            if (event.ctrlKey || (event.originalEvent.key === 'ArrowUp')) {
                                event.preventDefault(); // Prevent the default action (e.g., adding a new line)
                                sendRemarkUpdate(); // Call the function to send AJAX request
                            }
                        }
                    });

                    // Blur event for leaving the textarea
                    $('.remark-textarea').on('blur', function() {
                        // Send AJAX request when the textarea loses focus
                        sendRemarkUpdate();
                    });
                });
            </script>
        @endif
    @endif

    @if ($isStatusOpen)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                whichModal = "lock_wsModal";
                const modalSelector = document.querySelector('#' + whichModal);
                const modalToShow = new bootstrap.Modal(modalSelector);

                setTimeout(() => {
                    $('.lock-ws-cmd').on('click', function() {
                        var wsID = $(this).attr('lock_ws_id_value');
                        $('#' + whichModal + ' #lock-ws_id').val(wsID);
                        modalToShow.show();
                    });
                }, 800);
            });
        </script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                whichModal = "unlock_wsModal";
                const modalSelector = document.querySelector('#' + whichModal);
                const modalToShow = new bootstrap.Modal(modalSelector);

                setTimeout(() => {
                    $('.unlock-ws-cmd').on('click', function() {
                        var wsID = $(this).attr('unlock_ws_id_value');
                        $('#' + whichModal + ' #unlock-ws_id').val(wsID);
                        modalToShow.show();
                    });
                }, 800);
            });
        </script>
    @endif
@endsection
