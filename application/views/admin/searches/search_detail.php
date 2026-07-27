<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?> 
<style><?php include FCPATH . 'assets/css/search-detail.css'; ?></style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			
			<div class="col-md-12">
				<div class="panel_s">
					
					<div class="panel-body col-md-12">

						<?php if ( $previous_data ): ?>
							<div class="found-in-history">
								<div class="fih-title">
									<h2><?php echo count($previous_data); ?> similar data <?php echo (count($previous_data) > 1 ? 'have' : 'has'); ?> been found in history:</h2>
								</div>
									
								<table>
									<thead>
										<tr>
											<th>Search ID</th>
											<th>SSN</th>
											<th>First Name</th>
											<th>Middle Name</th>
											<th>Last Name</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $previous_data as $prev ): ?>
										<tr>
											<td><?php echo $prev['search_id']; ?></td>
											<td><?php echo $prev['ssn']; ?></td>
											<td><?php echo $prev['first_name']; ?></td>
											<td><?php echo $prev['middle_name']; ?></td>
											<td><?php echo $prev['last_name']; ?></td>
											<td><?php 
											switch ($prev['status']) {
												case 'N':
													echo 'Not Found';
												break;

												case 'F':
													echo 'Found';
												break;

												case 'C':
													echo 'Canceled';
												break;
												
												default:
													echo 'Canceled';
												break;
											}
											 ?></td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
								
							</div>
						<?php endif; ?>

						<?php if ( count($multiple_entries) > 1 ) {
							?>
								<div class="notification-search">
									<div class="page-title">
										<h2>multiple entries with same entry found:</h2>
									</div>
									<ul>
										<?php
										foreach ($multiple_entries as  $s) {
											?> 
											<li><a target="_blank" href="/admin/search/<?php echo $s; ?>"><?php echo $s; ?></a></li>
										<?php
										}
										?>
									</ul>
								</div>
							<?php
						}
						?>
						
						
						<div class="page-title">
							<h2>Criminal Case Information <a href="/admin/search/<?php echo $search_id; ?>/pdf" class="btn btn-primary export">Export to PDF</a></h2>
						</div>

						<div class="section">
							<div class="section-title">
								<h3>Search Information</h3>
							</div>
							<ul class="search-info">
								<li><label>Search ID:</label> <?php echo $search_id; ?></li>
								<li><label>Search Type:</label> <?php echo $search_type; ?></li>
								<li><label>State:</label> <?php echo $data->subject->state; ?></li>
								<li><label>County:</label> <?php echo $counties[$data->search_county_id]['county_name']; ?></li>
							</ul>
						</div><!-- END section -->

						<div class="section">
							<div class="section-title">
								<h3>Subject Information</h3>
							</div>
							<div class="row">
								<div class="col-md-5">
									<ul class="subject-info">
										<li>
											<span><label>Last:</label> <?php echo $data->subject->last_name; ?></span>
											<span><label>First:</label> <?php echo $data->subject->first_name; ?></span>
											<span><label>Middle:</label> <?php echo $data->subject->middle_name; ?></span>
										</li>
										<li>
											<span>
												<label>AKA 1:</label> 
												<?php if (isset($data->subject->aka_names[0]->last_name)) { echo $data->subject->aka_names[0]->last_name; } ?>
											</span>
											<span>
												<label>AKA 1:</label> 
												<?php if (isset($data->subject->aka_names[0]->first_name)) { echo $data->subject->aka_names[0]->first_name; } ?>
											</span>
											<span>
												<label>AKA 1:</label> 
												<?php if (isset($data->subject->aka_names[0]->middle_name)) { echo $data->subject->aka_names[0]->middle_name; } ?>
											</span>
										</li>
										<li>
											<span>
												<label>AKA 2:</label> 
												<?php if (isset($data->subject->aka_names[1]->last_name)) { echo $data->subject->aka_names[1]->last_name; } ?>
											</span>
											<span>
												<label>AKA 2:</label> 
												<?php if (isset($data->subject->aka_names[1]->first_name)) { echo $data->subject->aka_names[1]->first_name; } ?>
											</span>
											<span>
												<label>AKA 2:</label> 
												<?php if (isset($data->subject->aka_names[1]->middle_name)) { echo $data->subject->aka_names[1]->middle_name; } ?>
											</span>
										</li>
										<li>
											<span>
												<label>AKA 3:</label> 
												<?php if (isset($data->subject->aka_names[2]->last_name)) { echo $data->subject->aka_names[2]->last_name; } ?>
											</span>
											<span>
												<label>AKA 3:</label> 
												<?php if (isset($data->subject->aka_names[2]->first_name)) { echo $data->subject->aka_names[2]->first_name; } ?>
											</span>
											<span>
												<label>AKA 3:</label> 
												<?php if (isset($data->subject->aka_names[2]->middle_name)) { echo $data->subject->aka_names[2]->middle_name; } ?>
											</span>
										</li>
									</ul>
								</div>
								<div class="col-md-2">
									<ul class="subject-info-col-2">
										<li><label>Suffix:</label> <?php echo isset($data->subject->name_suffix) ? $data->subject->name_suffix : ''; ?></li>
										<li><label>DOB:</label> <?php echo isset($data->subject->date_of_birth) ? dob_format($data->subject->date_of_birth) : ''; ?></li>
										<li><label>SSN:</label> <?php echo isset($data->subject->ssn) ? $data->subject->ssn : ''; ?></li>
										<li><label>DL#:</label> <?php echo isset($data->subject->drivers_license) ? $data->subject->drivers_license : ''; ?></li>
									</ul>
								</div>
								<div class="col-md-2">
									<ul class="subject-info-col-2 wider-label">
										<li><label>Address:</label> <?php echo isset($data->subject->address1) ? $data->subject->address1 : ''; ?> <?php echo isset($data->subject->address2) ? $data->subject->address2 : ''; ?></li>
										<li><label>City:</label> <?php echo isset($data->subject->city) ? $data->subject->city : ''; ?></li>
										<li><label>Country:</label> <?php echo isset($data->subject->country) ? $data->subject->country : ''; ?></li>
										<li><label>State:</label> <?php echo isset($data->subject->state) ? $data->subject->state : ''; ?></li>
										<li><label>Zip:</label> <?php echo isset($data->subject->zip_code) ? $data->subject->zip_code : ''; ?></li>
									</ul>
								</div>

								<div class="col-md-3">
									<ul class="subject-info-col-2 widest-label">
										<li><label>Mother's Maiden:</label> <?php echo isset($data->subject->mothers_maiden) ? $data->subject->mothers_maiden : ''; ?></li>
										<li><label>Position Location:</label> <?php echo isset($data->subject->position_state) ? $data->subject->position_state : ''; ?></li>
										<li><label>Position Location County:</label> <?php echo isset($data->subject->position_county) ? $data->subject->position_county : ''; ?></li>
										
									</ul>
								</div>
							</div>
						</div><!-- END section -->

						<div class="section">
							<div class="section-title">
								<h3>Notes</h3>
							</div>
							<div class="row">
								<div class="col-md-12">
									<table class="notes">
										<tr>
											<td>Client notes</td>
											<td><?php echo (isset($data->client_notes) && $data->client_notes != '' ? nl2br($data->client_notes) : '-'); ?></td>
										</tr>
										<tr>
											<td>Internal notes</td>
											<td><?php echo (isset($data->internal_notes) && $data->internal_notes != '' ? nl2br($data->internal_notes) : '-'); ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						
						<?php if ( $cases ): ?>
							<div class="section">
								<div class="section-title">
									<h3>Cases Entered</h3>
								</div>
								
								<div class="added-cases">
									<ul class="list-group">
										<?php foreach($cases as $c_id => $c): ?>
											<li class="list-group-item">
												<span class="c-label"><a href="<?php echo admin_url( 'search/'.$search_id.'?cid='.$c_id ); ?>" data-cid="<?php echo $c_id; ?>" class="edit-case">Case number <?php echo $c['case_number']; ?></a></span> 
												<span class="c-action pull-right"><a href="<?php echo admin_url( 'search/'.$search_id.'?cid='.$c_id ); ?>" class="btn btn-primary edit-case" data-cid="<?php echo $c_id; ?>">Edit</a> <a href="<?php echo admin_url('case/delete/'.$data->search_id.'/'.$c_id) ?>" data-cid="<?php echo $c_id; ?>" class="btn btn-danger delete-case" onclick="return confirm('Are you sure want to delete this case?');">Delete</a></span></li>
										<?php endforeach; ?>
									</ul>
								</div>

								<?php echo form_open('submit/search'); ?>
									<input type="hidden" name="search_id" value="<?php echo $search_id; ?>">
									<input type="submit" class="btn btn-success submit-case" onclick="return confirm('Are you sure want to submit this case?');" value="Submit Entered Cases">
									<?php if ( $have_duplicates && !$already_duplicated ): ?>
										<a href="<?php echo admin_url('cases/clone/'.$data->search_id) ?>" class="btn btn-warning clone-cases" data-content="there are <?php echo $total_duplicates; ?> searches with the same name and SSN (<?php echo isset($data->subject->ssn) ? $data->subject->ssn : ''; ?>). Use this Clone button to duplicate the cases to the same SSN">Clone</a>
									<?php endif; ?>
								<?php echo form_close(); ?>
								
							</div>
						<?php endif; ?>

						<?php if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {
                            // Civil Case
                            $this->load->view('admin/searches/case_civil_form');
                        } else {
                            // Add Case
                            $this->load->view('admin/searches/case_felony_form');
                        } ?>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="dynamic-fields" style="display: none;">
    <div class="charge-info" id="crg-1">
        <span class="cc-remove"><i class="fa fa-close"></i></span>
        <div class="section-sub-title">
            <h4>Charge 1</h4>
        </div>
        <div class="input-wrapper">
            <div class="input-column x2">
                <div class="input-field">
                    <label><small class="req text-danger">*</small> Charge Description:</label>
                    <input type="text" name="charge[0][description]" class="form-field validate[required,maxSize[100]] min-width-500">
                </div>
            </div>
            <div class="input-column">
                <div class="input-field">
                    <label><small class="req text-danger">*</small> Charge Level:</label>
                    <select class="form-field validate[required]" name="charge[0][charge_level_id]">
                        <option value="">SELECT</option>
                        <?php foreach ($chargeLevel as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?> - [<?php echo $key; ?>]
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="input-column">
                <div class="input-field">
                    <label><small class="req text-danger">*</small> Charge Disposition:</label>
                    <select class="form-field validate[required]" name="charge[0][charge_disposition_id]">
                        <option value="">SELECT</option>
                        <?php foreach ($dispositions as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?> - [<?php echo $key; ?>]
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

	        <div class="section-sub-title">
	            <h3>Disposition Information</h3>
	        </div>
	        <div class="input-column x2">
				<div class="input-field">
					<label>Disposition Date:</label>
					<input type="text" name="charge[0][disposition_date]" class="form-field validate[custom[date]] input-date">
				</div>
				<div class="input-field">
                    <label>Classes & Programes:</label>
                    <input type="text" name="charge[0][classes_and_programes]" class="form-field min-width-500">
                </div>
                <div class="input-field">
                    <label>Additional Information:</label>
                    <textarea name="charge[0][addl_information]" class="form-field min-width-500"></textarea>
                </div>
                <div class="input-field">
                    <label>Fines:</label>
                    <input type="text" name="charge[0][fines]" class="form-field validate[custom[number]]">
                </div>
                <div class="input-field">
	                <label>Fees:</label>
	                <input type="text" name="charge[0][fees]" class="form-field validate[custom[number]]">
	            </div>
	            <div class="input-field">
	                <label>Costs:</label>
	                <input type="text" name="charge[0][costs]" class="form-field validate[custom[number]]">
	            </div>
	            <div class="input-field">
                    <label>Resitution:</label>
                    <input type="text" name="charge[0][restitution]" class="form-field validate[custom[number]]">
                </div>
			</div>

			<div class="input-column x2">
				<div class="accordion">
					<div class="trigger jail">
                        <input type="checkbox" id="jail-1" class="accord-main" >
                        <label for="jail-1" class="label-accordion">
                            <span>Jail<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                        </label>
                        <div class="content">
                        	<div class="input-field">
								<label>Jail Suspended:</label>
								<input type="checkbox" name="charge[0][sentence][jail_suspended]">
							</div>
                            <div class="input-field">
				                <label>Jail Time:</label>
				                <div class="time-field">
					                <span>
					                	<input type="text" name="charge[0][sentence][jail_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][jail_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][jail_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
				            <div class="input-field">
				                <label>Jail Credit Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][jail_credit_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][jail_credit_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][jail_credit_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
                        </div>
                    </div>
                    <div class="trigger prison">
                        <input type="checkbox" id="prison-1" class="accord-main" >
                        <label for="prison-1" class="label-accordion">
                            <span>Prison<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                        </label>
                        <div class="content">
                            <div class="input-field">
								<label>Prison Suspended:</label><input type="checkbox" name="charge[0][sentence][prison_suspended]">
							</div>
                            <div class="input-field">
				                <label>Prison Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][prison_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][prison_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][prison_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
				            <div class="input-field">
				                <label>Prison Credit Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][prison_credit_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][prison_credit_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][prison_credit_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
                        </div>
                    </div>
                    <div class="trigger probation">
                        <input type="checkbox" id="probation-1" class="accord-main" >
                        <label for="probation-1" class="label-accordion">
                            <span>Probation<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                        </label>
                        <div class="content">
                            <div class="input-field">
								<label>Probation Type:</label>
								<select class="form-field" name="charge[0][sentence][probation_type_id]">
			                        <?php foreach ($probationTypes as $key => $value): ?>
			                        <option value="<?php echo $key; ?>"><?php echo $value['description']; ?></option>
			                        <?php endforeach; ?>
			                    </select>
							</div>
                            <div class="input-field">
				                <label>Duration Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][probation_duration_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][probation_duration_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][probation_duration_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
				            
                        </div>
                    </div>
                    <div class="trigger license">
                        <input type="checkbox" id="license-1" class="accord-main" >
                        <label for="license-1" class="label-accordion">
                            <span>License Suspended<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                        </label>
                        <div class="content">
                            <div class="input-field">
				                <label>License Suspended Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][license_suspended_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][license_suspended_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][license_suspended_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
                        </div>
                    </div>
                    <div class="trigger community">
                        <input type="checkbox" id="community-1" class="accord-main" >
                        <label for="community-1" class="label-accordion">
                            <span>Community Service<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                        </label>
                        <div class="content">
                            <div class="input-field">
				                <label>Community Service Time:</label>
				                <div class="time-field">
				                	<span>
					                	<input type="text" name="charge[0][sentence][community_service_time][day]" class="form-field">
					                	<label>Days</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][community_service_time][month]" class="form-field">
					                	<label>Months</label>
					                </span>
					                <span>
					                	<input type="text" name="charge[0][sentence][community_service_time][year]" class="form-field">
					                	<label>Years</label>
					                </span>
				                </div>
				            </div>
                        </div>
                    </div>
				</div>
			</div>

			<div class="addl-disposition-container"></div>
			<button type="button" class="add-addl-disposition" data-id="crg-1">Add Add'l Disposition Info</button>
        </div>
            
    </div>
</div>

<div id="addl-disposition-fields" style="display: none;">
	<div class="addl-disposition-content">
		<span class="adis-close"><i class="fa fa-close"></i></span>	
		<div class="section-sub-title">
            <h3>Additional Disposition 1</h3>
        </div>
        <div class="input-column x2">
        	<div class="input-field">
				<label>Addition Date:</label>
				<input type="text" name="charge[0][addl_disposition][0][addition_date]" class="form-field validate[custom[date]] input-date">
			</div>
        	<div class="input-field">
        		<label>Type:</label>
        		<select class="form-field validate[required]" name="charge[0][addl_disposition][0][addition_type_id]">
	                <?php foreach ($additionTypes as $key => $value): ?>
	                <option value="<?php echo $key; ?>"><?php echo $value['description']; ?>
	                </option>
	                <?php endforeach; ?>
	            </select>
        	</div>
	        <div class="input-field">
        		<label>Action:</label>
        		<select class="form-field validate[required]" name="charge[0][addl_disposition][0][addition_action_id]">
	                <?php foreach ($additionActionTypes as $key => $value): ?>
	                <option value="<?php echo $key; ?>"><?php echo $value['description']; ?>
	                </option>
	                <?php endforeach; ?>
	            </select>
        	</div>
			
			<div class="input-field">
                <label>Other:</label>
                <input type="text" name="charge[0][addl_disposition][0][other]" class="form-field min-width-500">
            </div>

		</div>

		<div class="input-column x2">
			<div class="accordion">
				<div class="trigger jail">
                    <input type="checkbox" id="crg-1jail-1" class="accord-main" >
                    <label for="crg-1jail-1" class="label-accordion">
                        <span>Jail<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    </label>
                    <div class="content">
                    	<div class="input-field">
							<label>Jail Suspended:</label>
							<input type="checkbox" name="charge[0][addl_disposition][0][sentence][jail_suspended]">
						</div>
                        <div class="input-field">
			                <label>Jail Time:</label>
			                <div class="time-field">
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
			            <div class="input-field">
			                <label>Jail Credit Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_credit_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_credit_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][jail_credit_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
                    </div>
                </div>
                <div class="trigger prison">
                    <input type="checkbox" id="crg-1prison-1" class="accord-main" >
                    <label for="crg-1prison-1" class="label-accordion">
                        <span>Prison<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    </label>
                    <div class="content">
                        <div class="input-field">
							<label>Prison Suspended:</label><input type="checkbox" name="charge[0][addl_disposition][0][sentence][prison_suspended]">
						</div>
                        <div class="input-field">
			                <label>Prison Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
			            <div class="input-field">
			                <label>Prison Credit Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_credit_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_credit_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][prison_credit_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
                    </div>
                </div>
                <div class="trigger probation">
                    <input type="checkbox" id="crg-1probation-1" class="accord-main" >
                    <label for="crg-1probation-1" class="label-accordion">
                        <span>Probation<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    </label>
                    <div class="content">
                        <div class="input-field">
			                <label>Duration Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][probation_duration_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][probation_duration_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][probation_duration_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
			            
                    </div>
                </div>
                <div class="trigger license">
                    <input type="checkbox" id="crg-1license-1" class="accord-main" >
                    <label for="crg-1license-1" class="label-accordion">
                        <span>License Suspended<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    </label>
                    <div class="content">
                        <div class="input-field">
			                <label>License Suspended Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][license_suspended_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][license_suspended_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][license_suspended_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
                    </div>
                </div>
                <div class="trigger community">
                    <input type="checkbox" id="crg-1community-1" class="accord-main" >
                    <label for="crg-1community-1" class="label-accordion">
                        <span>Community Service<i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    </label>
                    <div class="content">
                        <div class="input-field">
			                <label>Community Service Time:</label>
			                <div class="time-field">
			                	<span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][community_service_time][day]" class="form-field">
				                	<label>Days</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][community_service_time][month]" class="form-field">
				                	<label>Months</label>
				                </span>
				                <span>
				                	<input type="text" name="charge[0][addl_disposition][0][sentence][community_service_time][year]" class="form-field">
				                	<label>Years</label>
				                </span>
			                </div>
			            </div>
                    </div>
                </div>
			</div>
		</div>	
	</div>
