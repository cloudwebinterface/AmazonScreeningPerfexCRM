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

</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body col-md-12">
						<div class="page-title">
							<h2>Criminal Case Information</h2>
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
						
						<?php if ( $cases ): ?>
							<div class="section">
								<div class="section-title">
									<h3>Cases Entered</h3>
								</div>

								<div class="cases-entered">
									<?php echo cases_to_list($cases, $search_id); ?>
								</div>

								<h3>JSON</h3>
								<div class="cases-json"><pre><?php echo cases_to_json($cases, $search_id); ?></pre></div>
								
							</div>
						<?php endif; ?>

						

					</div>
				</div>
			</div>
		</div>
	</div>
</div>


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

<script>
jQuery(document).ready(function ($) {

	$('.ab_errors').on('click', function() {
		$('.ab_errors').fadeOut();
	});

	<?php if ( isset($_GET['cid']) && $edit_case ): ?>
		var editCase = <?php echo json_encode($edit_case); ?>;

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

									if ( (ks == 'jail_time' || ks == 'jail_credit_time') && sentence[ks] != '' ) {
										$('#jail-'+chargeId).prop('checked', true);
									}

									if ( (ks == 'prison_time' || ks == 'prison_credit_time') && sentence[ks] != '' ) {
										$('#prison-'+chargeId).prop('checked', true);
									}

									if ( (ks == 'probation_type_id' && sentence[ks] > 1) || (ks == 'probation_duration_time' && sentence[ks] != '') ) {
										$('#probation-'+chargeId).prop('checked', true);
									}

									if ( ks == 'license_suspended_time' && sentence[ks] != '' ) {
										$('#license-'+chargeId).prop('checked', true);
									}

									if ( ks == 'community_service_time' && sentence[ks] != '' ) {
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

									$('#crg-'+chargeId+' .form-field[name="charge['+i+']['+k+']['+ks+']"]:not([type="checkbox"])').val(sentence[ks]);
									
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
										if ( (adsentence == 'jail_time' || adsentence == 'jail_credit_time') && adisSentence[adsentence] != '' ) {
											$('#crg-'+chargeId+'jail-'+dispositionId).prop('checked', true);
										}

										if ( (adsentence == 'prison_time' || adsentence == 'prison_credit_time') && adisSentence[adsentence] != '' ) {
											$('#crg-'+chargeId+'prison-'+dispositionId).prop('checked', true);
										}

										if ( (adsentence == 'probation_type_id' && adisSentence[adsentence] > 1) || (adsentence == 'probation_duration_time' && adisSentence[adsentence] != '') ) {
											$('#crg-'+chargeId+'probation-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'license_suspended_time' && adisSentence[adsentence] != '' ) {
											$('#crg-'+chargeId+'license-'+dispositionId).prop('checked', true);
										}

										if ( adsentence == 'community_service_time' && adisSentence[adsentence] != '' ) {
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

										$('#crg-'+chargeId+' .form-field[name="charge['+i+'][addl_disposition]['+n+']['+adis+']['+adsentence+']"]:not([type="checkbox"])').val(adisSentence[adsentence]);
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