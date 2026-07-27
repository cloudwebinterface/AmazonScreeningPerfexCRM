<?php echo form_open('submit/case', array('id' => 'caseForm')); ?>
<input type="hidden" name="search_id" value="<?php echo $search_id; ?>">
<input type="hidden" name="search_type" value="<?php echo $search_type; ?>">
<?php if ( $edit_case ) {
	echo '<input type="hidden" name="submit_mode" value="edit">';
	echo '<input type="hidden" name="cid" value="'.$_GET['cid'].'">';
} ?>
<div class="section">
	<div class="section-title">
		<h3>Case Information</h3>
	</div>
	<div class="input-wrapper">
		<div class="input-column">
			<div class="input-field">
				<label><small class="req text-danger">*</small> Case Number:</label>
				<input type="text" name="case_number" id="case_number" class="form-field validate[required,custom[number]]" value="<?php echo edit_case($edit_case, 'case_number'); ?>">
			</div>	
		</div>
		<div class="input-column">
			<div class="input-field">
				<label><small class="req text-danger">*</small> File Date:</label>
				<input type="text" name="file_date" id="file_date" class="form-field validate[required,custom[date]] input-date" value="<?php echo edit_case($edit_case, 'file_date'); ?>">
			</div>	
		</div>
		<div class="input-column">
			<div class="input-field">
				<label>Case Type:</label>
				<input type="text" name="case_type" class="form-field" value="<?php echo edit_case($edit_case, 'case_type'); ?>">
			</div>
		</div>
		<div class="input-column">
			<div class="input-field">
				<label>Case Type ID:</label>
				<select class="form-field" name="case_type_id">
                    <option value=""></option>
                    <?php foreach ($caseTypeId as $key => $case_type): ?>
                        <option value="<?php echo $key; ?>" <?php echo (edit_case($edit_case, 'case_type_id') == $key ? 'selected' : ''); ?>>[<?php echo $key; ?>] <?php echo $case_type['description']; ?></option>
                    <?php endforeach; ?>
                </select>
			</div>
		</div>
	</div>
</div>

