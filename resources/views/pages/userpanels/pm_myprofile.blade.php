@php
    $page = Session::get('page');
    $page_title = $page['page_title'];
    $cust_date_format = $page['custom_date_format'];
    $authenticated_user_data = Session::get('authenticated_user_data');
    // dd($authenticated_user_data->toArray());

    $typeValues = ['Public', 'Client', 'Superuser', 'Supervisor', 'Engineer']; // Convert the text type User (e.g Admin to 1) value to its numeric representation
    $typeIndex = array_search(auth()->user()->type, $typeValues);
    $convertedUserType = $typeIndex !== false ? $typeIndex : null;

@endphp


@php
    // Initialize avatar_src to default avatar
    $default_avatar_path = 'public/assets/defaults/avatar_default.png';
    $avatar_src = asset($default_avatar_path);

    // Check if the authenticated user is a karyawan or client and set avatar_src accordingly
    if (
        isset($authenticated_user_data['foto_karyawan']) &&
        !empty($authenticated_user_data['foto_karyawan']) &&
        file_exists(public_path('avatar/uploads/' . $authenticated_user_data['foto_karyawan']))
    ) {
        // For karyawan
        $avatar_src = asset('public/avatar/uploads/' . $authenticated_user_data['foto_karyawan']);
    } elseif (
        isset($authenticated_user_data['foto_client']) &&
        !empty($authenticated_user_data['foto_client']) &&
        file_exists(public_path('avatar/uploads/' . $authenticated_user_data['foto_client']))
    ) {
        // For client
        $avatar_src = asset('public/avatar/uploads/' . $authenticated_user_data['foto_client']);
    }

    // Check session for user_image only if the avatar_src is still the default
    if ($avatar_src === asset($default_avatar_path)) {
        // Get the user_image from session
        $session_user_image = Session::get('user_image');
        // If session_user_image is available, use it as avatar_src
        if ($session_user_image) {
            $avatar_src = $session_user_image;
        }
    } else {
        // If a valid user avatar exists, store it in the session
        Session::put('user_image', $avatar_src);
    }
@endphp


@extends('layouts.userpanels.v_main')
@section('header_page_cssjs')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/theme/vuexy/app-assets/css/pages/page-profile.css') }}">
@endsection


