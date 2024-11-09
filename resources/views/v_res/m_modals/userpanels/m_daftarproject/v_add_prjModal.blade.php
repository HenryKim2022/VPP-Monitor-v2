<div class="modal fade text-left modal-success" id="add_projectModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel113" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel113">Add Project Informations</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('m.projects.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- $idKaryawanForAbsen  --}}
                    <!--  <img src="{ Session::get('data')['qr_code_link'] }}" alt="QR Code"> -->

                    <div class="row g-1">
                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="form-group">
                                <label class="form-label" for="project-id">Project-ID</label>
                                <input class="form-control form-control-merge" id="project-id" name="project-id"
                                    placeholder="e.g. PRJ-24-00001" aria-describedby="project-id"
                                    tabindex="4"></input>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="form-group">
                                <label class="form-label" for="project-name">ProjectName</label>
                                <input class="form-control form-control-merge" id="project-name" name="project-name"
                                    placeholder="e.g. CONTROL UNIT CU310 - 2DP TYPE 6SL3040..."
                                    aria-describedby="project-name" tabindex="4"></input>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="form-group mb-0">
                                <label>Customer</label>
                                <select class="select2 form-control form-control-lg" name="client-id" id="client-id">
                                    <option value="">Select Customer</option>
                                    @foreach ($client_list as $client)
                                        <option value="{{ $client->id_client }}">
                                            {{ $client->na_client }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 col-12 mt-1">
                            <div class="form-group">
                                <input type="hidden" id="co-id" name="co-id" value="{{ $co_auth[0] }}"></input>
                                <label class="form-label" for="na-co">Project Co</label>
                                <input class="form-control form-control-merge" id="na-co" name="na-co"
                                    value="{{ $co_auth[1] }}" placeholder="e.g. John Doe" aria-describedby="na-co"
                                    tabindex="4" disabled readonly></input>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 col-12 form-group">
                            <div class="form-group mb-0">
                                <label>Team</label>
                                <select class="select2 form-control form-control-lg" name="team-id" id="team-id">
                                    <option value="">Select Team</option>
                                    @foreach ($team_list as $team)
                                        <option value="{{ $team->id_team }}">
                                            {{ $team->na_team }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-xl-6 col-md-6 col-12 form-group">
                            <label for="start-deadline">Start - Deadline</label>
                            <input type="text" id="start-deadline" name="start-deadline" class="form-control flatpickr-range"
                                placeholder="YYYY-MM-DD to YYYY-MM-DD" />
                        </div>


                        <div class="col-xl-12 col-md-12 col-12 mt-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


            <div class="modal-footer d-none">
                <button type="button" class="btn btn-success" data-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>
