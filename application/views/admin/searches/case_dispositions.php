<div class="col-lg-12">
    <div class="modal fade" id="casedispositionsModal" tabindex="-1" role="dialog"
        aria-labelledby="casedispositionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="case-disposition-form">
                    <div class="modal-header">
                        <h4 class="modal-title" id="casedispositionsModalLabel"> Add New Case Dispositions</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <!--  Date -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Date
                                </label>
                                <input type="text" name="addition_date" class="form-control validate[required,custom[date]] input-date">
                            </div>
                        </div>
                        <!--  Type -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Type
                                </label>
                                <select class="form-control validate[required]" id="addition_type_id" name="addition_type_id" >
                                    <?php foreach ($additionTypes as $key => $value): ?>
                                        <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--  Action -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Action
                                </label>
                                <select class="form-control validate[required]" id="addition_action_id" name="addition_action_id">
                                    <?php foreach ($additionActionTypes as $key => $value): ?>
                                        <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Jail Time -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Jail Time
                                </label>
                                <input type="text" name="jail_time" id="jail_time" class="form-control">
                            </div>
                        </div>
                        <!-- Jail Suspended -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Jail Suspended
                                </label>
                                <div class="input-group">
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default">
                                            <input type="radio" class="radio" name="jail_suspended" value="true">Yes
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" class="radio" autocomplete="off" name="jail_suspended" value="false">No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Jail Credit Time -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Jail Credit Time
                                </label>
                                <input type="text" name="jail_credit_time" id="jail_credit_time" class="form-control">
                            </div>
                        </div>
                        <!-- Prison Value -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Prison Value
                                </label>
                                <input type="text" name="prison_time" id="prison_time" class="form-control">
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
                                            <input type="radio" class="radio" name="prison_suspended" value="true">Yes
                                        </label>
                                        <label class="btn btn-default">
                                            <input type="radio" class="radio" autocomplete="off" name="prison_suspended"
                                                value="false">No
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Prison Credit Time-->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Prison Credit Time
                                </label>
                                <input type="text" name="prison_credit_time" id="prison_credit_time" class="form-control">
                            </div>
                        </div>
                        <!-- Probation Duration Time -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Probation Duration Time
                                </label>
                                <input type="text" name="probation_duration_time" id="probation_duration_time"
                                    class="form-control">
                            </div>
                        </div>
                        <!--  License Suspended Time -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    License Suspended Time
                                </label>
                                <input type="text" name="license_suspended_time" id="license_suspended_time"
                                    class="form-control">
                            </div>
                        </div>
                        <!--  Community Service Time -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="control-label">
                                    Community Service Time
                                </label>
                                <input type="text" name="community_service_time" id="community_service_time"
                                    class="form-control">
                            </div>
                        </div>
                        <!--  Other -->
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="control-label">
                                    Other
                                </label>
                                <textarea type="text" name="other" id="other" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-add-dispositions">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>