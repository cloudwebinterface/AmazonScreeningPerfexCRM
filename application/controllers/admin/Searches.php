<?php defined('BASEPATH') or exit('No direct script access allowed');

class Searches extends AdminController
{
	public function index() 
    {
		
        $data['title']  = 'Searches - AmazonScreening';
        $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
        $data['errors'] = $errors;

        $this->load->model('searches_model');

        $data['total_rows'] = $this->searches_model->total_rows(['search_status' => 'P']);
        $data['duplicate_searches'] = $this->searches_model->get_duplicate_searches();

        // check search table first
        $this->searches_model->check_tables();
        $this->load->view( 'admin/searches/searches', $data );
	}

    public function list() 
    {

        $data['title']  = 'Searches - AmazonScreening';
        $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
        $data['errors']  = $errors;

        $this->load->model('searches_model');

        // check search table first
        $this->searches_model->check_tables();
        $this->load->view( 'admin/searches/employee-searches', $data );
    }

    public function search_id($search_id = '') {
        $this->load->helper('api_helper');
        $this->load->model('searches_model');
        $response = ab_reload_search_data($search_id);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }

    public function all() {
        $this->load->helper('settings_helper');
        $searches = unserialize(get_option( 'unprocessed_searches' ));

        echo '<pre>';
        print_r($searches);
        echo '</pre>';
    }

