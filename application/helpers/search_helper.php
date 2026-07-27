<?php

defined('BASEPATH') or exit('No direct script access allowed');

function search_type($code) {
	$stype = array(
		'F' => 'Felony',
		'ST' => 'Statewide Felony/Misdemeanor',
		'F/M' => 'Felony/Misdemeanor',
		'CIV' => 'Civil',
		'CIV-L' => 'Civil'
	);

	return $stype[$code];
}

function search_id_exists($search_id) {
	$CI = &get_instance();
	$sql = "SELECT search_ID FROM " . db_prefix() . "searches WHERE search_ID = ?";
	$query = $CI->db->query($sql, array( $search_id ));
	
	if ( $query->num_rows() > 0 ) {
		return true;
	}

	return false;
}

function convertToSortable($array, $field) {
	return array_diff(array_combine(array_keys($array), array_column($array, $field)), [null]);
}

function dob_format($dob = '') {
	if ( $dob == '' ) { return ''; }
	$dob_exp = explode( '-', $dob );
	$year = $dob_exp[0];
	$month = $dob_exp[1];
	$day = $dob_exp[2];

	return $month . '/' . $day . '/' . $year;
	
}

function dob_format_reverse($dob = '') {
	if ( $dob == '' ) { return ''; }
	$dob_exp 		= explode('/', $dob);
	$dob_exp[0] 	= strlen(trim($dob_exp[0])) == 1 ? '0' . trim($dob_exp[0]) : trim($dob_exp[0]);
	$dob_exp[1] 	= strlen(trim($dob_exp[1])) == 1 ? '0' . trim($dob_exp[1]) : trim($dob_exp[1]);
	$year 			= $dob_exp[2];
	$month 			= $dob_exp[0];
	$day 			= $dob_exp[1];

	return $year . '-' . $month . '-' . $day;
}

function date_validation($date = '', $reverse = '') {
	if ( $date == '' ) { return ''; }

	if ( $reverse == true ) {
		return dob_format_reverse($date);
	} else {
		$date_exp = explode('/', $date);
		$date_exp[0] = strlen(trim($date_exp[0])) == 1 ? '0' . trim($date_exp[0]) : trim($date_exp[0]);
		$date_exp[1] = strlen(trim($date_exp[1])) == 1 ? '0' . trim($date_exp[1]) : trim($date_exp[1]);
		return implode('/', $date_exp);
	}

}

function edit_case($edit_case, $key = '') {
	if ( !$edit_case ) { return ''; }
	if ( $key == '' ) { return ''; }
	$keys = explode( '>', $key);

	if ( count($keys) < 2 ) {
		return isset($edit_case[$key]) ? $edit_case[$key] : '';
	}


	switch (count($keys)) {
		case '2':
			$result = isset($edit_case[$keys[0]][$keys[1]]) ? $edit_case[$keys[0]][$keys[1]] : '';
		break;
		case '3':
			$result = isset($edit_case[$keys[0]][$keys[1]][$keys[2]]) ? $edit_case[$keys[0]][$keys[1]][$keys[2]] : '';
		break;
		case '4':
			$result = isset($edit_case[$keys[0]][$keys[1]][$keys[2]][$keys[3]]) ? $edit_case[$keys[0]][$keys[1]][$keys[2]][$keys[3]] : '';
		break;
		case '5':
			$result = isset($edit_case[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]]) ? $edit_case[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]] : '';
		break;
	}

	return $result;

}

function charge_sentence_value($field, $location = '') {

    $jail_time  = [];
    $field      = is_array($field) ? $field : [];
    if ( $field ) {
        foreach ( (isset($field) ? $field : []) as $jtkey => $jt ) {
            if ( $jt == '' ) { continue; }
            if ( $jtkey == 'value' || $jtkey == 'unit' ) { continue; }

            $unit = $jtkey;
            if ( $jt > 1 ) { $unit = $jtkey . 's'; }
            $jail_time[] = $jt . ' ' . $unit;
        }    
    }

    $jail_time_value = implode( ' ', $jail_time);

    return isset($field["value"]) && $field["value"] != '' ? unit_suffix($field["value"], $field["unit"]) : $jail_time_value;
}

