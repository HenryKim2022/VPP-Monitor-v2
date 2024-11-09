@php
    $page = Session::get('page');
    $page_title = $page['page_title'];
    $cust_date_format = 'DD MMM YYYY';
    $cust_time_format = 'hh:mm:ss A';
    $reset_btn = false;
    // $authenticated_user_data = Session::get('authenticated_user_data');
    // dd($authenticated_user_data);
@endphp

@extends('layouts.userpanels.v_main')

@section('header_page_cssjs')
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
                    <pre style="color: white">{{ print_r($loadDaftarProjectsFromDB->toArray(), true) }}</pre>
                    <br>
                </div>
            </div>
        </div> --}}



        <!-- TableAbsen Card -->
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card card-developer-meetup">

                <div class="card-body p-1">

                    <div class="row match-height KOP-1">
                        <!-- Left Card 1st -->
                        <div class="col-xl-12 col-md-12 col-12 d-flex align-items-center logo_eng_text px-0">
                            <div class="card mb-0 w-100">
                                <div class="card-body pt-0">
                                    <!-- Column 3: Engineer Text -->
                                    {{-- <div class="col text-end col-xl-3 col-md-6 col-12 d-flex align-items-top"> --}}
                                    <span class="btn btn-primary auth-role-eng-text">
                                        <a class="mt-0 mb-0 cursor-default text-end">SPV - ENG - CLI</a>
                                    </span>
                                    {{-- </div> --}}
                                    <div class="row w-100 justify-content-between">
                                        <!-- Column 1: Brand Logo -->
                                        <div
                                            class="col text-start brand-logo col-xl-2 col-md-2 col-sm-1 d-flex align-items-center">
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
                                                    <h3 class="mt-0 mb-0 underline-text pt-2">PROJECT LISTS</h3>
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

                        <!--/ Right Card 2nd -->
                    </div>



                    <div class="row match-height mb-1 px-1 DIVI-1">
                        <div class="divider-container">
                            <div class="divider"></div> <!-- Divider line -->
                            <div class="button-wrapper">
                                <div class="nav-item">
                                    @if (auth()->user()->type === 'Superuser' || auth()->user()->type === 'Supervisor')
                                        @if ($modalData['modal_add'])
                                            <button onclick="openModal('{{ $modalData['modal_add'] }}')"
                                                class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success add-new-record"
                                                style="width: 3rem; height: 3rem; padding: 0;">
                                                <i class="fas fa-plus-circle fa-xs text-white"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                                <div class="nav-item">
                                    @if (auth()->user()->type === 'Superuser' || auth()->user()->type === 'Supervisor')
                                        <form class="needs-validation" method="POST" action="{{ route('m.mon.printmon') }}"
                                            id="print_moniFORM" novalidate>
                                            @csrf
                                            <input type="hidden" id="print-moni_id" name="print-moni_id" />
                                            <button
                                                class="btn bg-success mx-1 d-inline-block rounded-circle d-flex justify-content-center align-items-center border border-success add-new-record"
                                                style="width: 3rem; height: 3rem; padding: 0;">
                                                <i class="fas fa-print fa-xs text-white"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>



                    <table id="daftarProjectTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th class="cell-fit">Act</th>
                                <th class="cell-fit text-nowrap">Project-No</th>
                                <th class="text-nowrap">Project Name</th>
                                <th class="">Customer</th>
                                <th class="text-nowrap">Project Co</th>
                                <th class="">Team</th>
                                <th class="">Status</th>
                                <th class="">Start</th>
                                <th class="">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- {{dd($loadDaftarProjectsFromDB->toArray());}} --}}
                            @foreach ($loadDaftarProjectsFromDB as $project)
                                @php
                                    $totalActual = 0;
                                    $ongoing = $project->prjstatus_beta() == 'ONGOING' ? true : false;

                                    $currentDateTime = \Carbon\Carbon::now();
                                    $expiredAt = \Carbon\Carbon::parse($project->deadline_project);
                                    $isExpired = $expiredAt < $currentDateTime;
                                @endphp

                                @if ($project->monitor->isNotEmpty())
                                    @foreach ($project->monitor as $monitor)
                                        @php
                                            $total = 0; // Initialize total for this monitoring entry
                                        @endphp
                                        @if ($monitor->qty)
                                            @php
                                                $qty = $monitor->qty;
                                                // Find the tasks related to the current monitor where the associated worksheet's expired_ws is null