</div>

<style>
.ab_errors {
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    position: fixed;
    width: 100%;
    justify-content: center;
    align-items: center;
    background-color: rgba(0,0,0,0.5);
    z-index: 9999;
}
.ab_errors .error-content {
    background-color: #fff;
    z-index: 999999;
    width: 580px;
    padding: 30px;
    border-radius: 4px;
}

.ab_errors .error-content ul {
    padding-left: 20px;
    padding-top: 5px;
}

.ab_errors .error-content li {
    list-style: disc;
    line-height: 1.5;
}

.ab_errors .error-content h3 {
    margin-top: 0;
    margin-bottom: 20px;
    text-align: center;
}
</style>

<?php if (isset($_GET['errorId']) && $_GET['errorId'] != ''): ?>
<?php 
$errorId = $_GET['errorId'];
$errors = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
$error = isset($errors[$errorId]) ? json_decode( $errors[$errorId] ) : false;
$errorList = '';
if ( $error ) {
	foreach ($error->failed_updates as $key => $errorContent) {
		$errorList = $errorContent->error_message;
	}
}

$errorMessages = $errorList != '' ? explode(';', $errorList) : false;

?>

<div class="ab_errors">
	<div class="error-content">
		<h3>Failed updates</h3>
		<?php
			if ( $errorMessages ) {
				echo '<ul>';
				foreach ($errorMessages as $errorMessage) {
					echo '<li>' . $errorMessage . '</li>';
				}
				echo '</ul>';
			} else {
				echo '<pre>';
				print_r($error);
				echo '</pre>';
			}
		?>
	</div>	
