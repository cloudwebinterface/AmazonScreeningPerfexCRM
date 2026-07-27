<div class="col-lg-12">
    <div class="modal fade" id="CriminalChargesModal" tabindex="-1" role="dialog"
        aria-labelledby="CriminalChargesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="#" id="criminal-charges-form">
                    <div class="modal-header">
                        <h4 class="modal-title" id="CriminalChargesModalLabel"> Add
                            Criminal Charges</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                    
                        <!--  Description -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">
                                    <small class="req text-danger">*</small>
                                    Description
                                </label>
                                <input type="text" name="description" class="form-control description validate[required,maxSize[100]]">
                            </div>
                        </div>
                        <!--  Charge Level -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">
                                    <small class="req text-danger">*</small> Charge
                                    Level
                                </label>
                                <select class="form-control charge_level_id validate[required]" name="charge_level_id">
                                    <?php foreach ($chargeLevel as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?> - [<?php echo $key; ?>]</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--  Charge Disposition -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">
                                    <small class="req text-danger">*</small> Charge
                                    Disposition
                                </label>
                                <select class="form-control charge_disposition_id validate[required]" name="charge_disposition_id">
                                    <?php foreach ($dispositions as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?> - [<?php echo $key; ?>]</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!--  Disposition Date -->
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">
                                    Disposition Date
                                </label>
                                <input type="text" name="disposition_date" class="form-control disposition_date validate[required,custom[date]] input-date">
                            </div>
                        </div>

                        <!-- Sentences - sentences - CriminalSentence[] -->
                        <div>
                            <div class="col-lg-12">
                                <h4><b>Sentences</b></h4>
                            </div>
                            <!-- jail_time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Jail Time
                                    </label>
                                    <input type="text" name="sentences[0][jail_time]" class="form-control sentences-jail_time">
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
                                                <input type="radio" class="radio sentences-jail_suspended true" name="sentences[0][jail_suspended]" value="true">Yes
                                            </label>
                                            <label class="btn btn-default">
                                                <input type="radio" class="radio sentences-jail_suspended false" name="sentences[0][jail_suspended]" value="false">No
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
                                    <input type="text" name="sentences[0][jail_credit_time]" class="form-control sentences-jail_credit_time">
                                </div>
                            </div>
                            <!-- prison_time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Prison Time
                                    </label>
                                    <input type="text" name="sentences[0][prison_time]" class="form-control sentences-prison_time">
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
                                                <input type="radio" class="radio sentences-prison_suspended true" name="sentences[0][prison_suspended]" value="true">Yes
                                            </label>
                                            <label class="btn btn-default">
                                                <input type="radio" class="radio sentences-prison_suspended false" name="sentences[0][prison_suspended]" value="false">No
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
                                    <input type="text" name="sentences[0][prison_credit_time]" class="form-control sentences-prison_credit_time">
                                </div>
                            </div>
                            <!--  Probation Type -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Probation Type
                                    </label>
                                    <select class="form-control sentences-probation_type_id" name="sentences[0][probation_type_id]">
                                        <?php foreach ($probationTypes as $key => $value): ?>
                                            <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Probation Duration Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Probation Duration Time
                                    </label>
                                    <input type="text" name="sentences[0][probation_duration_time]" class="form-control sentences-probation_duration_time">
                                </div>
                            </div>

                            <!-- License Suspended Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        License Suspended Time
                                    </label>
                                    <input type="text" name="sentences[0][license_suspended_time]" class="form-control sentences-license_suspended_time">
                                </div>
                            </div>

                            <!-- Community Service Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Community Service Time
                                    </label>
                                    <input type="text" name="sentences[0][community_service_time]" class="form-control sentences-community_service_time">
                                </div>
                            </div>
                            <!-- fines -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Fines
                                    </label>
                                    <input type="number" name="sentences[0][fines]" class="form-control sentences-fines">
                                </div>
                            </div>
                            <!-- fees -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Fees
                                    </label>
                                    <input type="number" name="sentences[0][fees]" class="form-control sentences-fees">
                                </div>
                            </div>
                            <!-- costs -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Costs
                                    </label>
                                    <input type="number" name="sentences[0][costs]" class="form-control sentences-costs">
                                </div>
                            </div>
                            <!-- restitution -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Restitution
                                    </label>
                                    <input type="number" name="sentences[0][restitution]" class="form-control sentences-restitution">
                                </div>
                            </div>

                            <!-- Classes and Programs -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Classes and Programs
                                    </label>
                                    <input type="text" name="sentences[0][classes_and_programs]" class="form-control sentences-classes_and_programs">
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Additional Information
                                    </label>
                                    <textarea type="text" name="sentences[0][addl_information]" class="form-control sentences-addl_information"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Additional Dispositions - addl_disposition- CriminalAddition[] -->
                        <div>
                            <div class="col-lg-12">
                                <h4>Additional Dispositions</h4>
                            </div>
                            <!--  Date -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Date
                                    </label>
                                    <input type="text" name="addl_disposition[0][addition_date]" class="form-control addl_disposition-addition_date validate[custom[date]] input-date" >
                                </div>
                            </div>
                            <!--  Type -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Type
                                    </label>
                                    <select class="form-control addl_disposition-addition_type_id" name="addl_disposition[0][addition_type_id]">
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
                                    <select class="form-control addl_disposition-addition_action_id" name="addl_disposition[0][addition_action_id]">
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
                                    <input type="text" name="addl_disposition[0][jail_time]" class="form-control addl_disposition-jail_time">
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
                                                <input type="radio" name="addl_disposition[0][jail_suspended]" class="radio addl_disposition-jail_suspended true" value="true">Yes
                                            </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="addl_disposition[0][jail_suspended]" class="radio addl_disposition-jail_suspended false" value="false">No
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
                                    <input type="text" name="addl_disposition[0][jail_credit_time]" class="form-control addl_disposition-jail_credit_time">
                                </div>
                            </div>
                            <!-- Prison Value -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Prison Value
                                    </label>
                                    <input type="text" name="addl_disposition[0][prison_time]" class="form-control addl_disposition-prison_time">
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
                                                <input type="radio" name="addl_disposition[0][prison_suspended]" class="radio addl_disposition-prison_suspended true" value="true">Yes
                                            </label>
                                            <label class="btn btn-default">
                                                <input type="radio" name="addl_disposition[0][prison_suspended]" class="radio addl_disposition-prison_suspended false" value="false">No
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
                                    <input type="text" name="addl_disposition[0][prison_credit_time]" class="form-control addl_disposition-prison_credit_time">
                                </div>
                            </div>
                            <!-- Probation Duration Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Probation Duration Time
                                    </label>
                                    <input type="text" name="addl_disposition[0][probation_duration_time]" class="form-control addl_disposition-probation_duration_time">
                                </div>
                            </div>
                            <!--  License Suspended Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        License Suspended Time
                                    </label>
                                    <input type="text" name="addl_disposition[0][license_suspended_time]" class="form-control addl_disposition-license_suspended_time">
                                </div>
                            </div>
                            <!--  Community Service Time -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Community Service Time
                                    </label>
                                    <input type="text" name="addl_disposition[0][community_service_time]" class="form-control addl_disposition-community_service_time">
                                </div>
                            </div>
                            <!--  Other -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">
                                        Other
                                    </label>
                                    <textarea type="text" name="addl_disposition[0][other]" class="form-control addl_disposition-other"></textarea>
                                </div>
                            </div>

                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-add-criminal-charges">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>