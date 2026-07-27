<!-- Add Case -->
<div id="add-case-form" class="add-case-content">
    <?php echo form_open('admin/searches/update_search', array('id' => 'my-form')); ?>
    <h2><strong>Add Case</strong> <span class="close close-case pull-right">Close</span></h2>

    <div class="row search-info">
        <div class="title-section">
            <h3>Search Information</h3>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">Search ID:</label>
                <span><?php echo $search_id; ?></span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">Search Type:</label>
                <span><?php echo $search_type; ?></span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">State:</label>
                <span><?php echo $counties[$data->search_county_id]['state_code']; ?></span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">County:</label>
                <span><?php echo $counties[$data->search_county_id]['county_name']; ?></span>
            </div>
        </div>
    </div>

    <div class="row search-info">
        <div class="title-section">
            <h3>Subject Information</h3>
        </div>
        <div class="col-lg-5">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">Last:</label>
                                <span><?php echo $data->subject->last_name; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">First:</label>
                                <span><?php echo $data->subject->first_name; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">Middle:</label>
                                <span><?php echo $data->subject->middle_name; ?></span>
                            </div>
                        </td>
                    </tr>
    
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 1:</label>
                                <span>
                                    <?php
                                    if (isset($data->subject->aka_names[0])) {
                                        echo $data->subject->aka_names[0]->last_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 1:</label>
                                <span>
                                <?php
                                    if (isset($data->subject->aka_names[0])) {
                                        echo $data->subject->aka_names[0]->first_name; 
                                    } else {
                                        echo "";
                                    }
                                ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 1:</label>
                                <span>
                                    <?php 
                                    if (isset($data->subject->aka_names[0])) {
                                        echo $data->subject->aka_names[0]->middle_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 2:</label>
                                <span>
                                    <?php
                                    if (isset($data->subject->aka_names[1])) {
                                        echo $data->subject->aka_names[1]->last_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 2:</label>
                                <span>
                                <?php
                                    if (isset($data->subject->aka_names[1])) {
                                        echo $data->subject->aka_names[1]->first_name; 
                                    } else {
                                        echo "";
                                    }
                                ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 2:</label>
                                <span>
                                    <?php 
                                    if (isset($data->subject->aka_names[1])) {
                                        echo $data->subject->aka_names[1]->middle_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 3:</label>
                                <span>
                                    <?php
                                    if (isset($data->subject->aka_names[2])) {
                                        echo $data->subject->aka_names[2]->last_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 3:</label>
                                <span>
                                <?php
                                    if (isset($data->subject->aka_names[3])) {
                                        echo $data->subject->aka_names[3]->first_name; 
                                    } else {
                                        echo "";
                                    }
                                ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">AKA 3:</label>
                                <span>
                                    <?php 
                                    if (isset($data->subject->aka_names[3])) {
                                        echo $data->subject->aka_names[3]->middle_name; 
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-lg-2">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">DOB:</label>
                                <span><?php echo $data->subject->date_of_birth; ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">SSN:</label>
                                <span><?php echo $data->subject->ssn; ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">DL:</label>
                                <span><?php echo (isset($data->subject->drivers_license) ? $data->subject->drivers_license : ''); ?></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-lg-5">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">Addr:</label>
                                <span><?php echo (isset($data->subject->address1) ? $data->subject->address1 : ''); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <span><?php echo $data->subject->address2; ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">City:</label>
                                <span><?php echo $data->subject->city; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">State:</label>
                                <span><?php echo $data->subject->state; ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label class="control-label">Zip:</label>
                                <span><?php echo $data->subject->zip_code; ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <label class="control-label">Ctry:</label>
                                <span><?php echo $data->subject->country; ?></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Case Dispositions - case_addl_dispositions- CriminalAddition[] -->
    <div class="row section-form">
        <div class="col-lg-12">
            <h3><b>Case Dispositions : </b></h3>
            <div id="case_disposition_cache" class="list-group"></div>
        </div>
        <div class="col-lg-12 text-center">
            <br>
            <button type="button" class="text-right btn btn-warning case-disposition-trigger" data-toggle="modal"
                data-target="#casedispositionsModal">
                Add New Case Dispositions
            </button>
        </div>
    </div>

    <!-- Criminal Charges - criminal_charges -CriminalCharge[] -->
    <div class="row section-form">
        <div class="col-lg-12">
            <h3><b>Criminal Charges : </b></h3>
            <div id="criminal_charges_cache" class="list-group"></div>
        </div>
        <div class="col-lg-12 text-center" style="padding-bottom: 40px;">
            <button type="button" class="text-right btn btn-warning criminal-charges-trigger" data-toggle="modal"
                data-target="#CriminalChargesModal">
                Add New Criminal Charges
            </button>
        </div>
    </div>

    <!-- Main Case -->
    <div class="row">
        <!-- Case Number -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    <small class="req text-danger">*</small> Case Number
                </label>
                <input type="text" name="case_number" id="case_number" class="form-control validate[required,custom[number]]">
            </div>
        </div>
        <!--  File Date -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    <small class="req text-danger">*</small> File Date
                </label>
                <input type="text" name="file_date" id="file_date" class="form-control validate[required,custom[date]] input-date">
            </div>
        </div>
        <!-- Identified By Name -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Identified By Name
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="identified_by_name" value="true" checked>Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_name" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identified By DOB -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Identified By DOB
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="identified_by_dob" value="true" checked>Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_dob" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Identified By SSN -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Identified By SSN
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="identified_by_ssn" value="true" checked>Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_ssn" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Name on File -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    <small class="req text-danger">*</small> Name on File
                </label>
                <input type="text" name="name_on_file" id="name_on_file" class="form-control validate[required]">
            </div>
        </div>
        <!-- DOB on File -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    DOB on File
                </label>
                <input type="text" name="dob_on_file" id="dob_on_file" class="form-control validate[custom[date]] input-date">
            </div>
        </div>
        <!-- Additional DOB on File -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Additional DOB on File
                </label>
                <input type="text" name="addl_dob_on_file" id="addl_dob_on_file" class="form-control validate[custom[date]] input-date">
            </div>
        </div>
        <!-- SSN on File -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    SSN on File
                </label>
                <input type="text" name="ssn_on_file" id="ssn_on_file" class="form-control">
            </div>
        </div>

        <!-- DL State -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    DL State
                </label>
                <input type="text" name="dl_state" id="dl_state" class="form-control">
            </div>
        </div>

        <!--  DL Number -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    DL Number
                </label>
                <input type="text" name="dl_number" id="dl_number" class="form-control">
            </div>
        </div>

        <!-- Street Address -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Street Address
                </label>
                <input type="text" name="street_address" id="street_address" class="form-control">
            </div>
        </div>

        <!-- City -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    City
                </label>
                <input type="text" name="city" id="city" class="form-control">
            </div>
        </div>

        <!-- State -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    State
                </label>
                <input type="text" name="state" id="state" class="form-control">
            </div>
        </div>

        <!-- Zip Code -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Zip Code
                </label>
                <input type="text" name="zip_code" id="zip_code" class="form-control">
            </div>
        </div>

        <!-- Additional -->
        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">
                    Additional Information
                </label>
                <textarea type="text" name="addl_information" id="addl_information" class="form-control"></textarea>
            </div>
        </div>

        <!--  Case Disposition Date -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    <small class="req text-danger">*</small> Case Disposition Date
                </label>
                <input type="text" name="case_disposition_date" id="case_disposition_date" class="form-control validate[required,custom[date]] input-date">
            </div>
        </div>

        <!-- Case Disposition-->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Case Disposition ID
                </label>
                <select class="form-control" id="case_disposition_id" name="case_disposition_id">
                    <option value="1">SELECT</option>
                    <?php foreach ($dispositions as $key => $value): ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?> - [<?php echo $key; ?>]</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Case Sentence - case_sentence - CriminalSentence -->
    <div class="row">
        <div class="col-lg-12">
            <h3><b>Case Sentence</b></h3>
        </div>
        <!-- jail_time -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Jail Time
                </label>
                <input type="text" name="case_sentence[jail_time]" class="form-control case_sentence-jail_time">
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
                        <label class="btn btn-default active">
                            <input type="radio" name="case_sentence[jail_suspended]"
                                class="radio case_sentence-jail_suspended" value="true" checked>Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="case_sentence[jail_suspended]"
                                class="radio case_sentence-jail_suspended" value="false">No
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
                <input type="text" name="case_sentence[jail_credit_time]"
                    class="form-control case_sentence-jail_credit_time">
            </div>
        </div>
        <!-- prison_time -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Prison Time
                </label>
                <input type="text" name="case_sentence[prison_time]" class="form-control case_sentence-prison_time">
            </div>
        </div>
        <!-- prison_suspended -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Prison Suspended
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="case_sentence[prison_suspended]"
                                class="radio case_sentence-prison_suspended" value="true">Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="case_sentence[prison_suspended]"
                                class="radio case_sentence-prison_suspended" value="false">No
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
                <input type="text" name="case_sentence[prison_credit_time]"
                    class="form-control case_sentence-prison_credit_time">
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Probation Type
                </label>
                <select class="form-control case_sentence-probation_type_id" id="case_sentence-probation_type_id" name="case_sentence[probation_type_id]">
                    <option value="1">SELECT</option>
                    <?php foreach ($probationTypes as $key => $value): ?>
                    <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- probation_duration_time -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Probation Duration Time
                </label>
                <input type="text" name="case_sentence[probation_duration_time]"
                    class="form-control case_sentence-probation_duration_time">
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    License Suspended Time
                </label>
                <input type="text" name="case_sentence[license_suspended_time]"
                    class="form-control case_sentence-license_suspended_time">
            </div>
        </div>

        <!-- community_service_time -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Community Service Time
                </label>
                <input type="text" name="case_sentence[community_service_time]"
                    class="form-control case_sentence-community_service_time">
            </div>
        </div>
        <!-- fines -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Fines
                </label>
                <input type="number" name="case_sentence[fines]" id="fines" class="form-control case_sentence-fines">
            </div>
        </div>
        <!-- fees -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Fees
                </label>
                <input type="number" name="case_sentence[fees]" id="fees" class="form-control case_sentence-fees">
            </div>
        </div>
        <!-- costs -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Costs
                </label>
                <input type="number" name="case_sentence[costs]" id="costs" class="form-control case_sentence-costs">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Restitution
                </label>
                <input type="number" name="case_sentence[restitution]" id="restitution" class="form-control case_sentence-restitution">
            </div>
        </div>
        <!-- classes_and_programs -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Classes and Programs
                </label>
                <input type="text" name="case_sentence[classes_and_programs]"
                    class="form-control case_sentence-classes_and_programs">
            </div>
        </div>
        <!-- addl_information -->
        <div class="col-lg-4">
            <div class="form-group">
                <label class="control-label">
                    Additional Information
                </label>
                <textarea type="text" name="case_sentence[addl_information]" id="addl_information"
                    class="form-control case_sentence-addl_information"></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 text-right">
            <input type="hidden" name="submit-mode" value="cases">
            <input type="hidden" name="search_id" id="add-case-search_id" value="<?php echo $data->search_id; ?>">
            <input type="submit" class="btn btn-primary" value="Submit case">
        </div>
    </div>

    <?php echo form_close(); ?>
</div>

<!-- Modal Case Dispositions  -->
<?php $this->load->view('admin/searches/case_dispositions'); ?>

<!-- Modal Criminal Charges  -->
<?php $this->load->view('admin/searches/criminal_charges'); ?>
<style>
#add-case-form > form { 
    max-width: 1200px !important;
}
</style>