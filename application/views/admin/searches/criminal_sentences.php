<div class="col-lg-12">
    <div class="modal fade" id="criminalsentenceModal" tabindex="-1" role="dialog"
        aria-labelledby="criminalsentenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="criminalsentenceModalLabel"> Add
                        New Sentence</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <!-- jail_time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Jail Time
                            </label>
                            <input type="text" name="sentences[jail_time]" class="form-control sentences-jail_time">
                        </div>
                    </div>
                    <!-- jail_suspended -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Jail Suspended
                            </label>
                            <div class="input-group">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default">
                                        <input type="radio" class="radio sentences-jail_suspended true" name="sentences[jail_suspended]" value="true">Yes
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="radio" class="radio sentences-jail_suspended false" name="sentences[jail_suspended]" value="false">No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- jail_credit_time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Jail Credit Time
                            </label>
                            <input type="text" name="sentences[jail_credit_time]" class="form-control sentences-jail_credit_time">
                        </div>
                    </div>
                    <!-- prison_time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Prison Time
                            </label>
                            <input type="text" name="sentences[prison_time]" class="form-control sentences-prison_time">
                        </div>
                    </div>
                    <!-- Prison Suspended -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Prison Suspended
                            </label>
                            <div class="input-group">
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default">
                                        <input type="radio" class="radio sentences-prison_suspended true" name="sentences[prison_suspended]" value="true">Yes
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="radio" class="radio sentences-prison_suspended false" name="sentences[prison_suspended]" value="false">No
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- prison_credit_time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Prison Credit Time
                            </label>
                            <input type="text" name="sentences[prison_credit_time]" class="form-control sentences-prison_credit_time">
                        </div>
                    </div>
                    <!--  Probation Type -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Probation Type
                            </label>
                            <select class="form-control sentences-probation_type_id" name="sentences[probation_type_id]">
                                <option value="1">SELECT</option>
                                <option value="2">FORMAL</option>
                                <option value="3">PROBATION VIOLATION
                                </option>
                                <option value="4">SUMMARY</option>
                                <option value="5">INFORMAL</option>
                                <option value="6">SUPERVISED</option>
                                <option value="7">UNSUPERVISED</option>
                                <option value="8">COURT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Probation Duration Time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Probation Duration Time
                            </label>
                            <input type="text" name="sentences[probation_duration_time]" class="form-control sentences-probation_duration_time">
                        </div>
                    </div>

                    <!-- License Suspended Time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                License Suspended Time
                            </label>
                            <input type="text" name="sentences[license_suspended_time]" class="form-control sentences-license_suspended_time">
                        </div>
                    </div>

                    <!-- Community Service Time -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Community Service Time
                            </label>
                            <input type="text" name="sentences[community_service_time]" class="form-control sentences-community_service_time">
                        </div>
                    </div>
                    <!-- fines -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Fines
                            </label>
                            <input type="number" name="sentences[fines]" class="form-control sentences-fines">
                        </div>
                    </div>
                    <!-- fees -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Fees
                            </label>
                            <input type="number" name="sentences[fees]" class="form-control sentences-fees">
                        </div>
                    </div>
                    <!-- costs -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Costs
                            </label>
                            <input type="number" name="sentences[costs]" class="form-control sentences-costs">
                        </div>
                    </div>
                    <!-- restitution -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Restitution
                            </label>
                            <input type="number" name="sentences[restitution]" class="form-control sentences-restitution">
                        </div>
                    </div>

                    <!-- Classes and Programs -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Classes and Programs
                            </label>
                            <input type="text" name="sentences[classes_and_programs]" class="form-control sentences-classes_and_programs">
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="control-label">
                                Additional Information
                            </label>
                            <textarea type="text" name="sentences[addl_information]" class="form-control sentences-addl_information"></textarea>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-add-dispositions">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>