    public function get_data() {
        $offset     = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit      = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw       = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $where      = array('search_status' => 'P');
        $counties   = json_decode( get_option('table_Counties'), true ); 
        $order      = array();

        /*if ( isset($_POST['order'][0]['dir']) && isset($_POST['order'][0]['column']) ) {
            
            $columns = array(
                1 => 'list_id',
                2 => 'search_ID'
            );
            $column = $_POST['order'][0]['column'];

            $order['orderby'] = $columns[$column];
            $order['order'] = strtoupper($_POST['order'][0]['dir']);
        }*/

        $order['orderby'] = 'first_name';
        $order['order'] = 'asc';

        $this->load->model('searches_model'); 

        if ( isset($_POST['cSearch']) ) {
            $cSearch = $_POST['cSearch'];

            if ( $cSearch['search_by'] == 'search_id' ) {
                $data = $this->searches_model->get( $cSearch['search_value'], $where, $limit, $offset, $draw, $order, $counties );

                if ( $data ) {
                    $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
                } else {
                    $this->output->set_content_type('application/json')->set_output( json_encode( array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array() ) ) );
                }
                
            } else {

                $search_by = $cSearch['search_by'];

                switch ($search_by) {
                    case 'state':
                        $state = $cSearch['search_value'];
                        $where['state'] = $state;
                    break;

                    case 'name':
                        if ( $cSearch['first_name'] != '' ) {
                            $where['first_name'] = $cSearch['first_name'];
                        }

                        if ( $cSearch['middle_name'] != '' ) {
                            $where['middle_name'] = $cSearch['middle_name'];
                        }

                        if ( $cSearch['last_name'] != '' ) {
                            $where['last_name'] = $cSearch['last_name'];
                        }
                        
                    break;
                    
                }

                $data = $this->searches_model->get( '', $where, $limit, $offset, $draw, $order, $counties );
                if ( $data ) {
                    $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
                } else {
                    $this->output->set_content_type('application/json')->set_output( json_encode( array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array() ) ) );
                }
            }
            
        } else {

            $data = $this->searches_model->get( '', $where, $limit, $offset, $draw, $order, $counties );

            if ( $data ) {
                $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
            } else {
                $this->output->set_content_type('application/json')->set_output( json_encode( array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array() ) ) );
            }

        }
        
    }

	public function search_detail($id = '') {

        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->helper('countries_helper');
        $this->load->model('searches_model');

        $this->searches_model->remove_from_new_list($id);

    	$table_data                     = $this->searches_model->get_detail($id);
        $result                         = unserialize($table_data->orig_data);
        $data['search_id']              = $id;
        $data['search_type']            = $result->search_type;
        $cases                          = $table_data->cases != '' ? unserialize($table_data->cases) : array();
        $data['cases']                  = $cases;
        $data['edit_case']              = isset($_GET['cid']) && $_GET['cid'] != '' && isset($cases[$_GET['cid']]) ? $cases[$_GET['cid']] : false;             
        $data['data']                   = $result;
        $data['title']                  = 'Search Detail ' . $id . ' - AmazonScreening';
        $data['counties']               = json_decode( get_option('table_Counties'), true ); 
        $data['states']                 = us_states();
        $dispositions                   = convertToSortable(json_decode( get_option('table_Dispositions'), true ), 'description');

        asort($dispositions);

        $data['dispositions']           = $dispositions;

        $charge_level                   = convertToSortable( json_decode( get_option('table_ChargeLevels'), true ), 'description' );
        asort($charge_level);
        $data['chargeLevel']            = $charge_level;

        $data['caseTypeId']             = json_decode( get_option('table_CivilCaseType'), true ); 
        $data['probationTypes']         = json_decode( get_option('table_ProbationTypes'), true ); 
        $data['additionTypes']          = json_decode( get_option('table_AdditionTypes'), true ); 
        $data['additionActionTypes']    = json_decode( get_option('table_AdditionActionTypes'), true ); 
        $data['civilDisposition']       = json_decode( get_option('table_CivilCaseDispositions'), true ); 

        $where = array(
            'first_name'    => $table_data->first_name,
            'last_name'     => $table_data->last_name
        );

        $middle_name                    = isset($table_data->middle_name) && $table_data->middle_name != '' ? $table_data->middle_name : '';
        if ( $middle_name != '' ) {
            $where['middle_name'] = $middle_name;
        }

        $duplicates                     = $this->searches_model->get_searches( '', $where );
		$multiple_entries               = array();
        $ssn                            = $result->subject->ssn;
        $previous_data                  = [];

        if ( $ssn ) {
            $old_searches               = $this->searches_model->get_searches_by_ssn($ssn);
            if ( $old_searches ) {
                foreach( $old_searches as $idkey => $old_data ) {
                    $old_orig_data      = unserialize($old_data['orig_data']);
                    $previous_data[$idkey] = [
                        'search_id' => $old_data['search_ID'],
                        'first_name' => $old_data['first_name'],
                        'middle_name' => $old_data['middle_name'],
                        'last_name' => $old_data['last_name'],
                        'ssn' => $old_orig_data->subject->ssn,
                        'status' => $old_data['search_status']
                    ];
                }
            }
        }

        $data['previous_data']          = $previous_data;
		
        $same_ssn = array();
        if ( count($duplicates) > 1 ) {

            foreach ($duplicates as $idx => $s) {
                $od = unserialize($s->orig_data);
                if ( $result->subject->ssn != $od->subject->ssn ) { continue; }
                $same_ssn[] = $s->search_ID;
				$multiple_entries[] = $s->search_ID;
            }

            if ( count($same_ssn) > 1 ) {
                $has_duplicate = true; 
            } else {
                $has_duplicate = false; 
            }

        } else {

            $has_duplicate = false;

        }

        $duplicated                     = get_search_meta($id, 'already_cloned', true);
        
        $data['have_duplicates']        = $has_duplicate;
        $data['total_duplicates']       = count($same_ssn);
        $data['already_duplicated']     = $duplicated;
		$data['multiple_entries']       = $multiple_entries;
        
        $this->load->view( 'admin/searches/search_detail', $data );

	}

    public function update_search() {
        $this->load->helper('url');
        $this->load->helper('api_helper');
        $this->load->helper('search_meta_helper');
        
        $submit_mode    = $this->input->post('submit-mode');
        $data           = array();

        $this->load->model('searches_model');

        switch ($submit_mode) {
            case 'no-record':
            
                if ( ! isset($_POST['selected_list']) ) {
                    redirect('/admin/searches', 'refresh');
                }
                $detail = array(); 
                $search_ids = $this->input->post('selected_list');
                foreach ($search_ids as $key => $search_id) {
                    $detail[] = array(
                        'search_id' => $search_id,
                        'search_status' => 'N'
                    );
                }
    
                $data   = array(
                    'search_updates' => $detail
                );
                $data   = json_encode($data);
                $update = ab_update_searches_data($data);
                $t      = time();

                if ( isset($update->completed_updates) ) {

                    foreach ($update->completed_updates as $key => $c) {
                        $sent_date = array('sent_date' => $t);
                        $this->searches_model->update($c->search_id, 'N', $sent_date);
                    }
                    
                    $success        = get_option('search_update_success_logs') ? unserialize(get_option('search_update_success_logs')) : array();
                    $success[$t]    = json_encode($update);
                    update_option('search_update_success_logs', serialize($success), 'no');
                }

                if ( isset($update->failed_updates) ) {
                    $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
                    $errors[$t]     = json_encode($update);
                    update_option('search_update_error_logs', serialize($errors), 'no');
                }

                redirect('/admin/searches', 'refresh');
                exit;

            break;
            case 'note':
                $search_id = $this->input->post('search_id');
                $note = $this->input->post('note');
                $eta = $this->input->post('eta');

                $params = array(
                    'search_id' => $search_id
                );

                if ( $note ) {
                    $params['note_additions'] = array($note);
                } else {
                    $note = '';
                }

                if ( $eta ) {
                    $params['eta'] = $eta;
                } else {
                    $eta = '';
                }

                if ( $note == '' && $eta == '' ) {
                    echo '<pre>';
                    echo json_encode(array('status' => 'error', 'message' => 'Sending empty data huh?'));
                    echo '</pre>';
                    exit;
                }

                $detail = array( $params );

                $data = array(
                    'search_updates' => $detail
                );
                
                $data = json_encode($data);

                $update = ab_update_searches_data($data);

                if ( ! isset($update->completed_updates) ) {
                    echo '<pre>';
                    print_r($update);
                    echo '</pre>';
                    $errors = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
                    $current = date('Y/m/d H:i', time());
                    $errors[time()] = json_encode($update);
                    update_option('search_update_error_logs', serialize($errors), 'no');
                    exit;
                }

                foreach ($update->completed_updates as $key => $value) {
                    $sid = $value->search_id;
                    $search_data = ab_reload_search_data($sid);
                    $this->searches_model->add($search_data);
                }

                redirect('/admin/search/' . $search_id, 'refresh');
                exit;

            break;

            case 'cases':
                
                $post_data = $this->input->post();
                $submit_action_type = $this->input->post('submit-action-type');
                $search_type = $this->input->post('search_type');
                $search_id = $this->input->post('search_id');
                $type_civil = array('CIV', 'CIV-L');

                // unset no-needed field
                unset($post_data['search_id']);
                unset($post_data['search_type']);
                unset($post_data['submit-mode']);

                if ( in_array( $search_type, $type_civil ) ) {

                    // get cases from db
                    $civ_cases = get_search_meta( $search_id, 'cases' );

                    if ( $submit_action_type == 'update' ) {
                        unset($post_data['cid']);
                        unset($post_data['submit-action-type']);
                        $cid = $_POST['cid'];
                        $civ_cases[$cid] = $post_data;
                        update_search_meta( $search_id, 'cases', $civ_cases );
                        redirect('/admin/search/' . $search_id );
                        exit;
                    }

                    $id = time();
                    $civ_cases[$id] = $post_data;

                    update_search_meta( $search_id, 'cases', $civ_cases );

                    redirect('/admin/search/' . $search_id);
                    exit;

                } else {

                    $f_cases            = get_search_meta( $search_id, 'cases' );
                    $data               = $post_data;
                    $cad                = isset($data['case_addl_dispositions']) ? $data['case_addl_dispositions'] : false;
                    $case_disposition   = array();
                    if ( $cad ) {
                        foreach ($cad as $key => $c_disp) {
                            $b64_dec = base64_decode($c_disp);
                            $case_disposition[] = json_decode( $b64_dec, true );
                        }
                    }

                    $cmcgs              = isset($data['criminal_charges']) ? $data['criminal_charges'] : false;
                    $criminal_charges   = array();
                    if ( $cmcgs ) {
                        foreach ($cmcgs as $key => $criminalcharges ) {
                            $b64_dec = base64_decode($criminalcharges);
                            $criminal_charges[] = json_decode( $b64_dec, true );
                        }
                    }
                    $data['case_addl_dispositions'] = $case_disposition;
                    $data['criminal_charges'] = $criminal_charges;

                    if ( $submit_action_type == 'update' ) {

                        unset($data['cid']);
                        unset($data['submit-action-type']);

                        $cid = $_POST['cid'];
                        $f_cases[$cid] = $data;

                        update_search_meta( $search_id, 'cases', $f_cases );
                        redirect('/admin/search/' . $search_id );
                        exit;
                    }

                    $id = time();
                    $f_cases[$id] = $data;

                    update_search_meta( $search_id, 'cases', $f_cases );

                    redirect('/admin/search/' . $search_id);
                    exit;
                }
            
            break;
        }

    }

    public function delete_case($search_id = '', $id = '') {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }

        $this->load->model('searches_model');

        // get cases data
        $cases = $this->searches_model->get_cases($search_id);

        if ( isset($cases[$id]) ) {
            unset($cases[$id]);

            if ( $cases ) {
                $this->searches_model->submit_case($search_id, $cases);
            } else {
                $this->searches_model->submit_case($search_id, '');
            }

        }
        
        redirect('/admin/search/' . $search_id);
        exit;
    }

    public function new_requests() {
        $this->output
        ->set_content_type('application/json')
        ->set_output( json_encode(
            array(
                'status' => 'error',
                'message' => 'something wrong with the back-end, please check script logs.'
            )
        ) );
    }

    public function submit_case($search_id = '') {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }

        if ( $search_id == '') {
            redirect('/admin/search/' . $search_id);
            exit;
        }

        $this->load->helper('search_meta_helper');
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');
        $this->load->model('searches_model');

        // get cases data
        $cases          = get_search_meta($search_id, 'cases');
        $search_type    = get_search_meta($search_id, 'search_type');
        $params         = array(
            'search_id' => intval($search_id)
        );

        if ( ! $cases ) {
            redirect('/admin/search/' . $search_id);
            exit;
        }

        $case_list = array();

        foreach ($cases as $key => $case) {

            if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {

                if ( isset($case['case_type_id']) && $case['case_type_id'] != '' ) {
                    $case['case_type_id']           = intval($case['case_type_id']);
                }

                if ( isset($case['civil_disposition_id']) && $case['civil_disposition_id'] != '' ) {
                    $case['civil_disposition_id']   = intval($case['civil_disposition_id']);
                }
                
                if ( isset($case['identified_by_dob']) && $case['identified_by_dob'] != '' ) {
                    $case['identified_by_dob']      = (bool) $case['identified_by_dob'];
                }
                
                if ( isset( $case['identified_by_name'] ) && $case['identified_by_name'] != '' ) {
                    $case['identified_by_name']     = (bool) $case['identified_by_name'];
                }
                
                if ( isset($case['identified_by_ssn']) && $case['identified_by_ssn'] != '' ) {
                    $case['identified_by_ssn']      = (bool) $case['identified_by_ssn'];
                }
                
                if ( isset($case['identified_by_other']) && $case['identified_by_other'] != '' ) {
                    $case['identified_by_other']    = (bool) $case['identified_by_other'];
                }

            } else {

                if ( isset($case['case_addl_dispositions']) ) {

                    if ( $case['case_addl_dispositions'] ) {

                        foreach ($case['case_addl_dispositions'] as $key => $value) {

                            if ( isset($case['case_addl_dispositions'][$key]['addition_type_id']) && $case['case_addl_dispositions'][$key]['addition_type_id'] != '' ) {
                                $case['case_addl_dispositions'][$key]['addition_type_id']   = intval($value['addition_type_id']);
                            }
                            
                            if ( isset($case['case_addl_dispositions'][$key]['addition_action_id']) && $case['case_addl_dispositions'][$key]['addition_action_id'] != '' ) {
                                $case['case_addl_dispositions'][$key]['addition_action_id'] = intval($value['addition_action_id']);
                            }
                            
                            if ( isset($case['case_addl_dispositions'][$key]['jail_suspended']) && $case['case_addl_dispositions'][$key]['jail_suspended'] != '' ) {
                                $case['case_addl_dispositions'][$key]['jail_suspended']     = boolval($value['jail_suspended']);
                            }
                            
                            if ( isset($case['case_addl_dispositions'][$key]['prison_suspended']) && $case['case_addl_dispositions'][$key]['prison_suspended'] != '' ) {
                                $case['case_addl_dispositions'][$key]['prison_suspended']   = boolval($value['prison_suspended']);
                            }
                            
                        }   

                    }
                    
                }

                if ( isset($case['criminal_charges']) ) {

                    if ( $case['criminal_charges'] ) {

                        foreach ($case['criminal_charges'] as $key => $value) {
                            
                            if ( isset($case['criminal_charges'][$key]['charge_level_id']) && $case['criminal_charges'][$key]['charge_level_id'] != '' ) {
                                $case['criminal_charges'][$key]['charge_level_id'] = intval($value['charge_level_id']);
                            } else {
                                 $case['criminal_charges'][$key]['charge_level_id'] = 1;
                            }
                            
                            if ( isset($case['criminal_charges'][$key]['charge_disposition_id']) && $case['criminal_charges'][$key]['charge_disposition_id'] != '' ) {
                                $case['criminal_charges'][$key]['charge_disposition_id'] = intval($value['charge_disposition_id']);
                            } else {
                                $case['criminal_charges'][$key]['charge_disposition_id'] = 1;
                            }
                            

                            if ( isset($value['sentences']) ) {
                                if ( $value['sentences'] ) {
                                    foreach ($value['sentences'] as $skey => $sentence) {
                                        if ( isset($case['criminal_charges'][$key]['sentences'][$skey]['jail_suspended']) && $case['criminal_charges'][$key]['sentences'][$skey]['jail_suspended'] != '' ) {
                                            $case['criminal_charges'][$key]['sentences'][$skey]['jail_suspended'] = boolval($sentence['jail_suspended']);
                                        }
                                        
                                        if ( isset($case['criminal_charges'][$key]['sentences'][$skey]['prison_suspended']) && $case['criminal_charges'][$key]['sentences'][$skey]['prison_suspended'] != '' ) {
                                            $case['criminal_charges'][$key]['sentences'][$skey]['prison_suspended'] = boolval($sentence['prison_suspended']);
                                        }
                                        
                                        if ( isset($case['criminal_charges'][$key]['sentences'][$skey]['probation_type_id']) && $case['criminal_charges'][$key]['sentences'][$skey]['probation_type_id'] != '' ) {
                                            $case['criminal_charges'][$key]['sentences'][$skey]['probation_type_id'] = intval($sentence['probation_type_id']);
                                        } else {
                                            $case['criminal_charges'][$key]['sentences'][$skey]['probation_type_id'] = 1;
                                        }
                                        
                                    }    
                                }
                            }

                            if ( isset($value['addl_disposition']) ) {
                                if ( $value['addl_disposition'] ) {
                                    foreach ($value['addl_disposition'] as $skey => $addl_d) {
                                        if ( isset($case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_type_id']) && $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_type_id'] != '' ) {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_type_id'] = $addl_d['addition_type_id'] != 1 ? intval($addl_d['addition_type_id']) : '';
                                        } else {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_type_id'] = '';
                                        }
                                        
                                        if ( isset($case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_action_id']) && $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_action_id'] != '' ) {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_action_id'] = $addl_d['addition_action_id'] != 1 ? intval($addl_d['addition_action_id']) : '';
                                        } else {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['addition_action_id'] = '';
                                        }
                                        
                                        if ( isset($case['criminal_charges'][$key]['addl_disposition'][$skey]['jail_suspended']) && $case['criminal_charges'][$key]['addl_disposition'][$skey]['jail_suspended'] != '' ) {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['jail_suspended'] = boolval($addl_d['jail_suspended']);
                                        }
                                        
                                        if ( isset($case['criminal_charges'][$key]['addl_disposition'][$skey]['prison_suspended']) && $case['criminal_charges'][$key]['addl_disposition'][$skey]['prison_suspended'] != '' ) {
                                            $case['criminal_charges'][$key]['addl_disposition'][$skey]['prison_suspended'] = boolval($addl_d['prison_suspended']);
                                        }
                                        
                                    }    
                                }
                                
                            }

                        }    

                    }
                    
                }

                $case_data = array();

                // basic info
                $case_data["case_number"] = $case['case_number'];
                $case_data["file_date"] = $case["file_date"];
                $case_data["identified_by_name"] = isset($case["identified_by_name"]) ? (bool)$case["identified_by_name"] : (bool)false;
                $case_data["identified_by_dob"] = isset($case["identified_by_dob"]) ? (bool)$case["identified_by_dob"] : (bool)false;
                $case_data["identified_by_ssn"] = isset($case["identified_by_ssn"]) ? (bool)$case["identified_by_ssn"] : (bool)false;
                $case_data["name_on_file"] = $case["name_on_file"];
                $case_data["dob_on_file"] = isset($case["dob_on_file"]) ? $case["dob_on_file"] : '';
                $case_data["ssn_on_file"] = isset($case["ssn_on_file"]) ? $case["ssn_on_file"] : '';
                $case_data["dl_state"] = isset($case["dl_state"]) ? $case["dl_state"] : '';
                $case_data["dl_number"] = isset($case["dl_number"]) ? $case["dl_number"] : '';
                $case_data["street_address"] = isset($case["street_address"]) ? $case["street_address"] : '';
                $case_data["city"] = isset($case["city"]) ? $case["city"] : '';
                $case_data["state"] = isset($case["state"]) ? $case["state"] : '';
                $case_data["zip_code"] = isset($case["zip_code"]) ? $case["zip_code"] : '';
                $case_data["addl_information"] = isset($case["addl_information"]) ? $case["addl_information"] : '';
                $case_data["case_disposition_id"] = (int)$case["case_disposition_id"];
                $case_data["case_disposition_date"] = $case["case_disposition_date"];

                // case level sentence
                $case_data["case_sentence"]["jail_time"] = isset($case["case_sentence"]["jail_time"]) ? $case["case_sentence"]["jail_time"] : '';
                $case_data["case_sentence"]["jail_suspended"] = isset($case["case_sentence"]["jail_suspended"]) ? (bool)$case["case_sentence"]["jail_suspended"] : (bool) false;
                $case_data["case_sentence"]["jail_credit_time"] = isset($case["case_sentence"]["jail_credit_time"]) ? $case["case_sentence"]["jail_credit_time"] : '';

                $case_data["case_sentence"]["prison_time"] = isset($case["case_sentence"]["prison_time"]) ? $case["case_sentence"]["prison_time"] : '';
                $case_data["case_sentence"]["prison_suspended"] = isset($case["case_sentence"]["prison_suspended"]) ? (bool)$case["case_sentence"]["prison_suspended"] : (bool) false;
                $case_data["case_sentence"]["prison_credit_time"] = isset($case["case_sentence"]["prison_credit_time"]) ? $case["case_sentence"]["prison_credit_time"] : '';
                $case_data["case_sentence"]["probation_type_id"] = isset($case["case_sentence"]["probation_type_id"]) ? (int)$case["case_sentence"]["probation_type_id"] : 1;
                $case_data["case_sentence"]["probation_duration_time"] = isset($case["case_sentence"]["probation_duration_time"]) ? $case["case_sentence"]["probation_duration_time"] : '';
                $case_data["case_sentence"]["license_suspended_time"] = isset($case["case_sentence"]["license_suspended_time"]) ? $case["case_sentence"]["license_suspended_time"] : '';
                $case_data["case_sentence"]["community_service_time"] = isset($case["case_sentence"]["community_service_time"]) ? $case["case_sentence"]["community_service_time"] : '';
                $case_data["case_sentence"]["fines"] = isset($case["case_sentence"]["fines"]) ? $case["case_sentence"]["fines"] : '';
                $case_data["case_sentence"]["fees"] = isset($case["case_sentence"]["fees"]) ? $case["case_sentence"]["fees"] : '';
                $case_data["case_sentence"]["costs"] = isset($case["case_sentence"]["costs"]) ? $case["case_sentence"]["costs"] : '';
                $case_data["case_sentence"]["restitution"] = isset($case["case_sentence"]["restitution"]) ? $case["case_sentence"]["restitution"] : '';
                $case_data["case_sentence"]["classes_and_programs"] = isset($case["case_sentence"]["classes_and_programs"]) ? $case["case_sentence"]["classes_and_programs"] : '';
                $case_data["case_sentence"]["addl_information"] = isset($case["case_sentence"]["addl_information"]) ? $case["case_sentence"]["addl_information"] : '';

                if ( isset($case['case_addl_dispositions']) && count($case['case_addl_dispositions']) > 0 ) {
                    $case_data['case_addl_dispositions'] = $case['case_addl_dispositions'];
                }

                if ( isset($case['criminal_charges']) && count($case['criminal_charges']) > 1 ) {
                    $case_data['criminal_charges'] = $case['criminal_charges'];
                }

            }

            $case_list[] = $case_data;
        }
        

        if ( $search_type == 'CIV' || $search_type == 'CIV-L' ) {
            $params['civil_cases'] = $case_list;
        } else {
            $params['cases'] = $case_list;
        }

        $params['search_status'] = 'F';

        $detail = array( $params );

        $data = array(
            'search_updates' => $detail
        );
        
        $data = json_encode($data);

        $update = ab_update_searches_data($data);

        if ( ! isset($update->completed_updates) ) {
            $time           = time();
            $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
            $errors[$time]  = json_encode($update);
            update_option('search_update_error_logs', serialize($errors), 'no');
            redirect('/admin/search/' . $search_id .'?errorId='.$time);
            exit;
        }

        foreach ($update->completed_updates as $key => $value) {
            $sid = $value->search_id;
            $this->searches_model->update($sid, 'F');
        }

        redirect('/admin/searches/?submit_status=success&sid=' . $search_id );
        exit;
    }

    public function export() {
        $this->load->model('searches_model');
        $searches = $this->searches_model->get_searches('', ['search_status' => 'P']);

        $filteredArray = array_map( function($a) {
            $first_name     = $a->first_name;
            $middle_name    = $a->middle_name;
            $last_name      = $a->last_name;
            $search_id      = $a->search_ID;
            $data           = unserialize($a->orig_data);
            $dob            = isset($data->subject->date_of_birth) ? $data->subject->date_of_birth : '';
            $dob            = $dob != '' ? date('m-d-Y', strtotime($dob)) : '';
            $array          = [$first_name, $middle_name, $last_name, $search_id, $dob, ];
            $state          = $a->state;

            $array = [$first_name, $middle_name, $last_name, $search_id, $dob, $state];

            return $array;
            
        }, $searches );

        ob_start();
        $filename       = 'pending-searches_' . time() . '.csv';
        $fp             = fopen('php://output', 'w');
        $columns_header = array('first_name', 'middle_name', 'last_name', 'search_id', 'dob', 'state');
        
        fputcsv($fp, $columns_header);

        foreach ($filteredArray as $key => $data) {
            fputcsv($fp, $data);
        }

        fclose($fp);

        $output = ob_get_clean();

        $this->output
        ->set_header('Content-Disposition: attachment; filename=' . $filename)
        ->set_content_type('text/csv')
        ->set_output($output);

    }

    public function single_search_export($search_id = '') {
        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->helper('countries_helper');
        $this->load->model('searches_model');

        $table_data                     = $this->searches_model->get_detail($search_id);
        $result                         = unserialize($table_data->orig_data);
        $cases                          = $table_data->cases != '' ? unserialize($table_data->cases) : array();
        $counties                       = json_decode( get_option('table_Counties'), true ); 
        $states                         = us_states();
        $dispositions                   = convertToSortable(json_decode( get_option('table_Dispositions'), true ), 'description'); /*asort($dispositions);*/
        $charge_level                   = convertToSortable( json_decode( get_option('table_ChargeLevels'), true ), 'description' );/*asort($charge_level);*/
        $caseTypeId                     = json_decode( get_option('table_CivilCaseType'), true ); 
        $probationTypes                 = json_decode( get_option('table_ProbationTypes'), true ); 
        $additionTypes                  = json_decode( get_option('table_AdditionTypes'), true ); 
        $additionActionTypes            = json_decode( get_option('table_AdditionActionTypes'), true ); 
        $civilDisposition               = json_decode( get_option('table_CivilCaseDispositions'), true ); 
        $search_type                    = $result->search_type;
        $ssn                            = $result->subject->ssn;
       
       
        $client_notes                   = isset($result->client_notes) && $result->client_notes != '' ? nl2br($result->client_notes) : '-';
        $internal_notes                 = isset($result->internal_notes) && $result->internal_notes != '' ? nl2br($result->internal_notes) : '-';
        $mothers_maiden                 = isset($result->subject->mothers_maiden) ? $result->subject->mothers_maiden : '';
        $position_state                 = isset($result->subject->position_state) ? $result->subject->position_state : '';
        $position_county                = isset($result->subject->position_county) ? $result->subject->position_county : '';
        $dob                            = isset($result->subject->date_of_birth) ? $result->subject->date_of_birth : '';
        $dob                            = $dob != '' ? date('m-d-Y', strtotime($dob)) : '';
        $DL                             = isset($result->subject->drivers_license) ? $result->subject->drivers_license : '';
        $s_address1                     = isset($result->subject->address1) ? $result->subject->address1 : '';
        $s_city                         = isset($result->subject->city) ? $result->subject->city : '';
        $s_country                      = isset($result->subject->country) ? $result->subject->country : '';
        $s_state                        = isset($result->subject->state) ? $result->subject->state : '';
        $s_zip_code                     = isset($result->subject->zip_code) ? $result->subject->zip_code : '';

        $name_suffix                    = isset($result->subject->name_suffix) ? $result->subject->name_suffix : '';

        $first_name                     = $table_data->first_name;
        $ak1_first_name                 = isset($result->subject->aka_names[0]->first_name) ? $result->subject->aka_names[0]->first_name : '';
        $ak2_first_name                 = isset($result->subject->aka_names[1]->first_name) ? $result->subject->aka_names[1]->first_name : '';
        $ak3_first_name                 = isset($result->subject->aka_names[2]->first_name) ? $result->subject->aka_names[2]->first_name : '';

        
        $middle_name                    = isset($result->subject->middle_name) && $result->subject->middle_name != '' ? ' ' . $result->subject->middle_name : '';
        $ak1_middle_name                = isset($result->subject->aka_names[0]->middle_name) ? $result->subject->aka_names[0]->middle_name : '';
        $ak2_middle_name                = isset($result->subject->aka_names[1]->middle_name) ? $result->subject->aka_names[1]->middle_name : '';
        $ak3_middle_name                = isset($result->subject->aka_names[2]->middle_name) ? $result->subject->aka_names[2]->middle_name : '';

       
        $last_name                      = $table_data->last_name;
        $ak1_last_name                  = isset($result->subject->aka_names[0]->last_name) ? $result->subject->aka_names[0]->last_name : '';
        $ak2_last_name                  = isset($result->subject->aka_names[1]->last_name) ? $result->subject->aka_names[1]->last_name : '';
        $ak3_last_name                  = isset($result->subject->aka_names[2]->last_name) ? $result->subject->aka_names[2]->last_name : '';
        $cases_entered                  = '';

        if ( $cases ) {
            $cases = generate_cases($cases, $search_id, $search_type);
            ob_start();
            ?>
            <table border="0" cellpadding="3" cellspacing="0">
                <thead>
                    <tr class="section-title" style="background-color:#3F3F3F;color:#fff; font-size: 10px">
                        <th align="left" colspan="1">CASES ENTERED</th>
                    </tr>
                </thead>
                <tr>
                    <td>
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
                                            //$cval = dob_format($cval);
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
                    </td>
                </tr>
                <tr><td></td><td></td></tr>
            </table>
            
            <?php
            $cases_entered = ob_get_clean();
        }

        // create new PDF document
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator('AmazonScreening');
        $pdf->SetAuthor('PETER PUMA ENTERPRISES INC');
        $pdf->SetTitle('Single Search Report - ' . $search_id);
        $pdf->SetSubject($first_name . $middle_name . ' ' . $last_name);

        // set default header data
        $pdf->SetHeaderData(false, false, 'PETER PUMA ENTERPRISES INC', 'by AmazonScreening', [63, 63, 63], [63, 63, 63]);
        $pdf->setFooterData([63, 63, 63], [63, 63, 63]);

        // set header and footer fonts
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', 7]);

        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        $pdf->SetMargins(10, 20, 10, 20);

        $pdf->SetAutoPageBreak(true, 20);

        $pdf->setFontSubsetting(true);

        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 9, '', true);
        // ---------------------------------------------------------

        // add a page
        $pdf->AddPage();

        // writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
        // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

        // create some HTML content
        $html = '
        <style>        
        .label-information {
            font-weight: bold;
            margin-right: 8px;
        }
        .label-subject {
            margin: 0;
            font-weight: bold;
            width: 50px;
            color: #3F3F3F;
            font-size: 10px;
        }
        span {
            font-size: 10px;
        }
        h1 {
            text-align: center;
        }
        </style>

        <table border="0" cellpadding="4" cellspacing="0">
            <thead>
                <tr class="section-title" style="background-color:#3F3F3F;color:#fff; font-size: 10px">
                    <th align="left" colspan="2">SEARCH INFORMATION</th>
                </tr>
            </thead>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Search ID</label></td>
                <td width="168" align="left" style="border-bottom:0.1px solid #3F3F3F;">: ' . $search_id . '</td>
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Search Type</label></td>
                <td width="168" align="left" style="border-bottom:0.1px solid #3F3F3F;">: ' . search_type($search_type) . '</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">State</label></td>
                <td width="168" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$result->subject->state.'</td>
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">County</label></td>
                <td width="168" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$counties[$result->search_county_id]['county_name'].'</td>
            </tr>
            <tr><td></td><td></td><td></td><td></td></tr>
        </table>
        <table border="0" cellpadding="3" cellspacing="0">
            <thead>
                <tr class="section-title" style="background-color:#3F3F3F;color:#fff; font-size: 10px">
                    <th  align="left" colspan="6">SUBJECT INFORMATION</th>
                </tr>
            </thead>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Last</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$last_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">First</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$first_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Middle</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$middle_name.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 1</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak1_last_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 1</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak1_first_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 1</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak1_middle_name.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 2</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak2_last_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 2</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak2_first_name.'</td>
                <td width="50" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">AKA 2</label></td>
                <td width="128.5" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$ak2_middle_name.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="50" align="left"><label class="label-information">AKA 3</label></td>
                <td width="128.5" align="left">: '.$ak3_last_name.'</td>
                <td width="50" align="left"><label class="label-information">AKA 3</label></td>
                <td width="128.5" align="left">: '.$ak3_first_name.'</td>
                <td width="50" align="left"><label class="label-information">AKA 3</label></td>
                <td width="128.5" align="left">: '.$ak3_middle_name.'</td>
            </tr>
            <tr style="background-color:#cccccc;font-size: 10px; color: #3F3F3F">
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F;">
                <td width="35" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Suffix</label></td>
                <td width="95" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$name_suffix.'</td>
                <td width="50" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Address</label></td>
                <td width="128.5" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$s_address1.'</td>
                <td width="128" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Mother\'s Maiden</label></td>
                <td width="100" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$mothers_maiden.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="35" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">DOB</label></td>
                <td width="95" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$dob.'</td>
                <td width="50" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">City</label></td>
                <td width="128.5" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$s_city.'</td>
                <td width="128" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Position Location</label></td>
                <td width="100" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$position_state.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="35" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">SSN</label></td>
                <td width="95" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$ssn.'</td>
                <td width="50" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Country</label></td>
                <td width="128.5" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$s_country.'</td>
                <td width="128" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Position Location County</label></td>
                <td width="100" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$position_county.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="35" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">DL#</label></td>
                <td width="95" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$DL.'</td>
                <td width="50" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">State</label></td>
                <td width="128.5" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$s_state.'</td>
                <td width="128" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
                <td width="100" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="35" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
                <td width="95" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
                <td width="50" align="left"  style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Zip</label></td>
                <td width="128.5" align="left"  style="border-bottom:0.1px solid #3F3F3F;">: '.$s_zip_code.'</td>
                <td width="128" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
                <td width="100" align="left"  style="border-bottom:0.1px solid #3F3F3F;"></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </table>
        <table border="0" cellpadding="3" cellspacing="0">
            <thead>
                <tr class="section-title" style="background-color:#3F3F3F;color:#fff; font-size: 10px">
                    <th align="left" colspan="2">NOTES</th>
                </tr>
            </thead>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Client notes</label></td>
                <td width="436" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$client_notes.'</td>
            </tr>
            <tr style="font-size: 10px; color: #3F3F3F">
                <td width="100" align="left" style="border-bottom:0.1px solid #3F3F3F;"><label class="label-information">Internal notes</label></td>
                <td width="436" align="left" style="border-bottom:0.1px solid #3F3F3F;">: '.$internal_notes.'</td>
            </tr>
            <tr><td></td><td></td></tr>
        </table>
        ' . $cases_entered;

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // reset pointer to the last page
        $pdf->lastPage();

        // ---------------------------------------------------------

        //Close and output PDF document
        $pdf->Output('Single Search Report - ' . $search_id . ' ' . $first_name . $middle_name . ' ' . $last_name . '.pdf', 'I');
    }

}