function generate_cases($cases, $search_id, $search_type) {


    $case_list = array();

    foreach ($cases as $key => $case) {

        if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {

            $case_data = $case;

            unset($case_data['search_id']);
            unset($case_data['search_type']);
            unset($case_data['submit_mode']);
            unset($case_data['cid']);

            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other'] = boolval($case['identified_by_other']);
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

            if ( isset($case['case_type_id']) && $case['case_type_id'] != '' ) {
                $case_data['case_type_id']           = intval($case['case_type_id']);
            }

            if ( isset($case['civil_disposition_id']) && $case['civil_disposition_id'] != '' ) {
                $case_data['civil_disposition_id']   = intval($case['civil_disposition_id']);
            } 
            
            if ( isset($case['identified_by_dob']) && $case['identified_by_dob'] != '' ) {
                $case_data['identified_by_dob']      = (bool) $case['identified_by_dob'];
            } else {
                $case_data['identified_by_dob'] = (bool) false;
            }
            
            if ( isset( $case['identified_by_name'] ) && $case['identified_by_name'] != '' ) {
                $case_data['identified_by_name']     = (bool) $case['identified_by_name'];
            } else {
                $case_data['identified_by_name'] = (bool) false;
            }   
            
            if ( isset($case['identified_by_ssn']) && $case['identified_by_ssn'] != '' ) {
                $case_data['identified_by_ssn']      = (bool) $case['identified_by_ssn'];
            } else {
                $case_data['identified_by_ssn'] = (bool) false;
            }
            
            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other']    = (bool) $case['identified_by_other'];
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

        } else {

            $case_data = array();

            // basic info
            $case_data["case_number"] = $case['case_number'];
            $case_data["file_date"] = date_validation($case["file_date"], true);
            $case_data["identified_by_name"] = isset($case["identified_by_name"]) ? (bool)$case["identified_by_name"] : (bool)false;
            $case_data["identified_by_dob"] = isset($case["identified_by_dob"]) ? (bool)$case["identified_by_dob"] : (bool)false;
            $case_data["identified_by_ssn"] = isset($case["identified_by_ssn"]) ? (bool)$case["identified_by_ssn"] : (bool)false;
            $case_data["name_on_file"] = $case["name_on_file"];
            $case_data["dob_on_file"] = isset($case["dob_on_file"]) ? date_validation($case["dob_on_file"], true) : '';
            $case_data["ssn_on_file"] = isset($case["ssn_on_file"]) ? $case["ssn_on_file"] : '';
            $case_data["dl_state"] = isset($case["dl_state"]) ? $case["dl_state"] : '';
            $case_data["dl_number"] = isset($case["dl_number"]) ? $case["dl_number"] : '';
            $case_data["street_address"] = isset($case["street_address"]) ? $case["street_address"] : '';
            $case_data["city"] = isset($case["city"]) ? $case["city"] : '';
            $case_data["state"] = isset($case["state"]) ? $case["state"] : '';
            $case_data["zip_code"] = isset($case["zip_code"]) ? $case["zip_code"] : '';
            $case_data["addl_information"] = isset($case["addl_information"]) ? $case["addl_information"] : '';
            $case_data["case_disposition_id"] = (int)$case["charge"][0]["charge_disposition_id"];

            if ( isset($case['charge']) && count($case['charge']) > 0 ) {

                for ($n=0; $n < count($case['charge']); $n++) {

                    $case_data['criminal_charges'][$n]['description'] = $case["charge"][$n]['description'];
                    $case_data['criminal_charges'][$n]['charge_level_id'] = (int) $case["charge"][$n]['charge_level_id'];
                    $case_data['criminal_charges'][$n]['charge_disposition_id'] = (int) $case["charge"][$n]['charge_disposition_id'];
                    $case_data['criminal_charges'][$n]['disposition_date'] = isset($case["charge"][$n]['disposition_date']) ? date_validation($case["charge"][$n]['disposition_date'], true) : '';

                    // charge level sentence
            
                    // jail
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["jail_time"]);
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_suspended"] = isset($case["charge"][$n]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["jail_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_credit_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["jail_credit_time"]);
                    
                    // prison
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["prison_time"]);
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_suspended"] = isset($case["charge"][$n]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["prison_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_credit_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["prison_credit_time"]);

                    // probation
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_type_id"] = isset($case["charge"][$n]["sentence"]["probation_type_id"]) ? (int)$case["charge"][$n]["sentence"]["probation_type_id"] : 1;
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_duration_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["probation_duration_time"]);

                    // license suspended
                    $case_data['criminal_charges'][$n]["sentences"][0]["license_suspended_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["license_suspended_time"]);

                    // community service
                    $case_data['criminal_charges'][$n]["sentences"][0]["community_service_time"] = charge_sentence_value($case["charge"][$n]["sentence"]["community_service_time"]);

                    // costs
                    $case_data['criminal_charges'][$n]["sentences"][0]["fines"] = isset($case["charge"][$n]["fines"]) ? $case["charge"][$n]["fines"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["fees"] = isset($case["charge"][$n]["fees"]) ? $case["charge"][$n]["fees"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["costs"] = isset($case["charge"][$n]["costs"]) ? $case["charge"][$n]["costs"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["restitution"] = isset($case["charge"][$n]["restitution"]) ? $case["charge"][$n]["restitution"] : '';
                    
                    $case_data['criminal_charges'][$n]["sentences"][0]["classes_and_programs"] = isset($case["charge"][$n]["classes_and_programes"]) ? $case["charge"][$n]["classes_and_programes"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["addl_information"] = isset($case["charge"][$n]["addl_information"]) ? $case["charge"][$n]["addl_information"] : '';

                    if ( isset($case["charge"][$n]['addl_disposition']) && count($case["charge"][$n]['addl_disposition']) > 0 ) {
                        for ($i=0; $i < count($case["charge"][$n]['addl_disposition']); $i++) { 
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_date'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_date']) ? date_validation($case["charge"][$n]['addl_disposition'][$i]['addition_date'], true) : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_type_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_type_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_type_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_action_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_action_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_action_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['other'] = isset($case["charge"][$n]['addl_disposition'][$i]['other']) ? $case["charge"][$n]['addl_disposition'][$i]['other'] : '';

                            // jail
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"]);

                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_credit_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"]);

                            // prison
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"]);

                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_credit_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"]);

                            // probation
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["probation_duration_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"]);

                            // license suspended
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["license_suspended_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"]);

                            // community service
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["community_service_time"] = charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"]);

                        }
                        
                    }
                    
                }
                
            }

        }

        $case_list[] = $case_data;
    }

    return $case_list;
}