@section('page-content')
    {{-- @if (auth()->user()->type == 'Superuser' || auth()->user()->type == 'Supervisor')
        <h1>HI MIN :)</h1>
    @endif

    @if (auth()->user()->type == 'Engineer')
        <h1>HI WAN :)</h1>
    @endif --}}


    {{-- <div class="container row match-height pr-0"> --}}
    <div class="container match-height px-0">


        @if (auth()->user()->type != '')
            <div class="content-body">
                <!-- profile header -->
                <div id="user-profile">
                    <div class="row">
                        <div class="col-12 pr-1">
                            <div class="card profile-header mb-2">
                                <!-- profile cover photo -->
                                <img class="card-img-top"
                                    src="{{ asset('public/theme/vuexy/app-assets/images/profile/user-uploads/timeline.jpg') }}"
                                    alt="User Profile Image" />
                                <!--/ profile cover photo -->

                                <div class="position-relative">
                                    <!-- profile picture -->
                                    <div class="profile-img-container d-flex align-items-center">
                                        <style>
                                            .profile-img {
                                                width: 2rem;
                                                /* Adjust the width as needed */
                                                height: 2rem;
                                                /* Adjust the height as needed */
                                                overflow: hidden;
                                            }

                                            .profile-img img {
                                                width: 100%;
                                                height: 100%;
                                                object-fit: fill;
                                                object-position: center center;
                                            }
                                        </style>

                                        <div class="profile-img">
                                            <img src="{{ $avatar_src }}" class="rounded img-fluid hover-qr-image"
                                                alt="Card image" />
                                        </div>
                                        <!-- profile title -->
                                        <div class="profile-title ml-3">
                                            <h2 class="text-white ellipsis-v1" style="max-width: 45vw;"> <!-- Set a max-width here -->
                                                {{-- {{ $authenticated_user_data->na_client ? $authenticated_user_data->na_karyawan : 'Public User' }} --}}
                                                {{ isset($authenticated_user_data) ? ($authenticated_user_data->na_karyawan ?: $authenticated_user_data->na_client) : 'No Name' }}
                                            </h2>

                                            {{-- @php
                                                dd($authenticated_user_data->toArray());
                                            @endphp --}}

                                            @php
                                                $roles = [];

                                                // $role1 = $authenticated_user_data->daftar_login
                                                //     ? ($authenticated_user_data->daftar_login->type ==
                                                //     'Supervisor'
                                                //         ? 'WebSite ' .
                                                //             $authenticated_user_data->daftar_login->type
                                                //         : $authenticated_user_data->daftar_login->type)
                                                //     : null;

                                                $role1 = $authenticated_user_data->daftar_login_4get
                                                    ? ($authenticated_user_data->daftar_login_4get->type == 'Superuser'
                                                        ? 'WebSite ' . $authenticated_user_data->daftar_login_4get->type
                                                        : $authenticated_user_data->daftar_login_4get->type)
                                                    : ($authenticated_user_data->daftar_login
                                                        ? ($authenticated_user_data->daftar_login->type == 'Superuser'
                                                            ? 'WebSite ' . $authenticated_user_data->daftar_login->type
                                                            : $authenticated_user_data->daftar_login->type)
                                                        : null);

                                                $role2 = $authenticated_user_data->jabatan;

                                                // Add the first role to the roles array
                                                if ($role1) {
                                                    $roles[] = $role1;
                                                }

                                                // Add the additional roles from the jabatan relationship
                                                if ($role2) {
                                                    foreach ($role2 as $role) {
                                                        $roles[] = $role->na_jabatan;
                                                    }
                                                }

                                                $allRoles = implode(' & ', $roles);
                                            @endphp

                                            <p class="text-white">
                                                {{ $allRoles ?: 'Pasukan Rendang' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- tabs pill -->
                                <div class="profile-header-nav">
                                    <!-- navbar -->
                                    <nav
                                        class="navbar navbar-expand-md navbar-light justify-content-end justify-content-md-between w-100">
                                        <button class="btn btn-icon navbar-toggler" type="button"
                                            aria-controls="navbarSupportedContent" aria-expanded="false"
                                            aria-label="Toggle navigation">
                                            <i data-feather="align-justify" class="font-medium-5"></i>
                                        </button>
                                        {{-- <button class="btn btn-icon navbar-toggler" type="button" data-toggle="collapse"
                                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                        aria-expanded="false" aria-label="Toggle navigation">
                                        <i data-feather="align-justify" class="font-medium-5"></i>
                                    </button> --}}

                                        <!-- collapse  -->
                                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                            <div
                                                class="profile-tabs d-flex justify-content-between flex-wrap mt-1 mt-md-0 p-2">
                                            </div>
                                        </div>
                                        <!--/ collapse  -->
                                    </nav>
                                    <!--/ navbar -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ profile header -->

                <!-- account setting page -->
                <section id="page-account-settings">
                    <div class="row">
                        <!-- left menu section -->
                        <div class="col-md-3 pr-sm-2">
                            <ul class="nav nav-pills flex-column nav-left">
                                <!-- information -->
                                <li class="nav-item">
                                    <a class="nav-link active" id="account-pill-info" data-toggle="pill"
                                        href="#account-vertical-profile" aria-expanded="true">
                                        <i data-feather="user" class="font-medium-3 mr-1"></i>
                                        <span class="font-weight-bold">Profile</span>
                                    </a>
                                </li>

                                <!-- general -->
                                <li class="nav-item">
                                    <a class="nav-link" id="account-pill-general" data-toggle="pill"
                                        href="#account-vertical-general" aria-expanded="false">
                                        <i data-feather="edit" class="font-medium-3 mr-1"></i>
                                        <span class="font-weight-bold">Edit BioData</span>
                                    </a>
                                </li>
                                <!-- change password -->
                                <li class="nav-item">
                                    <a class="nav-link" id="account-pill-password" data-toggle="pill"
                                        href="#account-vertical-password" aria-expanded="false">
                                        <i data-feather="lock" class="font-medium-3 mr-1"></i>
                                        <span class="font-weight-bold">Change Account Auth</span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                        <!--/ left menu section -->

                        <!-- right content section -->
                        <div class="col-md-9 ml-0 ml-sm-3 ml-md-0 pr-1">
                            <div class="card">
                                <div class="card-body">
                                    <div class="tab-content">

                                        @if (auth()->user()->type != 'Client')
                                            <div class="tab-pane active" id="account-vertical-profile" role="tabpanel"
                                                aria-labelledby="account-pill-info" aria-expanded="false">
                                                <div class="container">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>UserID</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>
                                                                    {{-- {{ $authenticated_user_data->daftar_login->user_id }} --}}
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->user_id : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->user_id : '') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>EmployeeID</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>{{ $authenticated_user_data->id_karyawan }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Username</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>
                                                                    {{-- {{ $authenticated_user_data->daftar_login->username }} --}}
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->username : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->username : '') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Name</strong></td>
                                                                <td class="pl-2">: </td>
                                                                {{-- <td>{{ $authenticated_user_data->na_client :? $authenticated_user_data->na_karyawan }}</td> --}}
                                                                {{-- <td>
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->na_karyawan : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->na_karyawan : '') }}
                                                                </td> --}}
                                                                <td>
                                                                    {{ $authenticated_user_data->na_client ?: $authenticated_user_data->na_karyawan }}
                                                                </td>
                                                            </tr>

                                                            @if (auth()->user()->type != 'Client')
                                                                <tr>
                                                                    <td><strong>Office Role</strong></td>
                                                                    <td class="pl-2">: </td>
                                                                    <td>
                                                                        @php
                                                                            $rolesCount = $authenticated_user_data->jabatan->count();
                                                                        @endphp

                                                                        @if ($rolesCount > 0)
                                                                            @foreach ($authenticated_user_data->jabatan as $index => $role)
                                                                                {{ $role->na_jabatan }}@if ($index < $rolesCount - 1)
                                                                                    ,
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            N/A
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            <tr>
                                                                <td><strong>Email</strong></td>
                                                                <td class="pl-2">: </td>
                                                                {{-- <td>{{ $authenticated_user_data->daftar_login->email }}</td> --}}
                                                                <td>
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->email : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->email : '') }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <br><br><br><br><br> --}}
                                                </div>
                                            </div>

                                            <!-- general tab -->
                                            <div role="tabpanel" class="tab-pane" id="account-vertical-general"
                                                aria-labelledby="account-pill-general" aria-expanded="true">
                                                <!-- header media -->
                                                <div class="media">
                                                    <a href="javascript:void(0);" class="mr-25">
                                                        <img src="{{ $avatar_src }}" id="account-upload-img"
                                                            class="rounded hover-qr-image mr-50" alt="profile image"
                                                            height="80" width="80" />
                                                    </a>
                                                    <!-- upload and reset button -->
                                                    <div class="media-body mt-75 ml-1">
                                                        <label for="account-upload"
                                                            class="btn btn-sm btn-primary mb-75 mr-75">Upload</label>
                                                        <input type="file" id="account-upload" hidden
                                                            accept="image/png, image/jpeg, image/*" />
                                                        <button
                                                            class="btn btn-sm acc-avatar-reset btn-outline-secondary mb-75">Reset</button>
                                                        <p>Allowed JPG, GIF or PNG. Max size of 800kB</p>
                                                    </div>
                                                    <!--/ upload and reset button -->
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const uploadInput = document.getElementById('account-upload');
                                                        const uploadedAvatar = document.getElementById('account-upload-img');
                                                        const userId = '{{ $authenticated_user_data->id_karyawan }}';

                                                        uploadInput.addEventListener('change', function() {
                                                            const file = uploadInput.files[0];
                                                            const reader = new FileReader();

                                                            reader.onload = function(e) {
                                                                const uploadedImage = e.target.result;
                                                                uploadedAvatar.src = uploadedImage;

                                                                const formData = new FormData();
                                                                formData.append('id_karyawan', userId);
                                                                formData.append('foto_karyawan', file);

                                                                const xhr = new XMLHttpRequest();
                                                                xhr.open('POST', '{{ route('userPanels.avatar.edit') }}');
                                                                xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');
                                                                xhr.onload = function() {
                                                                    const response = JSON.parse(xhr.responseText);
                                                                    if (response.reload) {
                                                                        window.location.reload();
                                                                    }
                                                                };
                                                                xhr.send(formData);
                                                            };

                                                            reader.readAsDataURL(file);
                                                        });


                                                        var userProfilePhotoPreview = uploadedAvatar;
                                                        var userProfilePhotoInput = uploadInput;
                                                        userProfilePhotoInput.addEventListener('change', function() {
                                                            const file = this.files[0];
                                                            if (file && file.type.startsWith('image/')) {
                                                                const img = document.createElement('img');
                                                                img.src = URL.createObjectURL(file);

                                                                img.onload = function() {
                                                                    userProfilePhotoPreview.src = img.src;
                                                                };
                                                            }
                                                        });
                                                        var resetButton = document.querySelector('.acc-avatar-reset');
                                                        resetButton.addEventListener('click', function() {
                                                            userProfilePhotoPreview.src =
                                                                '{{ isset($authenticated_user_data['foto_karyawan']) && !empty($authenticated_user_data['foto_karyawan']) ? asset('public/avatar/uploads/' . $authenticated_user_data['foto_karyawan']) : (isset($authenticated_user_data['foto_client']) && !empty($authenticated_user_data['foto_client']) ? asset('public/avatar/uploads/' . $authenticated_user_data['foto_client']) : asset('public/assets/defaults/avatar_default.png')) }}';
                                                            userProfilePhotoInput.value = null;
                                                        });
                                                    });
                                                </script>
                                                <!--/ header media -->




                                                <!-- form -->
                                                <form class="validate-form mt-2"
                                                    action="{{ route('userPanels.biodata.edit') }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <input type="hidden" class="form-control"
                                                                    id="id_karyawan" name="id_karyawan" placeholder="ID"
                                                                    value="{{ $authenticated_user_data->id_karyawan ?: '0000000000' }}" />
                                                                {{-- value="{{ $authenticated_user_data->daftar_login ? ($authenticated_user_data->daftar_login->id_karyawan == null ? $authenticated_user_data->daftar_login->id_karyawan : $authenticated_user_data->daftar_login->id_karyawan) : null }}" /> --}}

                                                                <label for="account-name">Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="account-name" name="account-name"
                                                                    placeholder="Name"
                                                                    value="{{ $authenticated_user_data->na_karyawan ?: 'Not filled!' }}" />
                                                                {{-- value="{{ $authenticated_user_data->daftar_login ? ($authenticated_user_data->daftar_login->na_karyawan == null ? $authenticated_user_data->daftar_login->na_karyawan : $authenticated_user_data->daftar_login->na_karyawan) : null }}" /> --}}
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="birth-loc">Birth Location</label>
                                                                <input type="text" class="form-control" id="brith-loc"
                                                                    name="birth-loc" placeholder="Location of Birth"
                                                                    value="{{ $authenticated_user_data->tlah_karyawan ?: 'Not filled!' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="birth-date">Birth Date</label>
                                                                <input type="date"
                                                                    class="form-control pickadate-birth-date"
                                                                    id="birth-date" name="birth-date"
                                                                    placeholder="Date of Birth"
                                                                    value="{{ $authenticated_user_data->tglah_karyawan ? \Carbon\Carbon::parse($authenticated_user_data->tglah_karyawan)->format('Y-m-d') : '' }}" />
                                                            </div>
                                                        </div>


                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label>Religion</label>
                                                                <select class="select2 form-control form-control-lg"
                                                                    name="religion" id="religion">
                                                                    @php
                                                                        $religion =
                                                                            $authenticated_user_data->agama_karyawan;
                                                                    @endphp
                                                                    <option value=""
                                                                        {{ !$religion ? 'selected' : '' }}>
                                                                        Select religion</option>
                                                                    <option value="Islam"
                                                                        {{ $religion == 'Islam' ? 'selected' : '' }}>
                                                                        Islam</option>
                                                                    <option value="Kristen"
                                                                        {{ $religion == 'Kristen' ? 'selected' : '' }}>
                                                                        Kristen</option>
                                                                    <option value="Hindu"
                                                                        {{ $religion == 'Hindu' ? 'selected' : '' }}>
                                                                        Hindu</option>
                                                                    <option value="Buddha"
                                                                        {{ $religion == 'Buddha' ? 'selected' : '' }}>
                                                                        Buddha</option>
                                                                    <option value="Konghucu"
                                                                        {{ $religion == 'Konghucu' ? 'selected' : '' }}>
                                                                        Konghucu</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="address">Address</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" placeholder="Address"
                                                                    value="{{ $authenticated_user_data->alamat_karyawan ?: 'Not filled!' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="notelp">No.Telp</label>
                                                                <input type="text" class="form-control" id="notelp"
                                                                    name="notelp" placeholder="No. Telp"
                                                                    value="{{ $authenticated_user_data->notelp_karyawan ?: '+62 ' }}" />
                                                            </div>
                                                        </div>



                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-primary mt-2 mr-1">Save
                                                                changes</button>
                                                            <button type="reset"
                                                                class="btn btn-outline-secondary mt-2">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--/ form -->
                                            </div>
                                            <!--/ general tab -->

                                            <!-- change password -->
                                            <div class="tab-pane fade" id="account-vertical-password" role="tabpanel"
                                                aria-labelledby="account-pill-password" aria-expanded="false">
                                                <!-- form -->
                                                <form class="validate-form"
                                                    action="{{ route('userPanels.accdata.edit') }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <!-- HTML -->
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <input type="hidden" class="form-control" id="id"
                                                                    name="user_id" placeholder="ID" {{-- value="{{ $authenticated_user_data->daftar_login->user_id }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->user_id : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->user_id : '') }}" />
                                                                <input type="hidden" class="form-control" id="type"
                                                                    name="type" placeholder="TYPE"
                                                                    value="{{ $convertedUserType }}" />
                                                                <label for="account-username">Username</label>
                                                                <input type="text" class="form-control"
                                                                    id="account-username" name="username"
                                                                    placeholder="Username" {{-- value="{{ $authenticated_user_data->daftar_login->username }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->username : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->username : '') }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-e-mail">E-mail</label>
                                                                <input type="email" class="form-control"
                                                                    id="account-e-mail" name="email"
                                                                    placeholder="Email" {{-- value="{{ $authenticated_user_data->daftar_login->email }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->email : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->email : '') }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-retype-new-password"> New
                                                                    Password</label>
                                                                <div
                                                                    class="input-group form-password-toggle input-group-merge">
                                                                    <input type="password" class="form-control"
                                                                        id="account-retype-new-password"
                                                                        name="new-password" placeholder="New Password" />
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text cursor-pointer">
                                                                            <i data-feather="eye"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-retype-new-password">Retype New
                                                                    Password</label>
                                                                <div
                                                                    class="input-group form-password-toggle input-group-merge">
                                                                    <input type="password" class="form-control"
                                                                        id="account-retype-new-password"
                                                                        name="confirm-new-password"
                                                                        placeholder="Retype Password" />
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text cursor-pointer">
                                                                            <i data-feather="eye"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-primary mr-1 mt-1">Save
                                                                changes</button>
                                                            <button type="reset"
                                                                class="btn btn-outline-secondary mt-1">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--/ form -->
                                            </div>
                                            <!--/ change password -->
                                        @else
                                            <div class="tab-pane active" id="account-vertical-profile" role="tabpanel"
                                                aria-labelledby="account-pill-info" aria-expanded="false">
                                                <div class="container">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td><strong>UserID</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>
                                                                    {{-- {{ $authenticated_user_data->daftar_login->user_id }} --}}
                                                                    {{-- {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->user_id : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->user_id : '') }} --}}
                                                                    {{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->user_id : ($authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->user_id : '') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>ClientID</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>{{ $authenticated_user_data->id_client ?: $authenticated_user_data->id_karyawan }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Username</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>
                                                                    {{-- {{ $authenticated_user_data->daftar_login->username }} --}}
                                                                    {{-- {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->username : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->username : '') }} --}}
                                                                    {{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->username : ($authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->username : '') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Name</strong></td>
                                                                <td class="pl-2">: </td>
                                                                {{-- <td>{{ $authenticated_user_data->na_client ?: $authenticated_user_data->na_karyawan }}</td> --}}
                                                                {{-- <td>
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->na_karyawan : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->na_karyawan : '') }}
                                                                </td> --}}
                                                                {{-- <td>
                                                                    {{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->na_client : ($authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->na_karyawan : '') }}
                                                                </td> --}}

                                                                {{-- <td>{{ $authenticated_user_data->na_client ?: $authenticated_user_data->na_karyawan }}</td> --}}

                                                                {{-- <td>
                                                                    @if ($authenticated_user_data->na_client)
                                                                        {{ $authenticated_user_data->na_client }}
                                                                    @elseif($authenticated_user_data->na_karyawan)
                                                                        {{ $authenticated_user_data->na_karyawan }}
                                                                    @elseif($authenticated_user_data->daftar_login_4get)
                                                                        {{ $authenticated_user_data->daftar_login_4get->na_client }}
                                                                    @elseif($authenticated_user_data->daftar_login)
                                                                        {{ $authenticated_user_data->daftar_login->na_karyawan }}
                                                                    @else
                                                                        {{ '' }}
                                                                    @endif
                                                                </td> --}}

                                                                <td>{{ $authenticated_user_data->na_client ?: $authenticated_user_data->na_karyawan }}
                                                                </td>

                                                            </tr>



                                                            <tr>
                                                                <td><strong>Email</strong></td>
                                                                <td class="pl-2">: </td>
                                                                {{-- <td>{{ $authenticated_user_data->daftar_login->email }}</td> --}}
                                                                {{-- <td>
                                                                    {{ $authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->email : ($authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->email : '') }}
                                                                </td> --}}
                                                                <td>
                                                                    {{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->email : ($authenticated_user_data->daftar_login ? $authenticated_user_data->daftar_login->email : '') }}
                                                                </td>

                                                            </tr>


                                                            <tr>
                                                                <td><strong>Address</strong></td>
                                                                <td class="pl-2">: </td>
                                                                <td>
                                                                    {{ $authenticated_user_data->alamat_client ?: '-' }}
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    {{-- <br><br><br><br><br> --}}
                                                </div>
                                            </div>

                                            <!-- general tab -->
                                            <div role="tabpanel" class="tab-pane" id="account-vertical-general"
                                                aria-labelledby="account-pill-general" aria-expanded="true">
                                                <!-- header media -->
                                                <div class="media">
                                                    <a href="javascript:void(0);" class="mr-25">
                                                        <img src="{{ $authenticated_user_data->foto_karyawan == null ? env('APP_DEFAULT_AVATAR') : 'public/avatar/uploads/' . $authenticated_user_data->foto_karyawan }}"
                                                            id="account-upload-img" class="rounded hover-qr-image mr-50"
                                                            alt="profile image" height="80" width="80" />
                                                    </a>
                                                    <!-- upload and reset button -->
                                                    <div class="media-body mt-75 ml-1">
                                                        <label for="account-upload"
                                                            class="btn btn-sm btn-primary mb-75 mr-75">Upload</label>
                                                        <input type="file" id="account-upload" hidden
                                                            accept="image/png, image/jpeg, image/*" />
                                                        <button
                                                            class="btn btn-sm acc-avatar-reset btn-outline-secondary mb-75">Reset</button>
                                                        <p>Allowed JPG, GIF or PNG. Max size of 800kB</p>
                                                    </div>
                                                    <!--/ upload and reset button -->
                                                </div>
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const uploadInput = document.getElementById('account-upload');
                                                        const uploadedAvatar = document.getElementById('account-upload-img');
                                                        const userId = '{{ $authenticated_user_data->id_karyawan }}';

                                                        uploadInput.addEventListener('change', function() {
                                                            const file = uploadInput.files[0];
                                                            const reader = new FileReader();

                                                            reader.onload = function(e) {
                                                                const uploadedImage = e.target.result;
                                                                uploadedAvatar.src = uploadedImage;

                                                                const formData = new FormData();
                                                                formData.append('id_karyawan', userId);
                                                                formData.append('foto_karyawan', file);

                                                                const xhr = new XMLHttpRequest();
                                                                xhr.open('POST', '{{ route('userPanels.avatar.edit') }}');
                                                                xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');
                                                                xhr.onload = function() {
                                                                    const response = JSON.parse(xhr.responseText);
                                                                    if (response.reload) {
                                                                        window.location.reload();
                                                                    }
                                                                };
                                                                xhr.send(formData);
                                                            };

                                                            reader.readAsDataURL(file);
                                                        });


                                                        var userProfilePhotoPreview = uploadedAvatar;
                                                        var userProfilePhotoInput = uploadInput;
                                                        userProfilePhotoInput.addEventListener('change', function() {
                                                            const file = this.files[0];
                                                            if (file && file.type.startsWith('image/')) {
                                                                const img = document.createElement('img');
                                                                img.src = URL.createObjectURL(file);

                                                                img.onload = function() {
                                                                    userProfilePhotoPreview.src = img.src;
                                                                };
                                                            }
                                                        });
                                                        var resetButton = document.querySelector('.acc-avatar-reset');
                                                        resetButton.addEventListener('click', function() {
                                                            userProfilePhotoPreview.src =
                                                                '{{ $authenticated_user_data->foto_karyawan == null ? env('APP_DEFAULT_AVATAR') : 'public/avatar/uploads/' . $authenticated_user_data->foto_karyawan }}';
                                                            userProfilePhotoInput.value = null;
                                                        });
                                                    });
                                                </script>
                                                <!--/ header media -->




                                                <!-- form -->
                                                <form class="validate-form mt-2"
                                                    action="{{ route('userPanels.biodata.edit') }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <input type="hidden" class="form-control"
                                                                    id="id_karyawan" name="id_karyawan" placeholder="ID"
                                                                    value="{{ $authenticated_user_data->id_client ?: '0000000000' }}" />
                                                                {{-- value="{{ $authenticated_user_data->daftar_login ? ($authenticated_user_data->daftar_login->id_karyawan == null ? $authenticated_user_data->daftar_login->id_karyawan : $authenticated_user_data->daftar_login->id_karyawan) : null }}" /> --}}

                                                                <label for="account-name">Name</label>
                                                                <input type="text" class="form-control"
                                                                    id="account-name" name="account-name"
                                                                    placeholder="Name"
                                                                    value="{{ $authenticated_user_data->na_client ?: 'Not filled!' }}" />
                                                                {{-- value="{{ $authenticated_user_data->daftar_login ? ($authenticated_user_data->daftar_login->na_karyawan == null ? $authenticated_user_data->daftar_login->na_karyawan : $authenticated_user_data->daftar_login->na_karyawan) : null }}" /> --}}
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="birth-loc">Birth Location</label>
                                                                <input type="text" class="form-control"
                                                                    id="brith-loc" name="birth-loc"
                                                                    placeholder="Location of Birth"
                                                                    value="{{ $authenticated_user_data->tlah_karyawan ?: 'Not filled!' }}" />
                                                            </div>
                                                        </div> --}}
                                                        {{-- <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="birth-date">Birth Date</label>
                                                                <input type="date" class="form-control"
                                                                    id="brith-date" name="birth-date"
                                                                    placeholder="Date of Birth"
                                                                    value="{{ $authenticated_user_data->tglah_karyawan ?: 'Not filled!' }}" />
                                                            </div>
                                                        </div> --}}
                                                        {{-- <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label>Religion</label>
                                                                <select class="select2 form-control form-control-lg"
                                                                    name="religion" id="religion">
                                                                    @php
                                                                        $religion =
                                                                            $authenticated_user_data->agama_karyawan;
                                                                    @endphp
                                                                    <option value=""
                                                                        {{ !$religion ? 'selected' : '' }}>
                                                                        Select religion</option>
                                                                    <option value="Islam"
                                                                        {{ $religion == 'Islam' ? 'selected' : '' }}>
                                                                        Islam</option>
                                                                    <option value="Kristen"
                                                                        {{ $religion == 'Kristen' ? 'selected' : '' }}>
                                                                        Kristen</option>
                                                                    <option value="Hindu"
                                                                        {{ $religion == 'Hindu' ? 'selected' : '' }}>
                                                                        Hindu</option>
                                                                    <option value="Buddha"
                                                                        {{ $religion == 'Buddha' ? 'selected' : '' }}>
                                                                        Buddha</option>
                                                                    <option value="Konghucu"
                                                                        {{ $religion == 'Konghucu' ? 'selected' : '' }}>
                                                                        Konghucu</option>
                                                                </select>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="address">Address</label>
                                                                <input type="text" class="form-control" id="address"
                                                                    name="address" placeholder="Address"
                                                                    value="{{ $authenticated_user_data->alamat_client ?: 'Not filled!' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="notelp">No.Telp</label>
                                                                <input type="text" class="form-control" id="notelp"
                                                                    name="notelp" placeholder="No. Telp"
                                                                    value="{{ $authenticated_user_data->notelp_client ?: '+62 ' }}" />
                                                            </div>
                                                        </div>



                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-primary mt-2 mr-1">Save
                                                                changes</button>
                                                            <button type="reset"
                                                                class="btn btn-outline-secondary mt-2">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--/ form -->
                                            </div>
                                            <!--/ general tab -->

                                            <!-- change password -->
                                            <div class="tab-pane fade" id="account-vertical-password" role="tabpanel"
                                                aria-labelledby="account-pill-password" aria-expanded="false">
                                                <!-- form -->
                                                <form class="validate-form"
                                                    action="{{ route('userPanels.accdata.edit') }}" method="POST">
                                                    @csrf
                                                    <div class="row">
                                                        <!-- HTML -->
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <input type="hidden" class="form-control" id="id"
                                                                    name="user_id" placeholder="ID" {{-- value="{{ $authenticated_user_data->daftar_login->user_id }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->user_id : '' }}" />
                                                                <input type="hidden" class="form-control" id="type"
                                                                    name="type" placeholder="TYPE"
                                                                    value="{{ $convertedUserType }}" />
                                                                <label for="account-username">Username</label>
                                                                <input type="text" class="form-control"
                                                                    id="account-username" name="username"
                                                                    placeholder="Username" {{-- value="{{ $authenticated_user_data->daftar_login->username }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->username : '' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-e-mail">E-mail</label>
                                                                <input type="email" class="form-control"
                                                                    id="account-e-mail" name="email"
                                                                    placeholder="Email" {{-- value="{{ $authenticated_user_data->daftar_login->email }}" /> --}}
                                                                    value="{{ $authenticated_user_data->daftar_login_4get ? $authenticated_user_data->daftar_login_4get->email : '' }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-retype-new-password"> New
                                                                    Password</label>
                                                                <div
                                                                    class="input-group form-password-toggle input-group-merge">
                                                                    <input type="password" class="form-control"
                                                                        id="account-retype-new-password"
                                                                        name="new-password" placeholder="New Password" />
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text cursor-pointer">
                                                                            <i data-feather="eye"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="account-retype-new-password">Retype New
                                                                    Password</label>
                                                                <div
                                                                    class="input-group form-password-toggle input-group-merge">
                                                                    <input type="password" class="form-control"
                                                                        id="account-retype-new-password"
                                                                        name="confirm-new-password"
                                                                        placeholder="Retype Password" />
                                                                    <div class="input-group-append">
                                                                        <div class="input-group-text cursor-pointer">
                                                                            <i data-feather="eye"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-primary mr-1 mt-1">Save
                                                                changes</button>
                                                            <button type="reset"
                                                                class="btn btn-outline-secondary mt-1">Cancel</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!--/ form -->
                                            </div>
                                            <!--/ change password -->
                                        @endif


                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ right content section -->
                    </div>
                </section>
                <!-- / account setting page -->

            </div>
        @else
            We're sorry, you're not have enought autorization to access this page :(
        @endif



    </div>

@endsection


@section('footer_page_js')
    <script src="{{ asset('public/theme/vuexy/app-assets/js/scripts/components/components-modals.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#daftarLoginKaryawanTable').DataTable({
                lengthMenu: [5, 10, 15, 20, 25, 50, 100, 150, 200, 250],
                pageLength: 10,
                responsive: true,
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
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
    </script>


    <script>
        var bdayDatePickerInput = document.querySelector(".pickadate-birth-date");
        if (bdayDatePickerInput) {
            flatpickr(bdayDatePickerInput, {
                enableTime: false,
                dateFormat: "Y-m-d", // Format for the date and time
                altInput: true,
                altFormat: "j F, Y", // Format for the alternative input display
                allowInput: true, // Allow typing in the input field
                minDate: "1900-01-01", // Set minimum date
                maxDate: "8000-12-31" // Set maximum date
            });

        }
    </script>
@endsection