$relatedTasks = collect($project->task)->filter(function ($task) use (
    $monitor,
    $project,
) {
    // Find the related worksheet for the task
    $worksheet = collect($project->worksheet)->firstWhere(
        'id_ws',
        $task['id_ws'],
    );
    // Check if the task's worksheet expired_ws is null
                                                    return $task['id_monitoring'] === $monitor->id_monitoring &&
                                                        ($worksheet['expired_at_ws'] ?? null) === null; // Match tasks by id_monitoring and check expired_ws
                                                });

                                                // Calculate the total progress from related tasks
                                                $totalProgress = 0;
                                                foreach ($relatedTasks as $task) {
                                                    $totalProgress += $task['progress_current_task']; // Sum up the progress of related tasks
                                                }

                                                // Calculate average progress
                                                $up =
                                                    $relatedTasks->count() > 0
                                                        ? $totalProgress / $relatedTasks->count()
                                                        : 0; // Average progress
                                                $total = ($qty * $up) / 100; // Calculate total percentage
                                                $totalActual += $total; // Accumulate to totalActual
                                            @endphp
                                        @else
                                            @php
                                                $total = 0; // No quantity, total remains 0
                                            @endphp
                                        @endif
                                    @endforeach
                                @endif

                                @php
                                    $isCondFullfilled = $ongoing && $isExpired && $totalActual < 101 ? true : false;
                                @endphp



                                <tr class="{{ $isCondFullfilled ? 'expired' : '' }}">
                                    <td>
                                        <div class="dropdown d-lg-block d-sm-block d-md-block">
                                            <button class="btn btn-icon navbar-toggler" type="button"
                                                id="tableActionDropdown" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i data-feather="align-justify" class="font-medium-5"></i>
                                            </button>
                                            <!-- dropdown menu -->
                                            <div class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="tableActionDropdown">
                                                @if ($project->karyawan && $project->team)
                                                    <a class="open-project-mw dropdown-item d-flex align-items-center"
                                                        project_id_value = "{{ $project->id_project }}"
                                                        client_id_value = "{{ $project->client !== null ? $project->client->id_client : 0 }}"
                                                        href="{{ route('m.projects.getprjmondws') . '?projectID=' . $project->id_project }}">
                                                        <i data-feather="navigation" class="mr-1"
                                                            style="color: #288cc7;"></i>
                                                        Navigate
                                                    </a>
                                                @endif
                                                @if (auth()->user()->type === 'Superuser' || auth()->user()->id_karyawan == $project->karyawan->id_karyawan)
                                                    <a class="edit-record dropdown-item d-flex align-items-center"
                                                        project_id_value = "{{ $project->id_project }}"
                                                        karyawan_id_value = "{{ $project->karyawan !== null ? $project->karyawan->id_karyawan : 0 }}"
                                                        client_id_value = "{{ $project->client !== null ? $project->client->id_client : 0 }}"
                                                        onclick="openModal('{{ $modalData['modal_edit'] }}')">
                                                        <i data-feather="edit" class="mr-1" style="color: #28c76f;"></i>
                                                        Edit
                                                    </a>
                                                @endif
                                                @if (auth()->user()->type === 'Superuser' || auth()->user()->id_karyawan == $project->karyawan->id_karyawan)
                                                    <a class="delete-record dropdown-item d-flex align-items-center"
                                                        project_id_value = "{{ $project->id_project }}"
                                                        onclick="openModal('{{ $modalData['modal_delete'] }}')">
                                                        <i data-feather="trash" class="mr-1" style="color: #ea5455;"></i>
                                                        Delete
                                                    </a>
                                                @endif

                                            </div>
                                            <!--/ dropdown menu -->
                                        </div>
                                    </td>

                                    <td>
                                        @if ($project->karyawan && $project->team)
                                            @if ($project->id_project)
                                                <div data-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-placement="bottom" data-original-title="Click to navigate!"
                                                    class="pull-up">
                                                    <a class="open-project-mw {{ $isCondFullfilled ? 'text-white' : '' }}"
                                                        project_id_value="{{ $project->id_project }}"
                                                        href="{{ route('m.projects.getprjmondws') . '?projectID=' . $project->id_project }}">
                                                        {{ $project->id_project ?: '-' }}
                                                    </a>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        @else
                                            @if ($project->id_project)
                                                <div data-toggle="tooltip" data-popup="tooltip-custom"
                                                    data-placement="bottom" data-original-title="Fill Project Co & Team!"
                                                    class="pull-up">
                                                    <a class="open-project-mw {{ $isCondFullfilled ? 'text-white' : '' }}"
                                                        project_id_value="{{ $project->id_project }}">
                                                        {{ $project->id_project ?: '-' }}
                                                    </a>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td class="{{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ $project->na_project ?: '-' }}</td>
                                    <td class="{{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ $project->client !== null ? $project->client->na_client : '-' }}</td>
                                    <td class="{{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ $project->karyawan !== null ? $project->karyawan->na_karyawan : '-' }}</td>
                                    <td class="text-center align-middle {{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ $project->team !== null ? $project->team->na_team : '-' }}</td>
                                    <td class="text-center align-middle {{ $isCondFullfilled ? 'text-white' : '' }}">
                                        @if ($project->monitor->isNotEmpty())
                                            @if ($project->prjstatus_beta() == 'FINISH' || $totalActual == 100)
                                                <span class="bg-success text-white rounded small" style="padding: 0.5rem">
                                                    <i class="fas fa-check-circle me-1 fa-md"></i>
                                                    {{ number_format($totalActual, 0) }}%
                                                </span>
                                            @elseif ($project->prjstatus_beta() == 'FINISH' || $totalActual > 100)
                                                <span class="bg-danger text-white rounded small" style="padding: 0.5rem">
                                                    <i class="far fa-engine-warning me-1 fa-md"></i>
                                                    {{ number_format($totalActual, 0) }}%
                                                </span>
                                            @else
                                                <span class="bg-warning text-white rounded small" style="padding: 0.5rem">
                                                    <i class="fas fa-hourglass-half me-1 fa-md"></i>
                                                    {{ number_format($totalActual, 0) }}%
                                                </span>
                                            @endif
                                        @else
                                            @if ($project->prjstatus_beta() != 'FINISH' || $totalActual == 0)
                                                <span class="bg-warning text-white rounded small" style="padding: 0.5rem">
                                                    <i class="fas fa-hourglass-half me-1 fa-md"></i> 0%
                                                </span>
                                            @endif
                                        @endif
                                    </td>



                                    <td class="text-center align-middle {{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ \Carbon\Carbon::parse($project->start_project)->isoFormat($cust_date_format) }}
                                    </td>
                                    <td class="text-center align-middle {{ $isCondFullfilled ? 'text-white' : '' }}">
                                        {{ \Carbon\Carbon::parse($project->deadline_project)->isoFormat($cust_date_format) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <!--/ TableAbsen Card -->



    </div>







    <!-- BEGIN: AddPrjModal--> @include('v_res.m_modals.userpanels.m_daftarproject.v_add_prjModal') <!-- END: AddPrjModal-->
    <!-- BEGIN: EditPrjModal--> @include('v_res.m_modals.userpanels.m_daftarproject.v_edit_prjModal') <!-- END: EditPrjModal-->
    <!-- BEGIN: DelPrjModal--> @include('v_res.m_modals.userpanels.m_daftarproject.v_del_prjModal') <!-- END: DelPrjModal-->
    @if ($reset_btn)
        <!-- BEGIN: ResetPrjModal--> @include('v_res.m_modals.userpanels.m_daftarproject.v_reset_prjModal') <!-- END: ResetPrjModal-->
    @endif
@endsection


@section('footer_page_js')
    <script src="{{ asset('public/theme/vuexy/app-assets/js/scripts/components/components-modals.js') }}"></script>

    <script>
        $(document).ready(function() {
            var lengthMenu = [10, 50, 100, 500, 1000, 2000, 3000]; // Length menu options

            var $table = $('#daftarProjectTable').DataTable({
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
                    targets: [0, 7, 8], // Specify the columns to hide
                    visible: false // Set visibility to false
                }],
                initComplete: function() {
                    $(this.api().column([0]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([5]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([6]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([7]).header()).addClass('cell-fit text-center align-middle');
                    $(this.api().column([8]).header()).addClass('cell-fit text-center align-middle');

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
                        <span class="dropdown-item d-flex justify-content-center align-content-center">Project Lists</span>
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



        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                $('.open-project-mw').on('click', function() {
                    var projectID = $(this).attr('project_id_value');
                    console.log("Navigate to Project-ID: " + projectID);
                });
            }, 200);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalId = 'edit_projectModal';
            const modalSelector = document.getElementById(modalId);
            const modalToShow = new bootstrap.Modal(modalSelector);
            const targetedModalForm = document.querySelector('#' + modalId + ' #edit_projectModalFORM');

            $(document).on('click', '.edit-record', function(event) {
                var prjID = $(this).attr('project_id_value');
                var prjName = $(this).attr('project_id_value');
                var clientID = $(this).attr('client_id_value');
                var prjCO = $(this).attr('karyawan_id_value');

                console.log('Edit button clicked for project_id:', prjID);

                setTimeout(() => {
                    $.ajax({
                        url: '{{ route('m.projects.getprj') }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Update the CSRF token here
                        },
                        data: {
                            prjID: prjID,
                            prjName: prjName,
                            clientID: clientID,
                            prjCO: prjCO
                        },
                        success: function(response) {
                            console.log(response);
                            $('#e-client-id').val(response.id_client);
                            $('#e-project-id').val(response.id_project);
                            $('#edit-project-id').val(response.id_project);
                            $('#edit-project-name').val(response.na_project);
                            $('#edit-co-id').val(response.id_karyawan);
                            $('#edit-na-co').val(response.na_karyawan);
                            $('#edit-start-deadline').val(response.start_deadline);
                            setClientList(response);
                            setTeamList(response);

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


            function setTeamList(response) {
                var teamSelect = $('#' + modalId +
                    ' #edit-team-id');
                teamSelect.empty(); // Clear existing options
                teamSelect.append($('<option>', {
                    value: "",
                    text: "Select Team"
                }));
                $.each(response.teamList, function(index,
                    teamOption) {
                    var option = $('<option>', {
                        value: teamOption.value,
                        text: `[${teamOption.value}] ${teamOption.text}`
                    });
                    if (teamOption.selected) {
                        option.attr('selected',
                            'selected'); // Select the option
                    }
                    teamSelect.append(option);
                });
            }


            function setClientList(response) {
                var clientSelect = $('#' + modalId +
                    ' #edit-client-id');
                clientSelect.empty(); // Clear existing options
                clientSelect.append($('<option>', {
                    value: "",
                    text: "Select Customer"
                }));
                $.each(response.clientList, function(index,
                    clientOption) {
                    var option = $('<option>', {
                        value: clientOption.value,
                        text: `[${clientOption.value}] ${clientOption.text}`
                    });
                    if (clientOption.selected) {
                        option.attr('selected',
                            'selected'); // Select the option
                    }
                    clientSelect.append(option);
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
        document.addEventListener('DOMContentLoaded', function() {
            whichModal = "delete_projectModal";
            const modalSelector = document.querySelector('#' + whichModal);
            const modalToShow = new bootstrap.Modal(modalSelector);

            setTimeout(() => {
                $('.delete-record').on('click', function() {
                    var projectID = $(this).attr('project_id_value');
                    $('#' + whichModal + ' #project_id').val(projectID);
                    modalToShow.show();
                });
            }, 200);

        });
    </script>
@endsection