</div>
<?php endif; ?>

<?php if ( isset($_GET['duplicate']) && $_GET['duplicate'] != '' ): ?>
<div class="ab_errors">
	<div class="error-content">
		<h3>Duplicate success</h3>
		The cases has been cloned to the following search IDs:
		<ul>
			<?php $dups = json_decode(base64_decode($_GET['duplicate']));?>
			<?php foreach ($dups as $dupsid): ?>
			<li><a href="/admin/search/<?php echo $dupsid; ?>" target="_blank"><?php echo $dupsid; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>	
</div>
<?php endif; ?>

<?php ab_datepicker_scripts(); ?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() . 'assets/plugins/Validation-Engine/css/validationEngine.jquery.css'; ?>">
<script src="<?php echo site_url() . 'assets/plugins/serializejson/jquery.serializejson.js'; ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo site_url() . 'assets/plugins/Validation-Engine/js/languages/jquery.validationEngine-en.js'; ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo site_url() . 'assets/plugins/Validation-Engine/js/jquery.validationEngine.min.js'; ?>" type="text/javascript" charset="utf-8"></script>
<script>
	// case data based on the search ID
	var caseData = <?php echo json_encode($cases); ?>;
	var searchType = '<?php echo $search_type; ?>';
</script>

<script type="text/javascript">
	$(document).on('click', '.export_to_pdf', function(e) {
		e.preventDefault();
		$(this).closest('form').attr('target', '_blank').submit().removeAttr('target');
	});
