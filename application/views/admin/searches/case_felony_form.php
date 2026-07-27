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
				<label><input type="checkbox" name="identified_by_name" value="true" <?php echo ( !isset($_GET['cid']) && !$cases ? 'checked' : (edit_case($edit_case, 'identified_by_name') === 'true' ? 'checked' : '') ); ?>> Name</label>
			</div>
			<div class="input-field">
				<label><small class="req text-danger">*</small> Name on File:</label>
				<input type="text" name="name_on_file" id="name_on_file" class="form-field validate[required]" value="<?php 
				echo ( !isset($_GET['cid']) && !$cases ? (isset($data->subject->last_name) && $data->subject->last_name != '' ? $data->subject->last_name . ', ' : '') . ' ' . (isset($data->subject->first_name) ? $data->subject->first_name : '') . ' ' . (isset($data->subject->middle_name) ? $data->subject->middle_name : '') : edit_case($edit_case, 'name_on_file') ); 
				?>">
			</div>

		</div>
		<div class="input-column">
			<div class="input-field">
				<label><input type="checkbox" name="identified_by_dob" value="true" <?php echo ( !isset($_GET['cid']) && !$cases ? 'checked' : (edit_case($edit_case, 'identified_by_dob') === 'true' ? 'checked' : '') ); ?>> DOB</label>
			</div>
			<div class="input-field">
				<label>DOB on File:</label>
				<input type="text" name="dob_on_file" id="dob_on_file" class="form-field validate[custom[date]] input-date" value="<?php 
				echo ( !isset($_GET['cid']) && !$cases ? (dob_format(isset($data->subject->date_of_birth) ? $data->subject->date_of_birth : '')) : edit_case($edit_case, 'dob_on_file') );
				?>">
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
		<div class="input-column x2">
			
			<div class="input-field">
				<label>Additional Information:</label>
				<textarea name="addl_information" id="addl_information" class="form-field min-width-500"><?php echo edit_case($edit_case, 'addl_information'); ?></textarea>
			</div>

		</div>
		
	</div>
</div>

<div id="next-step" style="display: none;">
	<div class="section-title">
		<h3>Charge Information</h3>
	</div>
</div>

<div id="additional-charge" style="display: none;">
	<button type="button" class="btn btn-warning add-new-charge">Add New Charge</button>
</div>

<div class="section-action text-center">
	<button type="button" class="btn btn-primary next-step">Continue</button>
</div>
<div class="section-action text-center submit-case" style="display: none;">
	<button type="submit" class="btn btn-primary"><?php echo ( $edit_case ? 'Update' : 'Finish'); ?></button>
</div>

<?php echo form_close(); ?>

