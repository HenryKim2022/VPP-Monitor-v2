 <div class="modal fade text-left modal-success" id="edit_projectModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel113" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="myModalLabel113">Edit Team Informations</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form class="row g-2 needs-validation mt-1" method="POST" action="{{ route('m.projects.edit') }}" id="edit_projectModalFORM"
                     novalidate>
                     @csrf
                     <input type="hidden" id="e-client-id" name="e-client-id" value="" />
                     <input type="hidden" id="e-project-id" name="e-project-id" value="" />
                     <div class="col-xl-12 col-md-12 col-12">
                        <div class="form-group">
                            <label class="form-label" for="edit-project-id">Project-ID</label>
                            <input class="form-control form-control-merge" id="edit-project-id" name="edit-project-id"
                                placeholder="e.g. e.g. PRJ-24-00001" aria-describedby="edit-project-id" tabindex="4"></input>
                        </div>
                    </div>
                     <div class="col-xl-12 col-md-12 col-12">
                        <div class="form-group">
                            <label class="form-label" for="edit-project-name">ProjectName</label>
                            <input class="form-control form-control-merge" id="edit-project-name" name="edit-project-name"
                                placeholder="e.g. CONTROL UNIT CU310 - 2DP TYPE 6SL3040..." aria-describedby="edit-project-name" tabindex="4"></input>
                        </div>
                    </div>
                     <div class="col-xl-12 col-md-12 col-12">
                        <div class="form-group mb-0">
                            <label>Customer</label>
                            <select class="select2 form-control form-control-lg" name="edit-client-id" id="edit-client-id">
                                <option value="">Select Customer</option>
                                {{-- generated from js json --}}
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-12 col-md-12 col-12 mt-1">
                        <div class="form-group">
                            <input type="hidden" id="edit-co-id" name="edit-co-id" value="{{ $co_auth[0] }}"></input>
                            <label class="form-label" for="edit-na-co">Project Co</label>
                            <input class="form-control form-control-merge" id="edit-na-co" name="edit-na-co" value="{{ $co_auth[1] }}"
                                placeholder="e.g. John Doe" aria-describedby="edit-na-co" tabindex="4" disabled readonly></input>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6 col-12 form-group">
                        <div class="form-group mb-0">
                            <label>Team</label>
                            <select class="select2 form-control form-control-lg" name="edit-team-id" id="edit-team-id">
                                <option value="">Select Team</option>
                                {{-- generated from js json --}}
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-6 col-md-6 col-12 form-group">
                        <label for="edit-start-deadline">Start - Deadline</label>
                        <input type="text" id="edit-start-deadline" name="edit-start-deadline" class="form-control flatpickr-range"
                            placeholder="YYYY-MM-DD to YYYY-MM-DD" />
                    </div>


                     <div class="col-12 mb-3 mt-2">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" id="bsvalidationcheckbox1" name="bsvalidationcheckbox1" data-default-checked="false" required />
                          <label class="form-check-label" for="bsvalidationcheckbox1">Proceed to save</label>
                          <div class="feedback text-muted">You must agree before saving.</div>
                        </div>
                      </div>
                     <div class="modal-footer w-100 px-0 py-1">
                         <div class="col-12 text-center">
                             <div class="d-flex flex-col justify-content-end">
                                 <button class="modal-btn btn btn-primary" data-dismiss="modal" id="confirmCancel"
                                     type="button">Cancel</button>
                                 <button class="modal-btn btn btn-success ml-1" id="confirmSave" {{-- type="submit" --}}>Save</button>
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


