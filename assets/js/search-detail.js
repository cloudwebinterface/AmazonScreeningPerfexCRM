jQuery(document).ready(function ($) {

    $('#add-note-form').hide();
    $('#add-case-form').hide();

    // when add note button is clicked
    $(".add-note-button").click(function (e) {
        $('#add-note-form').show();
        $('#add-case-form').hide();
		$(this).addClass('active');
		$('body').addClass('myModal');
        $(".add-case-button").removeClass('active')
    });

    $('.add-case-button').click(function (e) {
        $('#add-case-form').show();
        $('#add-note-form').hide();
		$(".add-note-button").removeClass('active');
		$('body').addClass('myModal');
        $(this).addClass('active');
		var c_title = $('#add-case-form h2 > strong').text();
		c_title = c_title.replace('Edit', 'Add');
		$('#add-case-form input[type="submit"]').val('Submit case');
		$('#add-case-form h2 > strong').text(c_title);
		$('#add-case-form form').trigger("reset");
		if ( $('#add-case-form form > .submit-action-type').length > 0 ) {
			$('#add-case-form form > .submit-action-type').remove();
		}
		if ( $('#add-case-form form > .cid').length > 0 ) {
			$('#add-case-form form > .cid').remove();
		}
		$('#add-case-form input[type="radio"]').removeAttr('checked');
		$('#add-case-form input[type="radio"]').closest('.btn').removeClass('active');
		$('#criminal_charges_cache').attr('data-last', '').html('');
		$('#case_disposition_cache').attr('data-last', '').html('');
    });

	$('.edit-case').on('click', function(e) {
		e.preventDefault();
		$('#add-case-form').show();
		$('body').addClass('myModal');

		// reset form before loading the data
		$('#add-case-form form').trigger("reset");
		if ( $('#add-case-form form > .submit-action-type').length > 0 ) {
			$('#add-case-form form > .submit-action-type').remove();
		}
		if ( $('#add-case-form form > .cid').length > 0 ) {
			$('#add-case-form form > .cid').remove();
		}
		$('#add-case-form input[type="radio"]').removeAttr('checked');
		$('#add-case-form input[type="radio"]').closest('.btn').removeClass('active');
		$('#criminal_charges_cache').attr('data-last', '').html('');
		$('#case_disposition_cache').attr('data-last', '').html('');

		// replace string and add value
		var c_title = $('#add-case-form h2 > strong').text();
		var c_id = $(this).data('cid');
		var c_data = caseData[c_id];

		if ( $('#add-case-form form > .submit-action-type').length === 0 ) {
			$('#add-case-form form').prepend('<input type="hidden" name="submit-action-type" value="update" class="submit-action-type">');
		}

		if ( $('#add-case-form form > .cid').length === 0 ) {
			$('#add-case-form form').prepend('<input type="hidden" name="cid" value="'+c_id+'" class="cid">');
		}

		c_title = c_title.replace('Add', 'Edit');
		$('#add-case-form input[type="submit"]').val('Update case');
		$('#add-case-form h2 > strong').text(c_title);
		for( var k in c_data ) {
			if (c_data[k] == 'true' || c_data[k] == 'false') {
				$('input[name="'+k+'"]').closest('label').removeClass('active');
				$('input[name="'+k+'"][value="'+c_data[k]+'"]').attr('checked', 'checked').closest('label').addClass('active');
			} else {
				if ( k == 'case_sentence' ) {
					var cs_data = c_data[k];
					for ( var k_cs in cs_data ) {
						if ($('.'+k+'-'+k_cs).hasClass('radio')) {
							$('.'+k+'-'+k_cs+'.radio[value="'+cs_data[k_cs]+'"]').attr('checked', 'checked').closest('label').addClass('active');
						} else {
							$('.'+k+'-'+k_cs).val(cs_data[k_cs]);
						}
						
					}
				} else {
					$('#'+k).val(c_data[k]);
				}
				
			}
			
		}

		if ( searchType == 'F' ) {
            
            // criminal charges
            var total_cc = c_data['criminal_charges'].length;
            var cc = c_data['criminal_charges'];
            for ( var i = 0; i < total_cc; i++ ) {
                
                var criminalCharge = cc[i];
                var cc_label = criminalCharge.description;
                var jsonstring = JSON.stringify(cc[i]);
                var b64 = btoa(jsonstring);
                var ccid;

                ccid = i;
            
                $('#criminal_charges_cache').attr('data-last', i);
				
				$('#criminal_charges_cache').append('<div class="criminal_charges_item_group list-group-item cc-wrapper-'+ccid+'"><strong>'+cc_label+'</strong> <div class="cd-action pull-right"><a href="#" class="edit-criminal-charges" data-id="'+ccid+'">Edit</a> | <a href="#" class="delete-criminal-charges" data-id="'+ccid+'">Remove</a></div><input type="hidden" name="criminal_charges[]" class="criminal_charges_item ccid-'+ccid+'" data-id="'+ccid+'" value="'+b64+'"></div>');

            
            }

            // case dispositions
			var total_cd = c_data['case_addl_dispositions'].length;
            var cd = c_data['case_addl_dispositions'];

            for ( var cdi = 0; cdi < total_cd; cdi++ ) {
                var cdJsonstring = JSON.stringify(cd[cdi]);
                var cdb64 = btoa(cdJsonstring);
                var cdid = cdi;
            
                $('#case_disposition_cache').attr('data-last', cdi);

				$('#case_disposition_cache').append('<div class="case_addl_dispositions_item_group list-group-item">Case disposition '+(parseFloat(cdi)+1)+' <div class="cd-action pull-right"><a href="#" class="edit-case-disposition" data-id="'+cdid+'">Edit</a> | <a href="#" class="delete-case-disposition" data-id="'+cdid+'">Remove</a></div><input type="hidden" name="case_addl_dispositions[]" class="case_addl_dispositions_item cdid-'+cdid+'" data-id="'+cdid+'" value="'+cdb64+'"></div>');
            
            }

		}
	});

    $('.s-action-content span.close').click(function() {
        $('#add-note-form').hide();
		$('#add-case-form').hide();
		$('body').removeClass('myModal');
    });

    $(document).on('click', '.btn-add-dispositions', function(e) {
    	e.preventDefault();

    	var id, action;
    	id = $(this).attr('data-id');
    	action = $(this).attr('data-action');	

    	var dispData = {};
    	$('#casedispositionsModal input, #casedispositionsModal select, #casedispositionsModal textarea').each(function(index, el) {

    		if ( $(this).hasClass('radio') ) {
    			if ( $(this).prop("checked") ) {
    				dispData[$(this).attr('name')] = $(this).val();
    			}
    		} else {
    			dispData[$(this).attr('name')] = $(this).val();
    		}
	        
	    });

	    var jsonstring = JSON.stringify(dispData);
	    var b64 = btoa(jsonstring);

	    if ( action == 'edit' ) {
	    	$('.case_addl_dispositions_item.cdid-'+id).val(b64);
	    } else {

	    	var cdid;
            
			if ( typeof $('#case_disposition_cache').attr('data-last') === 'undefined' || $('#case_disposition_cache').attr('data-last') == '' ) {
				$('#case_disposition_cache').attr('data-last', 0);
				cdid = 0;
			} else {
				var oldId = $('#case_disposition_cache').attr('data-last');
				cdid = parseFloat(oldId)+1;
			}

		    var id = cdid;

		    console.log(cdid);
		    
		    $('#case_disposition_cache').append('<div class="case_addl_dispositions_item_group list-group-item">Case disposition '+(parseFloat(cdid)+1)+' <div class="cd-action pull-right"><a href="#" class="edit-case-disposition" data-id="'+cdid+'">Edit</a> | <a href="#" class="delete-case-disposition" data-id="'+cdid+'">Remove</a></div><input type="hidden" name="case_addl_dispositions[]" class="case_addl_dispositions_item cdid-'+cdid+'" data-id="'+cdid+'" value="'+b64+'"></div>');

		    $('#case_disposition_cache').attr('data-last', cdid);

	    }

	    console.log( 'save '+action+' '+id, dispData);

	    $('#casedispositionsModal').modal('hide');
    });

    $(document).on('click', '.case-disposition-trigger', function(e) {
    	$('#casedispositionsModalLabel').text('Add New Case Disposition');
    	$('.btn-add-dispositions').text('Add');
    	$('#casedispositionsModal input:not([type="radio"]), #casedispositionsModal select, #casedispositionsModal textarea').val('');
	    $('#casedispositionsModal input[type="radio"]').removeAttr('checked').closest('.btn').removeClass('active');
	    $('.btn-add-dispositions').attr({
    		'data-id': '',
    		'data-action': ''
    	});
    });

    $(document).on('click', '.edit-case-disposition', function(e) {
    	e.preventDefault();
    	$('#casedispositionsModal').modal('show');
    	$('#casedispositionsModalLabel').text('Edit Case Disposition');
    	$('.btn-add-dispositions').text('Update');

    	// reset 
    	$('#casedispositionsModal input[type="radio"]').removeAttr('checked').closest('.btn').removeClass('active');
    	var cd_id = $(this).data('id');
    	var cdval = $('input.cdid-'+cd_id).val();
    	var cdstr = atob(cdval);
    	var cd    = JSON.parse(cdstr);
    	console.log( 'open edit '+cd_id, cd);
    	$('.btn-add-dispositions').attr({
    		'data-id': cd_id,
    		'data-action': 'edit'
    	});
    	Object.keys(cd).forEach(key => {
			//console.log(key, cd[key]);
			$('#casedispositionsModal [name="'+key+'"]:not([type="radio"])').val(cd[key]);
			$('#casedispositionsModal [name="'+key+'"][type="radio"][value="'+cd[key]+'"]').attr('checked', 'checked').closest('.btn').addClass('active');
		});
    });

    $(document).on('click', '.delete-case-disposition', function (e) {
    	e.preventDefault();
    	var cd_id = $(this).data('id');
    	$(this).closest('.list-group-item').remove();
    });

    // CRIMINAL CHARGES
    $(document).on('click', '.criminal-charges-trigger', function(e) {
    	$('#CriminalChargesModalLabel').text('Add Criminal Charges');
    	$('.btn-add-criminal-charges').text('Add');
    	$('#CriminalChargesModal input:not([type="radio"]), #CriminalChargesModal select, #CriminalChargesModal textarea').val('');
	    $('#CriminalChargesModal input[type="radio"]').removeAttr('checked').closest('.btn').removeClass('active');
	    $('.btn-add-criminal-charges').attr({
    		'data-id': '',
    		'data-action': ''
    	});
    });

    
    $(document).on('submit', '#criminal-charges-form', function(e) {
    	e.preventDefault();

    	var id, action, dispData;
    	id = $('#criminal-charges-form .btn-add-criminal-charges').attr('data-id');
    	action = $('#criminal-charges-form .btn-add-criminal-charges').attr('data-action');	
    	dispData = $(this).serializeJSON();
    	var cc_label = dispData.description;

		var jsonstring = JSON.stringify(dispData);
	    var b64 = btoa(jsonstring);

	    if ( action == 'edit' ) {
	    	$('.cc-wrapper-'+id+' > strong').html(cc_label);
	    	$('.criminal_charges_item.ccid-'+id).val(b64);
	    } else {

	    	var ccid;
            
			if ( typeof $('#criminal_charges_cache').attr('data-last') === 'undefined' || $('#criminal_charges_cache').attr('data-last') == '' ) {
				$('#criminal_charges_cache').attr('data-last', 0);
				ccid = 0;
			} else {
				ccid = parseFloat($('#criminal_charges_cache').attr('data-last'))+1;
			}

		    var id = ccid;
		    
		    $('#criminal_charges_cache').append('<div class="criminal_charges_item_group list-group-item cc-wrapper-'+ccid+'"><strong>'+cc_label+'</strong> <div class="cd-action pull-right"><a href="#" class="edit-criminal-charges" data-id="'+ccid+'">Edit</a> | <a href="#" class="delete-criminal-charges" data-id="'+ccid+'">Remove</a></div><input type="hidden" name="criminal_charges[]" class="criminal_charges_item ccid-'+ccid+'" data-id="'+ccid+'" value="'+b64+'"></div>');

		    $('#criminal_charges_cache').attr('data-last', ccid);

	    }

	    console.log( 'save '+action+' '+id, dispData);

	    $('#CriminalChargesModal').modal('hide');
    });

    $(document).on('click', '.edit-criminal-charges', function(e) {
    	e.preventDefault();
    	$('#CriminalChargesModal').modal('show');
    	$('#CriminalChargesModalLabel').text('Edit Criminal Charges');
    	$('.btn-add-criminal-charges').text('Update');

    	// reset 
    	$('#CriminalChargesModal input[type="radio"]').removeAttr('checked').closest('.btn').removeClass('active');
    	var cc_id = $(this).data('id');
    	var ccval = $('input.ccid-'+cc_id).val();
    	var ccstr = atob(ccval);
    	var cc    = JSON.parse(ccstr);
    	console.log( 'open edit '+cc_id, cc);
    	$('.btn-add-criminal-charges').attr({
    		'data-id': cc_id,
    		'data-action': 'edit'
    	});
    	Object.keys(cc).forEach(key => {

			if ( $.isArray(cc[key]) ) {
				var arr = cc[key];
				for (var i in arr) {
				    // skip loop if the property is from prototype
				    if (!arr.hasOwnProperty(i)) continue;

				    var obj = arr[i];
				    console.log('obj', obj);
				    for (var prop in obj) {
				        // skip loop if the property is from prototype
				        if (!obj.hasOwnProperty(prop)) continue;

				        if ( $('#CriminalChargesModal .'+key+'-'+prop ).hasClass('radio') ) {
							$('#CriminalChargesModal .'+key+'-'+prop+'.'+obj[prop] ).attr('checked', 'checked').closest('.btn').addClass('active');
						} else {
							$( '#CriminalChargesModal .'+key+'-'+prop ).val(obj[prop]);
						}

				    }
				}

			} else {
				$('#CriminalChargesModal .'+key+':not([type="radio"])').val(cc[key]);
				$('#CriminalChargesModal .'+key+'[type="radio"][value="'+cc[key]+'"]').attr('checked', 'checked').closest('.btn').addClass('active');
			}
			
		});
    });

    $(document).on('click', '.delete-criminal-charges', function (e) {
    	e.preventDefault();
    	var cc_id = $(this).data('id');
    	$(this).closest('.list-group-item').remove();
    });

    $('.ab_errors').on('click', function() {
		$('.ab_errors').fadeOut();
	});
    
});