</script>

<script>
jQuery(document).ready(function ($) {

	$('.ab_errors').on('click', function() {
		$('.ab_errors').fadeOut();
	});

	var dataAbbr = {
		'years':'year', 'year':'year', 'yrs':'year', 'yr':'year', 'y':'year',
		'months':'month', 'month':'month', 'mos':'month', 'mo':'month', 'm':'month',
		'days':'day', 'day':'day', 'dys':'day', 'd':'day',
		'hours':'hour', 'hour':'hour', 'hrs':'hour', 'hr':'hour', 'h':'hour'
	};

	<?php if ( isset($_GET['cid']) && $edit_case ): ?>
		var editCase = <?php echo json_encode($edit_case); ?>;
		console.log('case:', editCase);

		<?php $types = array('CIV', 'CIV-L'); ?>

		<?php if ( ! in_array( $search_type, $types ) ) { ?>

			$('button.next-step').hide();
			$('#next-step, #additional-charge, .submit-case').fadeIn();

			if ( editCase.charge ) {
				for( var i = 0; i < editCase.charge.length; i++ ) {
					var chargeId = i+1;

					$('#dynamic-fields .charge-info').clone().appendTo('#next-step').find('.section-sub-title').find('h4').text('Charge ' + chargeId).closest('.charge-info').attr('id', 'crg-' + chargeId);
					$('#crg-'+chargeId+' .add-addl-disposition').attr('data-id', 'crg-'+chargeId);
					$('#crg-'+chargeId+' .trigger.jail .accord-main').attr('id','jail-'+chargeId);
					$('#crg-'+chargeId+' .trigger.jail .label-accordion').attr('for','jail-'+chargeId);
					$('#crg-'+chargeId+' .trigger.prison .accord-main').attr('id','prison-'+chargeId);
					$('#crg-'+chargeId+' .trigger.prison .label-accordion').attr('for','prison-'+chargeId);
					$('#crg-'+chargeId+' .trigger.probation .accord-main').attr('id','probation-'+chargeId);
					$('#crg-'+chargeId+' .trigger.probation .label-accordion').attr('for','probation-'+chargeId);
					$('#crg-'+chargeId+' .trigger.license .accord-main').attr('id','license-'+chargeId);
					$('#crg-'+chargeId+' .trigger.license .label-accordion').attr('for','license-'+chargeId);
					$('#crg-'+chargeId+' .trigger.community .accord-main').attr('id','community-'+chargeId);
					$('#crg-'+chargeId+' .trigger.community .label-accordion').attr('for','community-'+chargeId);

					$('#crg-'+chargeId+' .form-field, #crg-'+chargeId+' input').attr('name', function() {
						return this.name.replace(/^charge\[.*?\]/, 'charge['+i+']');
					});

					// add value
					var charge = editCase.charge[i];
					for( var k in charge ) {

						$('[name="charge['+i+']['+k+']"]').val(charge[k]);

						if ( k == 'sentence' ) {
							var sentence = charge.sentence;

							if ( sentence ) {

								for ( var ks in sentence ) {

									if ( (ks == 'jail_time' || ks == 'jail_credit_time') && typeof sentence[ks].value !== 'undefined' && sentence[ks].value != '' ) {
										$('#jail-'+chargeId).prop('checked', true);
									}

									if ( (ks == 'prison_time' || ks == 'prison_credit_time') && typeof sentence[ks].value !== 'undefined' && sentence[ks].value != '' ) {
										$('#prison-'+chargeId).prop('checked', true);
									}

									if (ks == 'probation_type_id' && sentence[ks] > 1) {
										$('#probation-'+chargeId).prop('checked', true);
									}

									if (ks == 'probation_duration_time' && typeof sentence[ks].value !== 'undefined' && sentence[ks].value != '') {
										$('#probation-'+chargeId).prop('checked', true);
									}

									if ( ks == 'license_suspended_time' && typeof sentence[ks].value !== 'undefined' && sentence[ks].value != '' ) {
										$('#license-'+chargeId).prop('checked', true);
									}

									if ( ks == 'community_service_time' && typeof sentence[ks].value !== 'undefined' && sentence[ks].value != '' ) {
										$('#community-'+chargeId).prop('checked', true);
									}

									if ( ks == 'jail_suspended' && sentence[ks] == 'on' ) {
										$('#jail-'+chargeId).prop('checked', true);
									}

									if ( ks == 'prison_suspended' && sentence[ks] == 'on' ) {
										$('#prison-'+chargeId).prop('checked', true);
									}

									if ( sentence[ks] == 'on' ) {
										$('#crg-'+chargeId+' [name="charge['+i+']['+k+']['+ks+']"][type="checkbox"]').prop('checked', true);
									}

									if ( ks == 'jail_time' || ks == 'jail_credit_time' || ks == 'prison_time' || ks == 'prison_credit_time' || ks == 'probation_duration_time' || ks == 'license_suspended_time' || ks == 'community_service_time' ) {
										
										if ( $.isPlainObject(sentence[ks]) ) {
											var chargeTime = sentence[ks];

											if ( typeof chargeTime.value !== 'undefined' ) {
												$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+']['+chargeTime.unit+']"]').val(chargeTime.value);	
											} else {
												for ( var jtime in chargeTime ) {
													$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+']['+jtime+']"]').val(chargeTime[jtime]);
													if ( chargeTime[jtime] != '' ) {
														var sentenceParentTab = ks.split('_');
														$('#'+sentenceParentTab[0]+'-'+chargeId).prop('checked', true);
													}	
												}
											}
											
										} else {

											var str = sentence[ks].split(' ');

											if ( str.length > 1 ) {
												var unit = str[1].toLowerCase();
												$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+'][value]"]').val(str[0]);
												$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+'][unit]"]').val(dataAbbr[unit]);

												if ( (ks == 'jail_time' || ks == 'jail_credit_time') && sentence[ks] != '' ) {
													$('#jail-'+chargeId).prop('checked', true);
												}

												if ( (ks == 'prison_time' || ks == 'prison_credit_time') && sentence[ks] != '' ) {
													$('#prison-'+chargeId).prop('checked', true);
												}

												if ( ks == 'probation_duration_time' && sentence[ks] != '' ) {
													$('#probation-'+chargeId).prop('checked', true);
												}

												if ( ks == 'license_suspended_time' && sentence[ks] != '' ) {
													$('#license-'+chargeId).prop('checked', true);
												}

												if ( ks == 'community_service_time' && sentence[ks] != '' ) {
													$('#community-'+chargeId).prop('checked', true);
												}

											}

										}

									} else {
										$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+']"]:not([type="checkbox"])').val(sentence[ks]);
									}
									
								}
							}
						}
					}

					if ( typeof editCase.charge[i].addl_disposition !== 'undefined' ) {
						for( var n = 0; n < editCase.charge[i].addl_disposition.length; n++ ) {

							var dispositionId = n+1;

							$('#addl-disposition-fields .addl-disposition-content').clone().appendTo('#next-step #crg-'+chargeId+' .addl-disposition-container').attr('data-id', 'adis-'+dispositionId).find('.section-sub-title').find('h3').text('Additional Disposition '+dispositionId);

							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.jail .accord-main').attr('id', 'crg-'+chargeId+'jail-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.jail .label-accordion').attr('for', 'crg-'+chargeId+'jail-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.prison .accord-main').attr('id', 'crg-'+chargeId+'prison-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.prison .label-accordion').attr('for', 'crg-'+chargeId+'prison-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.probation .accord-main').attr('id', 'crg-'+chargeId+'probation-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.probation .label-accordion').attr('for', 'crg-'+chargeId+'probation-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.license .accord-main').attr('id', 'crg-'+chargeId+'license-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.license .label-accordion').attr('for', 'crg-'+chargeId+'license-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.community .accord-main').attr('id', 'crg-'+chargeId+'community-'+dispositionId);
							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .trigger.community .label-accordion').attr('for', 'crg-'+chargeId+'community-'+dispositionId);

							$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] .form-field, #crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] input').attr('name', function() {
								return this.name.replace(/^charge\[.*?\]\[addl_disposition\]\[.*?\]/, 'charge['+i+'][addl_disposition]['+n+']');
							});

							// add value
							var addisData = editCase.charge[i].addl_disposition[n];
							for( var adis in addisData ) {
								$('#crg-'+chargeId+' .addl-disposition-content[data-id="adis-'+dispositionId+'"] [name="charge['+i+'][addl_disposition]['+n+']['+adis+']"]:not([type="checkbox"])').val(addisData[adis]);

								if ( adis == 'sentence' ) {
									var adisSentence = addisData[adis];
									for ( var adsentence in adisSentence ) {
										if ( (adsentence == 'jail_time' || adsentence == 'jail_credit_time') && adisSentence[adsentence].value != '' ) {
											$('#crg-'+chargeId+'jail-'+dispositionId).prop('checked', true);
										}

										if ( (adsentence == 'prison_time' || adsentence == 'prison_credit_time') && adisSentence[adsentence].value != '' ) {
											$('#crg-'+chargeId+'prison-'+dispositionId).prop('checked', true);
										}

										if ( (adsentence == 'probation_type_id' && adisSentence[adsentence] > 1) || (adsentence == 'probation_duration_time' && adisSentence[adsentence].value != '') ) {
											$('#crg-'+chargeId+'probation-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'license_suspended_time' && adisSentence[adsentence].value != '' ) {
											$('#crg-'+chargeId+'license-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'community_service_time' && adisSentence[adsentence].value != '' ) {
											$('#crg-'+chargeId+'community-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'jail_suspended' && adisSentence[adsentence] == 'on' ) {
											$('#crg-'+chargeId+'jail-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'prison_suspended' && adisSentence[adsentence] == 'on' ) {
											$('#crg-'+chargeId+'prison-'+dispositionId).prop('checked', true);
										}

										//console.log(adsentence, adisSentence[adsentence]);
										//console.log('selector: ', '#crg-'+chargeId+' [name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+']"][type="checkbox"]');

										if ( adisSentence[adsentence] == 'on' ) {
											$('#crg-'+chargeId+' [name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+']"][type="checkbox"]').prop('checked', true);
										}

										if ( adsentence == 'jail_time' || adsentence == 'jail_credit_time' || adsentence == 'prison_time' || adsentence == 'prison_credit_time' || adsentence == 'probation_duration_time' || adsentence == 'license_suspended_time' || adsentence == 'community_service_time' ) {

											if ( $.isPlainObject(adisSentence[adsentence]) ) {
												$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+'][value]"]').val(adisSentence[adsentence].value);
												$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+'][value]"]').val(adisSentence[adsentence].value);
											} else {
												var str = adisSentence[adsentence].split(' ');

												if ( str.length > 1 ) {
													var unit = str[1].toLowerCase();
													$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+'][value]"]').val(str[0]);
													$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+'][unit]"]').val(dataAbbr[unit]);
												}
											}

										} else {
											$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+']"]:not([type="checkbox"])').val(adisSentence[adsentence]);
										}
										
									}
								}

							}

						}
					}
				}
			}

		<?php } ?>

		$('.input-date').each( function() {
			var str = $(this).val();
			var expl;
			if ( str != '' ) {
				expl = str.split('/');
				if ( expl[0].length == 1 ) {
					expl[0] = 0+expl[0];
				}

				if ( expl[1].length == 1 ) {
					expl[1] = 0+expl[1];
				}

				str = expl.join('/');
			}
			$(this).val(str);
		});
	<?php endif; ?>

	$('button.next-step').on('click', function(e) {
		e.preventDefault();
		$('#dynamic-fields .charge-info').clone().appendTo('#next-step');
		$(this).fadeOut();
		$('#next-step, #additional-charge, .submit-case').fadeIn();
		<?php /*$('#addl-disposition-fields .addl-disposition-content').clone().appendTo('#next-step #crg-1 .addl-disposition-container');*/ ?>
		var n = $("#next-step").offset().top;
    	$('html, body').animate({ scrollTop: n }, 500);
	});

	$('button.add-new-charge').on('click', function (e) {
		e.preventDefault();
		var totalCharge = $('#next-step .charge-info').length;
		var nextCharge = parseFloat(totalCharge)+1;
		$('#dynamic-fields .charge-info').clone().appendTo('#next-step').find('.section-sub-title').find('h4').text('Charge ' + nextCharge).closest('.charge-info').attr('id', 'crg-' + nextCharge);

		$('#crg-'+nextCharge+' .add-addl-disposition').attr('data-id', 'crg-'+nextCharge);
		
		$('#crg-'+nextCharge+' .trigger.jail .accord-main').attr('id','jail-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.jail .label-accordion').attr('for','jail-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.prison .accord-main').attr('id','prison-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.prison .label-accordion').attr('for','prison-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.probation .accord-main').attr('id','probation-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.probation .label-accordion').attr('for','probation-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.license .accord-main').attr('id','license-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.license .label-accordion').attr('for','license-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.community .accord-main').attr('id','community-'+nextCharge);
		$('#crg-'+nextCharge+' .trigger.community .label-accordion').attr('for','community-'+nextCharge);

		$('#crg-'+nextCharge+' .form-field, #crg-'+nextCharge+' input').attr('name', function() {
			return this.name.replace(/^charge\[.*?\]/, 'charge['+totalCharge+']');
		});

		<?php /*$('#addl-disposition-fields .addl-disposition-content').clone().appendTo('#next-step #crg-'+nextCharge+' .addl-disposition-container').attr('data-id', 'adis-1').find('.section-sub-title').find('h3').text('Additional Disposition 1');

		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.jail .accord-main').attr('id', 'crg-'+nextCharge+'jail-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.jail .label-accordion').attr('for', 'crg-'+nextCharge+'jail-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.prison .accord-main').attr('id', 'crg-'+nextCharge+'prison-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.prison .label-accordion').attr('for', 'crg-'+nextCharge+'prison-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.probation .accord-main').attr('id', 'crg-'+nextCharge+'probation-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.probation .label-accordion').attr('for', 'crg-'+nextCharge+'probation-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.license .accord-main').attr('id', 'crg-'+nextCharge+'license-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.license .label-accordion').attr('for', 'crg-'+nextCharge+'license-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.community .accord-main').attr('id', 'crg-'+nextCharge+'community-1');
		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .trigger.community .label-accordion').attr('for', 'crg-'+nextCharge+'community-1');

		$('#crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] .form-field, #crg-'+nextCharge+' .addl-disposition-content[data-id="adis-1"] input').attr('name', function() {
			return this.name.replace(/^charge\[.*?\]\[addl_disposition\]\[.*?\]/, 'charge['+totalCharge+'][addl_disposition][0]');
		});*/ ?>

		var n = $(document).height();
    	$('html, body').animate({ scrollTop: n }, 500);

	});

	$(document).on('click', '.add-addl-disposition', function(e) {
		var curId = $(this).attr('data-id');
		var splitId = curId.split('-');
		var parentId = splitId[1];
		var parentIndex = parentId-1;
		var totalDisp = $('#'+curId+' .addl-disposition-container .addl-disposition-content').length;

		var nextDisp = parseFloat(totalDisp)+1;

		$('#addl-disposition-fields .addl-disposition-content').clone().appendTo('#next-step #'+curId+' .addl-disposition-container').attr('data-id', 'adis-'+nextDisp).find('.section-sub-title').find('h3').text('Additional Disposition '+nextDisp);

		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.jail .accord-main').attr('id', curId+'jail-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.jail .label-accordion').attr('for', curId+'jail-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.prison .accord-main').attr('id', curId+'prison-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.prison .label-accordion').attr('for', curId+'prison-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.probation .accord-main').attr('id', curId+'probation-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.probation .label-accordion').attr('for', curId+'probation-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.license .accord-main').attr('id', curId+'license-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.license .label-accordion').attr('for', curId+'license-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.community .accord-main').attr('id', curId+'community-'+nextDisp);
		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .trigger.community .label-accordion').attr('for', curId+'community-'+nextDisp);

		$('#'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] .form-field, #'+curId+' .addl-disposition-content[data-id="adis-'+nextDisp+'"] input').attr('name', function() {
			return this.name.replace(/^charge\[.*?\]\[addl_disposition\]\[.*?\]/, 'charge['+parentIndex+'][addl_disposition]['+totalDisp+']');
		});

		var n = $(document).height();
		$('html, body').animate({ scrollTop: n }, 500);

	});

	$(document).on('change', '.accord-main', function(e) {
		e.preventDefault();
		if ( $(this).prop('checked') === false ) {
			$(this).closest('.trigger').find('.content').find('.input-field').find('.form-field').val('');
			$(this).closest('.trigger').find('.content').find('.input-field').find('[type="checkbox"]').prop('checked', false);
		}
	});

	$(document).on('click', 'span.cc-remove', function(e) {
		e.preventDefault();
		$(this).closest('.charge-info').remove();
	});

	$(document).on('click', 'span.adis-close', function(e) {
		e.preventDefault();
		$(this).closest('.addl-disposition-content').remove();
	});

	$("#caseForm").validationEngine('attach', {promptPosition : "bottomLeft", scroll: false});

	$('.input-date').on('blur', function() {
		var str = $(this).val();
		var expl;
		if ( str != '' ) {
			expl = str.split('/');
			if ( expl[0].length == 1 ) {
				expl[0] = 0+expl[0];
			}

			if ( expl[1].length == 1 ) {
				expl[1] = 0+expl[1];
			}

			str = expl.join('/');
		}
		$(this).val(str);
	});

});
</script>
</body>
</html>