<div class="section">
	<div class="section-title">
		<h3>Identified by Information</h3>
	</div>
	<div class="section-sub-title">
		<h4>Identified by</h4>
	</div>
	<div class="input-wrapper">
		<div class="input-column">
			<div class="input-field">
				<label><input type="checkbox" name="identified_by_name" value="true" <?php echo (edit_case($edit_case, 'identified_by_name') === 'true' ? 'checked' : ''); ?>> Name</label>
			</div>
			<div class="input-field">
				<label><small class="req text-danger">*</small> Name on File:</label>
				<input type="text" name="name_on_file" id="name_on_file" class="form-field validate[required]" value="<?php echo edit_case($edit_case, 'name_on_file'); ?>">
			</div>

		</div>
		<div class="input-column">
			<div class="input-field">
				<label><input type="checkbox" name="identified_by_dob" value="true" <?php echo (edit_case($edit_case, 'identified_by_dob') === 'true' ? 'checked' : ''); ?>> DOB</label>
			</div>
			<div class="input-field">
				<label>DOB on File:</label>
				<input type="text" name="dob_on_file" id="dob_on_file" class="form-field validate[custom[date]] input-date" value="<?php echo edit_case($edit_case, 'dob_on_file'); ?>">
			</div>

		</div>
		<div class="input-column">
			<div class="input-field">
				<label><input type="checkbox" name="identified_by_ssn" value="true" <?php echo (edit_case($edit_case, 'identified_by_ssn') === 'true' ? 'checked' : ''); ?>> SSN</label>
			</div>
			<div class="input-field">
				<label>SSN on File:</label>
				<input type="text" name="ssn_on_file" id="ssn_on_file" class="form-field validate[custom[number]]" value="<?php echo edit_case($edit_case, 'ssn_on_file'); ?>">
			</div>

		</div>
		<div class="input-column">
			<div class="input-field">
				<label><input type="checkbox" name="identified_by_other" value="true" <?php echo (edit_case($edit_case, 'identified_by_other') === 'true' ? 'checked' : ''); ?>> Other</label>
			</div>
			<div class="input-field">
				<label>Defendant:</label>
				<input type="text" name="defendant" class="form-field" value="<?php echo edit_case($edit_case, 'defendant'); ?>">
			</div>

		</div>
	</div>

	<div class="section-sub-title">
		<h4>Additional Identifiers</h4>
	</div>
	<div class="input-wrapper">
		<div class="input-column">
			<div class="input-field">
				<label>Street Address:</label>
				<input type="text" name="street_address" id="street_address" class="form-field" value="<?php echo edit_case($edit_case, 'street_address'); ?>">
			</div>
			<div class="input-field">
				<label>City:</label>
				<input type="text" name="city" id="city" class="form-field" value="<?php echo edit_case($edit_case, 'city'); ?>">
			</div>
			<div class="input-field">
				<label>State:</label>
				<select name="state" id="state" class="form-field">
					<option value="">SELECT</option>
					<?php foreach ($states as $key => $state) {
						echo '<option value="'.$key.'" '.(edit_case($edit_case, 'state') == $key ? 'selected' : '').'>'.strtoupper($state).'</option>';
					} ?>
				</select>
			</div>
			<div class="input-field">
				<label>Zip:</label>
				<input type="text" name="zip_code" id="zip_code" class="form-field" value="<?php echo edit_case($edit_case, 'zip_code'); ?>">
			</div>
		</div>
		<div class="input-column">
			<div class="input-field">
				<label>DL#:</label>
				<input type="text" name="dl_number" id="dl_number" class="form-field" value="<?php echo edit_case($edit_case, 'dl_number'); ?>">
			</div>
			<div class="input-field">
				<label>DL-State:</label>
				<select name="dl_state" id="dl_state" class="form-field">
					<option value="">SELECT</option>
					<?php foreach ($states as $key => $state) {
						echo '<option value="'.$key.'" '.(edit_case($edit_case, 'dl_state') == $key ? 'selected' : '').'>'.strtoupper($state).'</option>';
					} ?>
				</select>
			</div>
		</div>	
		<div class="input-column">
			
			<div class="input-field">
				<label>Additional Identifier:</label>
				<input type="text" name="addl_identifier" class="form-field" value="<?php echo edit_case($edit_case, 'addl_identifier'); ?>">
			</div>

		</div>
	</div>
	<div class="section-sub-title">
		<h4>Disposition Information</h4>
	</div>
	<div class="input-wrapper">
		<div class="input-column">
			<div class="input-field">
				<label>Civil Disposition</label>
                <select class="form-field" name="civil_disposition_id">
                    <option value=""></option>
                    <?php foreach ($civilDisposition as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php echo (edit_case($edit_case, 'civil_disposition_id') == $key ? 'selected' : ''); ?>>[<?php echo $key; ?>] <?php echo $value['description']; ?></option>
                    <?php endforeach; ?>
                </select>
			</div>
			<div class="input-field">
				<label>Court Location</label>
                <input type="text" name="court_location" class="form-field" value="<?php echo edit_case($edit_case, 'court_location'); ?>">
			</div>
		</div>
		<div class="input-column">
			<div class="input-field">
				<label>Judgement:</label>
				<input type="text" name="judgement" class="form-field" value="<?php echo edit_case($edit_case, 'judgement'); ?>">
			</div>
			<div class="input-field">
				<label>Judgement Amount:</label>
				<input type="text" name="judgement_amount" class="form-field validate[custom[number]]" value="<?php echo edit_case($edit_case, 'judgement_amount'); ?>">
			</div>
			<div class="input-field">
				<label>Judgement Date:</label>
				<input type="text" name="judgement_date" class="form-field validate[custom[date]] input-date" value="<?php echo edit_case($edit_case, 'judgement_date'); ?>">
			</div>
		</div>	
		<div class="input-column">
			<div class="input-field">
				<label>Plaintiff:</label>
				<input type="text" name="plaintiff" class="form-field" value="<?php echo edit_case($edit_case, 'plaintiff'); ?>">
			</div>
		</div>
	</div>
</div>

<div class="section-action text-center submit-case">
	<button type="submit" class="btn btn-primary"><?php echo ( $edit_case ? 'Update' : 'Finish'); ?></button>
</div>

<?php echo form_close(); ?>