function cases_to_json($cases, $search_id) {

	$result 		= get_search_meta($search_id, 'search_data');
	$search_type 	= $result->search_type;
    $case_list 		= array();

    foreach ($cases as $key => $case) {

        if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {

            $case_data = $case;

            unset($case_data['search_id']);
            unset($case_data['search_type']);
            unset($case_data['submit_mode']);
            unset($case_data['cid']);

            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other'] = boolval($case['identified_by_other']);
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

            if ( isset($case['case_type_id']) && $case['case_type_id'] != '' ) {
                $case_data['case_type_id']           = intval($case['case_type_id']);
            }

            if ( isset($case['civil_disposition_id']) && $case['civil_disposition_id'] != '' ) {
                $case_data['civil_disposition_id']   = intval($case['civil_disposition_id']);
            } 
            
            if ( isset($case['identified_by_dob']) && $case['identified_by_dob'] != '' ) {
                $case_data['identified_by_dob']      = (bool) $case['identified_by_dob'];
            } else {
                $case_data['identified_by_dob'] = (bool) false;
            }
            
            if ( isset( $case['identified_by_name'] ) && $case['identified_by_name'] != '' ) {
                $case_data['identified_by_name']     = (bool) $case['identified_by_name'];
            } else {
                $case_data['identified_by_name'] = (bool) false;
            }   
            
            if ( isset($case['identified_by_ssn']) && $case['identified_by_ssn'] != '' ) {
                $case_data['identified_by_ssn']      = (bool) $case['identified_by_ssn'];
            } else {
                $case_data['identified_by_ssn'] = (bool) false;
            }
            
            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other']    = (bool) $case['identified_by_other'];
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

        } else {

            $case_data = array();

            // basic info
            $case_data["case_number"] = $case['case_number'];
            $case_data["file_date"] = date_validation($case["file_date"], true);
            $case_data["identified_by_name"] = isset($case["identified_by_name"]) ? (bool)$case["identified_by_name"] : (bool)false;
            $case_data["identified_by_dob"] = isset($case["identified_by_dob"]) ? (bool)$case["identified_by_dob"] : (bool)false;
            $case_data["identified_by_ssn"] = isset($case["identified_by_ssn"]) ? (bool)$case["identified_by_ssn"] : (bool)false;
            $case_data["name_on_file"] = $case["name_on_file"];
            $case_data["dob_on_file"] = isset($case["dob_on_file"]) ? date_validation($case["dob_on_file"], true) : '';
            $case_data["ssn_on_file"] = isset($case["ssn_on_file"]) ? $case["ssn_on_file"] : '';
            $case_data["dl_state"] = isset($case["dl_state"]) ? $case["dl_state"] : '';
            $case_data["dl_number"] = isset($case["dl_number"]) ? $case["dl_number"] : '';
            $case_data["street_address"] = isset($case["street_address"]) ? $case["street_address"] : '';
            $case_data["city"] = isset($case["city"]) ? $case["city"] : '';
            $case_data["state"] = isset($case["state"]) ? $case["state"] : '';
            $case_data["zip_code"] = isset($case["zip_code"]) ? $case["zip_code"] : '';
            $case_data["addl_information"] = isset($case["addl_information"]) ? $case["addl_information"] : '';
            $case_data["case_disposition_id"] = (int)$case["charge"][0]["charge_disposition_id"];
            //$case_data["case_disposition_date"] = isset($case["charge"][0]["disposition_date"]) ? date_validation($case["charge"][0]["disposition_date"], true) : '';


            if ( isset($case['charge']) && count($case['charge']) > 0 ) {

                for ($n=0; $n < count($case['charge']); $n++) {

                    $case_data['criminal_charges'][$n]['description'] = $case["charge"][$n]['description'];
                    $case_data['criminal_charges'][$n]['charge_level_id'] = (int) $case["charge"][$n]['charge_level_id'];
                    $case_data['criminal_charges'][$n]['charge_disposition_id'] = (int) $case["charge"][$n]['charge_disposition_id'];
                    $case_data['criminal_charges'][$n]['disposition_date'] = isset($case["charge"][$n]['disposition_date']) ? date_validation($case["charge"][$n]['disposition_date'], true) : '';

                    // charge level sentence
            
                    // jail
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_time"] = isset($case["charge"][$n]["sentence"]["jail_time"]) ? $case["charge"][$n]["sentence"]["jail_time"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_suspended"] = isset($case["charge"][$n]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["jail_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_credit_time"] = isset($case["charge"][$n]["sentence"]["jail_credit_time"]) ? $case["charge"][$n]["sentence"]["jail_credit_time"] : '';

                    // prison
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_time"] = isset($case["charge"][$n]["sentence"]["prison_time"]) ? $case["charge"][$n]["sentence"]["prison_time"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_suspended"] = isset($case["charge"][$n]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["prison_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_credit_time"] = isset($case["charge"][$n]["sentence"]["prison_credit_time"]) ? $case["charge"][$n]["sentence"]["prison_credit_time"] : '';

                    // probation
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_type_id"] = isset($case["charge"][$n]["sentence"]["probation_type_id"]) ? (int)$case["charge"][$n]["sentence"]["probation_type_id"] : 1;
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_duration_time"] = isset($case["charge"][$n]["sentence"]["probation_duration_time"]) ? $case["charge"][$n]["sentence"]["probation_duration_time"] : '';

                    // license suspended
                    $case_data['criminal_charges'][$n]["sentences"][0]["license_suspended_time"] = isset($case["charge"][$n]["sentence"]["license_suspended_time"]) ? $case["charge"][$n]["sentence"]["license_suspended_time"] : '';

                    // community service
                    $case_data['criminal_charges'][$n]["sentences"][0]["community_service_time"] = isset($case["charge"][$n]["sentence"]["community_service_time"]) ? $case["charge"][$n]["sentence"]["community_service_time"] : '';

                    // costs
                    $case_data['criminal_charges'][$n]["sentences"][0]["fines"] = isset($case["charge"][$n]["fines"]) ? $case["charge"][$n]["fines"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["fees"] = isset($case["charge"][$n]["fees"]) ? $case["charge"][$n]["fees"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["costs"] = isset($case["charge"][$n]["costs"]) ? $case["charge"][$n]["costs"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["restitution"] = isset($case["charge"][$n]["restitution"]) ? $case["charge"][$n]["restitution"] : '';
                    
                    $case_data['criminal_charges'][$n]["sentences"][0]["classes_and_programs"] = isset($case["charge"][$n]["classes_and_programes"]) ? $case["charge"][$n]["classes_and_programes"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["addl_information"] = isset($case["charge"][$n]["addl_information"]) ? $case["charge"][$n]["addl_information"] : '';

                    if ( isset($case["charge"][$n]['addl_disposition']) && count($case["charge"][$n]['addl_disposition']) > 0 ) {
                        for ($i=0; $i < count($case["charge"][$n]['addl_disposition']); $i++) { 
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_date'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_date']) ? date_validation($case["charge"][$n]['addl_disposition'][$i]['addition_date'], true) : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_type_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_type_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_type_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_action_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_action_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_action_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['other'] = isset($case["charge"][$n]['addl_disposition'][$i]['other']) ? $case["charge"][$n]['addl_disposition'][$i]['other'] : '';

                            // jail
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"] : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_credit_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"] : '';

                            // prison
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"] : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_credit_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"] : '';

                            // probation
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["probation_duration_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"] : '';

                            // license suspended
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["license_suspended_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"] : '';

                            // community service
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["community_service_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"] : '';
                        }
                        
                    }
                    
                }
                
            }

        }

        $case_list[] = $case_data;
    }
    

    if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {
        $params['civil_cases'] = $case_list;
    } else {
        $params['cases'] = $case_list;
    }

    
    $data = json_encode($params, JSON_PRETTY_PRINT);

    return $data;
}

