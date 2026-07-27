<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?> 
<style><?php include FCPATH . 'assets/css/search-detail.css'; ?></style>
<style type="text/css" media="screen">
ul.case {
    width: 100%;
    max-width: 900px;
    margin: 0 auto 40px;
}
ul.case > .case-parent, li.sub {
    border-bottom: 1px solid #eee;
    padding: 5px;
}
li.sub:last-child {
    border: none;
}
ul.case .case-parent > label, .sub > label {
    font-weight: bold;
    font-family: 'Open Sans';
    width: 200px;
    margin-bottom: 0;
}
.cases-entered h4 {
    text-align: center;
    margin-top: 50px;
    margin-bottom: 20px;
    border-bottom: 4px double #999;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
    padding-bottom: 10px;
}
ul.criminal_charges {
    padding-left: 200px;
}
.cc-title {
    padding-bottom: 5px;
    margin-top: 10px;
    font-size: 16px;
    font-family: 'Open Sans';
    padding-left: 5px;
    border-bottom: 1px solid #999;
}
ul.sentences {
    padding-left: 200px;
}
table.notes {
    width: 100%;
    margin-top: 15px;
    margin-bottom: 15px;
}
table.notes td:first-child {
    width: 125px;
    font-weight: bold;
    font-family: 'Open Sans';
}
.notes tr td {
    padding-bottom: 10px;
}
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body col-md-12">
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
							<?php $cases = generate_cases($cases, $search_id, $search_type); ?>
							<div class="section">
								<div class="section-title">
									<h3>Cases Entered</h3>
								</div>

								<div class="cases-entered">
									<?php foreach ($cases as $key => $c) {
										echo '<h4>Case No. ' . $c['case_number'] . '</h4>';
										echo '<ul class="case">';
										foreach ($c as $label => $cval) {
											echo '<li class="case-parent"><label>'.$label.':</label> ';
											if ( ! is_array( $cval ) ) {

												if ( $label == 'identified_by_name' || $label == 'identified_by_dob' || $label == 'identified_by_ssn' ) {
													$cval = $cval == 1 ? 'yes' : '';
												}

												if ( $label == 'file_date' || $label == 'dob_on_file' ) {
													$cval = dob_format($cval);
												}

												if ( $label == 'case_disposition_id' ) {
													$cval = $cval . ' - ' . disposition_name($cval);
												}

												echo $cval;
											} else {

												if ( $label == 'criminal_charges' ) {
													foreach ($cval as $cckey => $cc) {
														echo '<div class="cc-title">Charge '.(intval($cckey)+1).'</div>';
														echo '<ul class="criminal_charges">';

														foreach ($cc as $cclabel => $ccval) {
															echo '<li class="sub"><label>'.$cclabel.':</label> ';

										    				if ( !is_array($ccval) ) {

									                            if ( $cclabel == 'charge_level_id' ) {
									                                $ccval = $ccval . ' - ' . charge_level_name($ccval);
									                            }

									                            if ( $cclabel == 'charge_disposition_id' ) {
									                                $ccval = $ccval . ' - ' . disposition_name($ccval);
									                            }

									                            if ( $cclabel == 'disposition_date' ) {
									                                $ccval = dob_format($ccval);
									                            }

										    					echo $ccval;

										    				} else {

									                            if ( $cclabel == 'sentences' ) {
									                                echo '<ul class="sentences">';
									                                foreach ($ccval[0] as $sclabel => $scval) {
									                                    echo '<li class="sub"><label>'.$sclabel.':</label> ';

									                                    if ( $sclabel == 'jail_suspended' || $sclabel == 'prison_suspended' ) {
									                                        $scval = $scval == 1 ? 'yes' : '';
									                                    }

									                                    if ( $sclabel == 'probation_type_id' ) {
									                                        $scval = $scval . ' - ' . probation_name($scval);
									                                    }

									                                    echo $scval;
									                                    echo '</li>';
									                                }
									                                echo '</ul>';
									                            }

									                            if ( $cclabel == 'addl_disposition' ) {
									                                foreach ($ccval as $adkey => $ad) {
									                                    echo '<div class="disp-title">Disposition '.(intval($adkey)+1).'</div>';
									                                    echo '<ul class="sentences">';
									                                    foreach ($ad as $adlabel => $adval) {
									                                        echo '<li class="sub"><label>'.$adlabel.':</label> ';
									                                        if ( $adlabel == 'addition_date' ) {
									                                            $adval = dob_format($adval);
									                                        }

									                                        if ( $adlabel == 'addition_type_id' ) {
									                                            $adval = $adval . ' - ' . addition_type_name($adval);
									                                        }

									                                        if ( $adlabel == 'addition_action_id' ) {
									                                            $adval = $adval . ' - ' . addition_action_type_name($adval);
									                                        }

									                                        if ( $adlabel == 'Jail Suspended' || $adlabel == 'rison_suspended' ) {
									                                            $adval = $adval == 1 ? 'yes' : '';
									                                        }

									                                        echo $adval;
									                                        echo '</li>';
									                                    }
									                                    echo '</ul>';
									                                }
									                            }

									                        }

										    				echo '</li>';
														}
												
														echo '</ul>';
									    			}
												}

												
											}
											echo '</li>';
										}
										
										echo '</ul>';
									} ?>
								</div>

								<h3>JSON</h3>
								<div class="cases-json"><pre><?php echo json_encode($cases, JSON_PRETTY_PRINT); ?></pre></div>
								
							</div>

						<?php else: ?>
							<div class="section">
								<div class="section-title">
									<h3>No Cases Entered</h3>
								</div>
							</div>
						<?php endif; ?>

						

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php ab_datepicker_scripts(); ?>
<script>
jQuery(document).ready(function ($) {
	$('.menu-item-searches').removeClass('active');
	$('.menu-item-history').addClass('active');
});
</script>
</body>
</html>