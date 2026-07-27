<!-- Add Civil Case -->
<div id="add-case-form" class="add-case-content">
    <?php echo form_open('admin/searches/update_search'); ?>
    <input type="hidden" name="search_type" value="<?php echo $search_type; ?>">
    <h2><strong>Add Civil Case</strong> <span class="close close-case pull-right">close</span></h2>

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

    <!-- Main Add Civil Case -->
    <div class="row">
        <!-- Additional Identifier Info -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Additional Identifier Info
                </label>
                <input type="text" name="addl_identifier" id="addl_identifier" class="form-control">
            </div>
        </div>
        <!-- Case Number -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    <small class="req text-danger">*</small> Case Number
                </label>
                <input type="text" name="case_number" id="case_number" class="form-control" required="">
            </div>
        </div>

        <!-- Case Type -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Case Type
                </label>
                <input type="text" name="case_type" id="case_type" class="form-control">
            </div>
        </div>

        <!--  Case Type ID -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Case Type ID
                </label>
                <select class="form-control" id="case_type_id" name="case_type_id">
                    <option value=""></option>
                    <?php foreach ($caseTypeId as $key => $case_type): ?>
                        <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $case_type['description']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!--  Civil Disposition -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Civil Disposition
                </label>
                <select class="form-control" id="civil_disposition_id" name="civil_disposition_id">
                    <option value=""></option>
                    <?php foreach ($civilDisposition as $key => $value): ?>
                        <option value="<?php echo $key; ?>">[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Court Location -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Court Location
                </label>
                <input type="text" name="court_location" id="court_location" class="form-control">
            </div>
        </div>

        <!-- Defendant -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Defendant
                </label>
                <input type="text" name="defendant" id="defendant" class="form-control">
            </div>
        </div>

        <!-- DL Number -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    DL Number
                </label>
                <input type="text" name="dl_number" id="dl_number" class="form-control">
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

        <!--  DOB on File -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    DOB on File
                </label>
                <input type="text" name="dob_on_file" id="dob_on_file" class="form-control" required="">
            </div>
        </div>

        <!--  File Date -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    File Date
                </label>
                <input type="text" name="file_date" id="file_date" class="form-control" required="">
            </div>
        </div>

        <!-- Identified By DOB -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    Identified By DOB
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="identified_by_dob" value="true">Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_dob" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identified By Name -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    Identified By Name
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="identified_by_name" value="true">Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_name" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identified By SSN -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    Identified By SSN
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="identified_by_ssn" value="true">Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_ssn" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identified By Other -->
        <div class="col-lg-3">
            <div class="form-group">
                <label class="control-label">
                    Identified By Other
                </label>
                <div class="input-group">
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="radio" name="identified_by_other" value="true">Yes
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" autocomplete="off" name="identified_by_other" value="false">No
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Judgement -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Judgement
                </label>
                <input type="text" name="judgement" id="judgement" class="form-control">
            </div>
        </div>

        <!-- Judgement Amount -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Judgement Amount
                </label>
                <input type="text" name="judgement_amount" id="judgement_amount" class="form-control">
            </div>
        </div>

        <!--  Judgement Date -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Judgement Date
                </label>
                <input type="text" name="judgement_date" id="judgement_date" class="form-control">
            </div>
        </div>

        <!-- Name on File -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Name on File
                </label>
                <input type="text" name="name_on_file" id="name_on_file" class="form-control">
            </div>
        </div>

        <!-- Plaintiff -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Plaintiff
                </label>
                <input type="text" name="plaintiff" id="plaintiff" class="form-control">
            </div>
        </div>

        <!-- SSN on File-->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    SSN on File
                </label>
                <input type="text" name="ssn_on_file" id="ssn_on_file" class="form-control">
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

        <!-- Street Address -->
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">
                    Street Address
                </label>
                <input type="text" name="street_address" id="street_address" class="form-control">
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