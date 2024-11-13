<div class="modal fade text-left modal-success" id="add_taskModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel113" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel113">Add
                    *{{ \Carbon\Carbon::parse($loadDataWS->working_date_ws)->isoFormat($cust_date_format) }} -
                    Task Informations
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('m.task.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-1">
                        <input type="hidden" class="form-control form-control-merge" id="ws-id_ws" name="ws-id_ws"
                            placeholder="filled by system" aria-describedby="ws-id_ws" tabindex="4"
                            value="{{ $loadDataWS->id_ws }}"></input>
                        <input type="hidden" class="form-control form-control-merge" id="ws-id_project"
                            name="ws-id_project" placeholder="filled by system" aria-describedby="ws-id_project"
                            tabindex="4" value="{{ $loadDataWS->id_project }}"></input>
                        <input type="hidden" class="form-control form-control-merge" id="ws-arrival_time"
                            name="ws-arrival_time" placeholder="filled by system" aria-describedby="ws-arrival_time"
                            tabindex="4" value="{{ $loadDataWS->arrival_time_ws }}"></input>
                        <input type="hidden" class="form-control form-control-merge" id="ws-finish_time"
                            name="ws-finish_time" placeholder="filled by system" aria-describedby="ws-finish_time"
                            tabindex="4" value="{{ $loadDataWS->finish_time_ws }}"></input>

                        @php
                            $currentTime = now();
                            $formattedTime = $currentTime->format('H:i:s');
                        @endphp
                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="form-group">
                                <label for="task-work_time">Working-Time</label>
                                <input type="time" id="task-work_time" name="task-work_time"
                                    class="form-control text-left flatpickr-time" value="{{ old('task-work_time', $formattedTime) }}"
                                    placeholder="HH:MM:SS AM/PM" />
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 col-12 mb-1">
                            <div class="form-group mb-0">
                                <label>Task</label>
                                <select class="select2 form-control form-control-lg" name="task-id_monitoring" id="task-id_monitoring">
                                    <option value="">Select Task</option>
                                    @foreach ($taskCategoryList as $task)
                                        <option value="{{ $task->id_monitoring }}"
                                            {{ old('task-id_monitoring') == $task->id_monitoring ? 'selected' : '' }}>
                                            {{ $task->category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xl-12 col-md-12 col-12">
                            <div class="form-group">
                                <label for="task-description">Description</label>
                                <textarea id="task-description" name="task-description" class="form-control" rows="3"
                                    placeholder="*- Doing abcd &#10;*- Doing efg&#10;*- Doing hijkl">{{ old('task-description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-xl-12 col-md-12 col-12 mb-2">
                            <div class="form-group">
                                <label for="task-current-progress">Current Progress%</label>
                                <input type="number" id="task-current-progress" name="task-current-progress" class="form-control"
                                    placeholder="eg: 2" value="{{ old('task-current-progress') }}">
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
