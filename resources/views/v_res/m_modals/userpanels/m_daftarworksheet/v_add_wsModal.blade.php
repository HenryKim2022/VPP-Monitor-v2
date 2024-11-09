<div class="modal fade text-left modal-success position-fixed" id="add_wsModal" data-bs-backdrop="static"
 {{-- tabindex="-1" --}}
    role="dialog" aria-labelledby="myModalLabel113" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel113">Add {{ $prjmondws->id_project }} Worksheet Informations
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('m.ws.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-1">
                        <input type="hidden" class="form-control form-control-merge" id="ws-id_karyawan"
                            name="ws-id_karyawan" placeholder="filled by system" aria-describedby="ws-id_karyawan"
                            tabindex="4" value="{{ auth()->user()->id_karyawan }}"></input>
                        <input type="hidden" class="form-control form-control-merge" id="ws-id_project"
                            name="ws-id_project" placeholder="filled by system" aria-describedby="ws-id_project"
                            tabindex="4" value="{{ $prjmondws->id_project }}"></input>

                        <div class="col-xl-6 col-md-6 col-12">
                            <div class="form-group">
                                <label for="ws-working_date">Working-Date</label>
                                <input type="date" class="form-control" id="ws-working_date"
                                    name="ws-working_date" placeholder="Date of Prj Started"
                                    value="{{ now()->toDateString() }}" />
                            </div>
                        </div>

                        @php
                            $currentTime = now();
                            $formattedTime = $currentTime->format('H:i:s');
                        @endphp
                        <div class="col-xl-6 col-md-6 col-12">
                            <div class="form-group">
                                <label for="ws-arrival_time">Arrival-Time</label>
                                <input type="time" id="ws-arrival_time" name="ws-arrival_time"
                                    class="form-control text-left flatpickr-time" value="{{ $formattedTime }}" placeholder="HH:MM:SS AM/PM" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 col-12">
                            <div class="form-group">
                                <label for="ws-finish_time">Finish-Time</label>
                                <input type="time" id="ws-finish_time" name="ws-finish_time"
                                    class="form-control text-left flatpickr-time" value="{{ $formattedTime }}" placeholder="HH:MM:SS AM/PM" />
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 col-12">
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