function cases_to_list($cases, $search_id) {

	$result 		= get_search_meta($search_id, 'search_data');
	$search_type 	= $result->search_type;
    $case_list 		= array();

    foreach ($cases as $key => $case) {

        if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {

            $case_data = $case;

            unset($case_data['search_id']);
            unset($case_data['search_type']);
            unset($case_data['submit_mode']);
            unset($case_data['cid']);

            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other'] = boolval($case['identified_by_other']);
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

            if ( isset($case['case_type_id']) && $case['case_type_id'] != '' ) {
                $case_data['case_type_id']           = intval($case['case_type_id']);
            }

            if ( isset($case['civil_disposition_id']) && $case['civil_disposition_id'] != '' ) {
                $case_data['civil_disposition_id']   = intval($case['civil_disposition_id']);
            } 
            
            if ( isset($case['identified_by_dob']) && $case['identified_by_dob'] != '' ) {
                $case_data['identified_by_dob']      = (bool) $case['identified_by_dob'];
            } else {
                $case_data['identified_by_dob'] = (bool) false;
            }
            
            if ( isset( $case['identified_by_name'] ) && $case['identified_by_name'] != '' ) {
                $case_data['identified_by_name']     = (bool) $case['identified_by_name'];
            } else {
                $case_data['identified_by_name'] = (bool) false;
            }   
            
            if ( isset($case['identified_by_ssn']) && $case['identified_by_ssn'] != '' ) {
                $case_data['identified_by_ssn']      = (bool) $case['identified_by_ssn'];
            } else {
                $case_data['identified_by_ssn'] = (bool) false;
            }
            
            if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                $case_data['identified_by_other']    = (bool) $case['identified_by_other'];
            } else {
                $case_data['identified_by_other'] = (bool) false;
            }

        } else {

            $case_data = array();

            // basic info
            $case_data["case_number"] = $case['case_number'];
            $case_data["file_date"] = date_validation($case["file_date"], true);
            $case_data["identified_by_name"] = isset($case["identified_by_name"]) ? (bool)$case["identified_by_name"] : (bool)false;
            $case_data["identified_by_dob"] = isset($case["identified_by_dob"]) ? (bool)$case["identified_by_dob"] : (bool)false;
            $case_data["identified_by_ssn"] = isset($case["identified_by_ssn"]) ? (bool)$case["identified_by_ssn"] : (bool)false;
            $case_data["name_on_file"] = $case["name_on_file"];
            $case_data["dob_on_file"] = isset($case["dob_on_file"]) ? date_validation($case["dob_on_file"], true) : '';
            $case_data["ssn_on_file"] = isset($case["ssn_on_file"]) ? $case["ssn_on_file"] : '';
            $case_data["dl_state"] = isset($case["dl_state"]) ? $case["dl_state"] : '';
            $case_data["dl_number"] = isset($case["dl_number"]) ? $case["dl_number"] : '';
            $case_data["street_address"] = isset($case["street_address"]) ? $case["street_address"] : '';
            $case_data["city"] = isset($case["city"]) ? $case["city"] : '';
            $case_data["state"] = isset($case["state"]) ? $case["state"] : '';
            $case_data["zip_code"] = isset($case["zip_code"]) ? $case["zip_code"] : '';
            $case_data["addl_information"] = isset($case["addl_information"]) ? $case["addl_information"] : '';
            $case_data["case_disposition_id"] = (int)$case["charge"][0]["charge_disposition_id"];
            //$case_data["case_disposition_date"] = isset($case["charge"][0]["disposition_date"]) ? date_validation($case["charge"][0]["disposition_date"], true) : '';


            if ( isset($case['charge']) && count($case['charge']) > 0 ) {

                for ($n=0; $n < count($case['charge']); $n++) {

                    $case_data['criminal_charges'][$n]['description'] = $case["charge"][$n]['description'];
                    $case_data['criminal_charges'][$n]['charge_level_id'] = (int) $case["charge"][$n]['charge_level_id'];
                    $case_data['criminal_charges'][$n]['charge_disposition_id'] = (int) $case["charge"][$n]['charge_disposition_id'];
                    $case_data['criminal_charges'][$n]['disposition_date'] = isset($case["charge"][$n]['disposition_date']) ? date_validation($case["charge"][$n]['disposition_date'], true) : '';

                    // charge level sentence
            
                    // jail
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_time"] = isset($case["charge"][$n]["sentence"]["jail_time"]) ? $case["charge"][$n]["sentence"]["jail_time"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_suspended"] = isset($case["charge"][$n]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["jail_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["jail_credit_time"] = isset($case["charge"][$n]["sentence"]["jail_credit_time"]) ? $case["charge"][$n]["sentence"]["jail_credit_time"] : '';

                    // prison
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_time"] = isset($case["charge"][$n]["sentence"]["prison_time"]) ? $case["charge"][$n]["sentence"]["prison_time"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_suspended"] = isset($case["charge"][$n]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["prison_suspended"] : (bool) false;
                    $case_data['criminal_charges'][$n]["sentences"][0]["prison_credit_time"] = isset($case["charge"][$n]["sentence"]["prison_credit_time"]) ? $case["charge"][$n]["sentence"]["prison_credit_time"] : '';

                    // probation
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_type_id"] = isset($case["charge"][$n]["sentence"]["probation_type_id"]) ? (int)$case["charge"][$n]["sentence"]["probation_type_id"] : 1;
                    $case_data['criminal_charges'][$n]["sentences"][0]["probation_duration_time"] = isset($case["charge"][$n]["sentence"]["probation_duration_time"]) ? $case["charge"][$n]["sentence"]["probation_duration_time"] : '';

                    // license suspended
                    $case_data['criminal_charges'][$n]["sentences"][0]["license_suspended_time"] = isset($case["charge"][$n]["sentence"]["license_suspended_time"]) ? $case["charge"][$n]["sentence"]["license_suspended_time"] : '';

                    // community service
                    $case_data['criminal_charges'][$n]["sentences"][0]["community_service_time"] = isset($case["charge"][$n]["sentence"]["community_service_time"]) ? $case["charge"][$n]["sentence"]["community_service_time"] : '';

                    // costs
                    $case_data['criminal_charges'][$n]["sentences"][0]["fines"] = isset($case["charge"][$n]["fines"]) ? $case["charge"][$n]["fines"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["fees"] = isset($case["charge"][$n]["fees"]) ? $case["charge"][$n]["fees"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["costs"] = isset($case["charge"][$n]["costs"]) ? $case["charge"][$n]["costs"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["restitution"] = isset($case["charge"][$n]["restitution"]) ? $case["charge"][$n]["restitution"] : '';
                    
                    $case_data['criminal_charges'][$n]["sentences"][0]["classes_and_programs"] = isset($case["charge"][$n]["classes_and_programes"]) ? $case["charge"][$n]["classes_and_programes"] : '';
                    $case_data['criminal_charges'][$n]["sentences"][0]["addl_information"] = isset($case["charge"][$n]["addl_information"]) ? $case["charge"][$n]["addl_information"] : '';

                    if ( isset($case["charge"][$n]['addl_disposition']) && count($case["charge"][$n]['addl_disposition']) > 0 ) {
                        for ($i=0; $i < count($case["charge"][$n]['addl_disposition']); $i++) { 
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_date'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_date']) ? date_validation($case["charge"][$n]['addl_disposition'][$i]['addition_date'], true) : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_type_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_type_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_type_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['addition_action_id'] = isset($case["charge"][$n]['addl_disposition'][$i]['addition_action_id']) ? (int)$case["charge"][$n]['addl_disposition'][$i]['addition_action_id'] : (int) 1;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]['other'] = isset($case["charge"][$n]['addl_disposition'][$i]['other']) ? $case["charge"][$n]['addl_disposition'][$i]['other'] : '';

                            // jail
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"] : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_credit_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"] : '';

                            // prison
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"] : '';
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"] : (bool) false;
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_credit_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"] : '';

                            // probation
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["probation_duration_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"] : '';

                            // license suspended
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["license_suspended_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"] : '';

                            // community service
                            $case_data['criminal_charges'][$n]['addl_disposition'][$i]["community_service_time"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"]) ? $case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"] : '';
                        }
                        
                    }
                    
                }
                
            }

        }

        $case_list[] = $case_data;
    }

    ob_start();
    
    
    foreach ($case_list as $key => $c) {
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
    }
    

    return ob_get_clean();
}

function disposition_name($id) {
	$dispositions = convertToSortable(json_decode( get_option('table_Dispositions'), true ), 'description');
	return ($id == '1' ? 'None' : $dispositions[$id] );
}

function charge_level_name($id) {
    $cl = convertToSortable( json_decode( get_option('table_ChargeLevels'), true ), 'description' );
    return ($id == '1' ? 'None' : $cl[$id]);
}

function probation_name($id) {
    $pr = convertToSortable( json_decode( get_option('table_ProbationTypes'), true ), 'description' );

    return ($id == '1' ? 'None' : $pr[$id]);
}

function addition_type_name($id) {
    $pr = convertToSortable( json_decode( get_option('table_AdditionTypes'), true ), 'description' );

    return ($id == '1' ? 'None' : $pr[$id]);
}

function addition_action_type_name($id) {
    $pr = convertToSortable( json_decode( get_option('table_AdditionActionTypes'), true ), 'description' );

    return ($id == '1' ? 'None' : $pr[$id]);
}

function unit_suffix($val, $suffix) {
    if ( $val > 1 ) {
        return $val . ' ' . $suffix . 's';
    } else {
        return $val . ' ' . $suffix;
    }
}