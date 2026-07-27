<?php defined('BASEPATH') or exit('No direct script access allowed');

class Submit extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('summary_model');
        $this->load->model('searches_model');
        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');
    }

	public function index() {
		show_404();
    	exit;
	}

    public function case() {
        if ( ! $_POST ) {
            show_404();
            exit;
        }

        $this->load->model('searches_model');

        $search_id      = $_POST['search_id'];
        $case_number    = $_POST['case_number'];

        if ( ! isset($_POST['cid']) ) {
            $check = $this->searches_model->case_exists($case_number, $search_id);
            if ( $check ) {
                redirect('/admin/search/' . $search_id );
                exit;
            }
        }

        $id             = isset($_POST['cid']) && $_POST['cid'] != '' ? $_POST['cid'] : time();
        $post_data      = $_POST;
        if (isset($post_data['submit_mode'])) {
            unset($post_data['submit_mode']);
        }
        if (isset($post_data['cid'])) {
            unset($post_data['cid']);
        }
        $cases          =  $this->searches_model->get_cases($search_id);
        $cases[$id]     = $post_data;

        $this->searches_model->submit_case($search_id, $cases);

        redirect('/admin/search/' . $search_id );
        exit;
    }

    public function all_searches() {

        $all_searches = $this->summary_model->get();

        if ( !$all_searches ) { redirect('/admin/summary'); exit; }

        $searches = array();
        foreach ($all_searches as $key => $search) {
            $search_id      = $search->search_ID;
            $search_info    = unserialize($search->orig_data);
            $search_type    = $search_info->search_type;
            $cases          = unserialize($search->cases);

            $searches[$key]['search_id'] = $search_id;
            $searches[$key]['search_status'] = 'F';
            if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {
                $searches[$key]['civil_cases'] = generate_cases($cases, $search_id, $search_type);
            } else {
                $searches[$key]['cases'] = generate_cases($cases, $search_id, $search_type);
            }

        }

        $data = array(
            'search_updates' => $searches
        );

        $data   = json_encode($data);
        $update = ab_update_searches_data($data);
        $t      = time();

        if ( isset($update->failed_updates) ) {
            $errors     = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
            $errors[$t] = json_encode($update);
            update_option('search_update_error_logs', serialize($errors), 'no');
        }

        if ( isset($update->completed_updates) ) {
            foreach ($update->completed_updates as $key => $value) {
                $sid = $value->search_id;
                $submit_date = array('sent_date' => $t);
                $this->searches_model->update($sid, 'F', $submit_date);
            }

            $success        = get_option('search_update_success_logs') ? unserialize(get_option('search_update_success_logs')) : array();
            $success[$t]    = json_encode($update);
            update_option('search_update_success_logs', serialize($success), 'no'); 
        }
        
        redirect('/admin/summary/?submit_status=success&t=' . $t, 'refresh' );
        exit;
    }

    private function charge_sentence_value($field, $location = '') {

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

    public function search() {
        if ( ! $_POST ) {
            show_404();
            exit;
        }

        if ( ! isset($_POST['search_id'])) {
            show_404();
            exit;
        }

        if ( $_POST['search_id'] == '' ) {
            show_404();
            exit;
        }

        $this->load->helper('search_meta_helper');
        $this->load->helper('api_helper');
        $this->load->helper('search_helper');
        $this->load->helper('settings_helper');
        $this->load->model('searches_model');

        $search_id      = $_POST['search_id'];
        $cases          = $this->searches_model->get_cases($search_id);
        $table_data     = $this->searches_model->get_detail($search_id);
        $search_info    = unserialize($table_data->orig_data);
        $search_type    = $search_info->search_type;
        $params         = array(
            'search_id'     => intval($search_id),
            'search_status' => 'F'
        );

        if ( ! $cases ) {
            redirect('/admin/search/' . $search_id);
            exit;
        }

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

                        $case_data['criminal_charges'][$n]["sentences"][0]["jail_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["jail_time"]);
                        $case_data['criminal_charges'][$n]["sentences"][0]["jail_suspended"] = isset($case["charge"][$n]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["jail_suspended"] : (bool) false;
                        $case_data['criminal_charges'][$n]["sentences"][0]["jail_credit_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["jail_credit_time"]);

                        // prison
                        $case_data['criminal_charges'][$n]["sentences"][0]["prison_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["prison_time"]);
                        $case_data['criminal_charges'][$n]["sentences"][0]["prison_suspended"] = isset($case["charge"][$n]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]["sentence"]["prison_suspended"] : (bool) false;
                        $case_data['criminal_charges'][$n]["sentences"][0]["prison_credit_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["prison_credit_time"]);

                        // probation
                        $case_data['criminal_charges'][$n]["sentences"][0]["probation_type_id"] = isset($case["charge"][$n]["sentence"]["probation_type_id"]) ? (int)$case["charge"][$n]["sentence"]["probation_type_id"] : 1;
                        $case_data['criminal_charges'][$n]["sentences"][0]["probation_duration_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["probation_duration_time"]); 

                        // license suspended
                        $case_data['criminal_charges'][$n]["sentences"][0]["license_suspended_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["license_suspended_time"]); 

                        // community service
                        $case_data['criminal_charges'][$n]["sentences"][0]["community_service_time"] = $this->charge_sentence_value($case["charge"][$n]["sentence"]["community_service_time"]);

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
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_time"]);
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_suspended"] : (bool) false;
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["jail_credit_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["jail_credit_time"]);

                                // prison
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_time"]);
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_suspended"] = isset($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"]) ? (bool)$case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_suspended"] : (bool) false;
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["prison_credit_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["prison_credit_time"]);

                                // probation
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["probation_duration_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["probation_duration_time"]);

                                // license suspended
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["license_suspended_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["license_suspended_time"]);

                                // community service
                                $case_data['criminal_charges'][$n]['addl_disposition'][$i]["community_service_time"] = $this->charge_sentence_value($case["charge"][$n]['addl_disposition'][$i]["sentence"]["community_service_time"]);
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

        

        $detail = array( $params );
        $data   = array(
            'search_updates' => $detail
        );
        $data   = json_encode($data);
        $update = ab_update_searches_data($data);
        $t      = time();

        if ( ! isset($update->completed_updates) ) {
            $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
            $errors[$t]  = json_encode($update);
            update_option('search_update_error_logs', serialize($errors), 'no');
            redirect('/admin/search/' . $search_id .'?errorId='.$t);
            exit;
        }

        foreach ($update->completed_updates as $key => $value) {
            $sid = $value->search_id;
            $sent_date = array('sent_date' => $t);
            $this->searches_model->update($sid, 'F', $sent_date);
        }

        
        $success        = get_option('search_update_success_logs') ? unserialize(get_option('search_update_success_logs')) : array();
        $success[$t]    = json_encode($update);
        update_option('search_update_success_logs', serialize($success), 'no');
        redirect('/admin/searches/?submit_status=success&sid=' . $search_id, 'refresh' );
        exit;
    }

    public function search_form() {

        if ( !isset($_POST['search_by'])) {
            show_404();
            exit;
        }

        $json = json_encode($_POST);
        $data = base64_encode($json);

        redirect( '/admin/searches/?search&fdata=' . $data );
        exit;
    }

    public function search_history_form() {

        if ( !isset($_POST['search_by'])) {
            show_404();
            exit;
        }

        $json = json_encode($_POST);
        $data = base64_encode($json);

        redirect( '/admin/history?search&fdata=' . $data );
        exit;
    }

}