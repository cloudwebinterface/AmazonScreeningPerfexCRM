<?php defined('BASEPATH') or exit('No direct script access allowed');

class History extends AdminController {

	public function index() 
    {
    	$data['title'] = 'Search history - AmazonScreening';
    	$this->load->view( 'admin/searches/history', $data );
    }

    public function history_old() {
        $data['title'] = 'Search history (Old table) - AmazonScreening';
        $this->load->view( 'admin/searches/history_old', $data );
    }

    public function get_data() {

        $this->load->model('history_model');
        $this->load->helper('search_helper');

        $offset = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit  = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw   = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $where  = '';
        $order              = array();
        $order['orderby']   = 'list_id';
        $order['order']     = 'ASC';
        $counties           = json_decode( get_option('table_Counties'), true ); 
        $cases_only         = '';

        if ( isset($_POST['haveCases']) && $_POST['haveCases'] == 'yes' ) {
            $cases_only = true;
        }

        if ( isset($_POST['cSearch']) ) {
            $cSearch = $_POST['cSearch'];
            if ( $cSearch['search_by'] == 'search_id' ) {
                $where = array();
                $where['search_id'] = $cSearch['search_value'];
            }

            if ( $cSearch['search_by'] == 'name' ) {
                $where = array();

                if ( isset($cSearch['first_name']) && $cSearch['first_name'] != '' ) {
                    $first_name         = $cSearch['first_name'];
                    $fname_has_wildcard = strpos($first_name, '%');
                    $fname_total_char   = strlen($first_name);
                    if ( $fname_has_wildcard !== false && ($fname_has_wildcard+1) == $fname_total_char ) {
                        $first_name = substr($first_name, 0, -1);
                        $where['first_name'][] = [
                            'operator' => 'wildcard',
                            'value' => $first_name
                        ];
                    } else {
                        $where['first_name'] = $cSearch['first_name'];
                    }
                    
                }

                if ( isset($cSearch['middle_name']) && $cSearch['middle_name'] != '' ) {
                    $where['middle_name'] = $cSearch['middle_name'];
                }

                if ( isset($cSearch['last_name']) && $cSearch['last_name'] != '' ) {
                    $last_name          = $cSearch['last_name'];
                    $lname_has_wildcard = strpos($last_name, '%');
                    $lname_total_char   = strlen($last_name);
                    if ( $lname_has_wildcard !== false && ($lname_has_wildcard+1) == $lname_total_char ) {
                        $last_name = substr($last_name, 0, -1);
                        $where['last_name'][] = [
                            'operator' => 'wildcard',
                            'value' => $last_name
                        ];
                    } else {
                        $where['last_name'] = $cSearch['last_name'];
                    }
                }
            }

            if ( $cSearch['search_by'] == 'sent_date' ) {

                if ( isset( $cSearch['sent_date_from'] ) && $cSearch['sent_date_from'] != '' ) {
                    $from = strtotime($cSearch['sent_date_from']);

                    if ( $where == '' ) {
                        $where = array();
                    }
                    
                    $where['sent_date'][] = array(
                        'operator'  => '>=',
                        'value'     => $from
                    );
                }

                if ( isset( $cSearch['sent_date_to'] ) && $cSearch['sent_date_to'] != '' ) {
                    $to = strtotime($cSearch['sent_date_to']);
                    $where['sent_date'][] = array(
                        'operator'  => '<=',
                        'value'     => $to
                    );
                }

            }
        }

        $rows       = $this->history_model->get($where, $limit, $offset, $order, $cases_only);
        $total_rows = $this->history_model->total_rows('', $cases_only);
        if ( ! $rows ) {
            $r = array(
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array(),
                'rows' => $rows
            );

            echo json_encode($r);
            exit;
        }

        $records = array();

        foreach ($rows as $row_id => $search) {
            $search_id          = $search->search_ID;
            $search_data        = unserialize($search->orig_data);
            $search_id_column   = '<a href="/admin/history/detail/'.$search_id.'">'.$search_id.'</a>';
            $cases              = $search->cases;
            $cases_entered      = $cases != '' ? '<i class="fa fa-check text-success"></i>' : '';
            $subject            = $search_data->subject;
            $name               = $search->first_name . ' ' . $search->middle_name . ' ' . $search->last_name;
            $ssn                = isset($subject->ssn) ? $subject->ssn : '';
            $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
            $search_type        = strtoupper(search_type($search_data->search_type)) . ' ('.$search_data->search_type.')';
            $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
            $county             = isset($search_data->search_county_id) ? $counties[$search_data->search_county_id]['county_name'] : '';
            $search_status      = $search->search_status;

            switch ($search_status) {
                case 'C':
                    $search_status = 'Canceled';
                break;

                case 'F':
                    $search_status = 'Found';
                break;

                case 'N':
                    $search_status = 'Not Found';
                break;
                
                default:
                    $search_status = $search->search_status;
                break;
            }


            $records[] = array(
                'row_id' => $search->list_id,
                'search_id' => $search_id_column, 
                'cases_entered' => $cases_entered, 
                'name' => $name, 
                'ssn' => $ssn, 
                'dob' => $dob, 
                'search_type' => $search_type, 
                'state' => $state,
                'county' => $county,
                'load_date' => date('F j, Y H:i', $search->added_date),
                'sent_date' => ($search->sent_date != '' ? date('F j, Y H:i', $search->sent_date) : '-'),
                'status' => $search_status
            );
        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_rows,
            'recordsFiltered' => $total_rows,
            'data' => $records
        );

        echo json_encode($data);
        exit;

    }

    public function get_old_data() 
    {
        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');

        $offset = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $order = array();

        if ( isset($_POST['order'][0]['dir']) && isset($_POST['order'][0]['column']) ) {
            
            $columns = array(
                1 => 'list_id',
                2 => 'search_ID'
            );
            $column = $_POST['order'][0]['column'];

            $order['orderby'] = $columns[$column];
            $order['order'] = strtoupper($_POST['order'][0]['dir']);
        }

        $this->load->model('history_model'); 

        if ( isset($_POST['cSearch']) ) {
            $cSearch = $_POST['cSearch'];

            if ( $cSearch['search_by'] == 'search_id' ) {
            	$search_by_id = array('search_ID' => $cSearch['search_value']);
            	$get_data = $this->history_model->get_old( $search_by_id, $limit, $offset );
                $total = $this->history_model->total_old_rows($search_by_id);

	            if ( !$get_data ) {
	                $r = array(
	                    'draw' => $draw,
	                    'recordsTotal' => 0,
	                    'recordsFiltered' => 0,
	                    'data' => array()
	                );

	                echo json_encode($r);
	                exit;
	            }

	            $record = array();

	            foreach ($get_data as $key => $value) {
	                $search_id          = $value->search_ID;
	                $checkbox           = '';
	                $search_id_column   = '<a href="/admin/history/detail_old/'.$search_id.'">'.$search_id.'</a>';
	                $cases              = get_search_meta( $search_id, 'cases' );
	                $cases_entered      = $cases && (is_array($cases)||is_object($cases)) ? '<i class="fa fa-check text-success"></i>' : '';
	                $subject            = get_search_meta( $search_id, 'subject');
	                $name               = (isset($subject->first_name) ? $subject->first_name : '') . ' ' . (isset($subject->middle_name) ? $subject->middle_name : '') . ' ' . (isset($subject->last_name) ? $subject->last_name : '');
	                $ssn                = isset($subject->ssn) ? $subject->ssn : '';
	                $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
	                $search_type        = strtoupper(search_type(get_search_meta( $search_id, 'search_type' ))) . ' ('.get_search_meta( $search_id, 'search_type' ).')';
	                $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
	                $action             = '<a href="'.admin_url('search/'.$search_id).'" data-id="'.$search_id.'">Update</a>';

	                $record[] = array(
	                    //'NF' => $checkbox, 
	                    'row_id' => $value->list_id,
	                    'search_id' => $search_id_column, 
	                    'cases_entered' => $cases_entered, 
	                    'name' => $name, 
	                    'ssn' => $ssn, 
	                    'dob' => $dob, 
	                    'search_type' => $search_type, 
	                    'state' => $state
	                );
	            }

	            $data = array(
	                'draw' => $draw,
	                'recordsTotal' => $total,
	                'recordsFiltered' => $total,
	                'data' => $record,
	                'search' => $search_by_id,
	                'get_data' => $get_data
	            );

	            $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
                
            } else {

            	$get_data = $this->history_model->search( $cSearch, $limit, $offset );
            	$total = count($get_data);
                $record = array();

	            foreach ($get_data as $key => $value) {
	                $search_id          = $value['search_ID'];
	                $checkbox           = '';
	                $search_id_column   = '<a href="/admin/history/detail_old/'.$search_id.'">'.$search_id.'</a>';
	                $cases              = get_search_meta( $search_id, 'cases' );
	                $cases_entered      = $cases && (is_array($cases)||is_object($cases)) ? '<i class="fa fa-check text-success"></i>' : '';
	                $subject            = get_search_meta( $search_id, 'subject');
	                $name               = (isset($subject->first_name) ? $subject->first_name : '') . ' ' . (isset($subject->middle_name) ? $subject->middle_name : '') . ' ' . (isset($subject->last_name) ? $subject->last_name : '');
	                $ssn                = isset($subject->ssn) ? $subject->ssn : '';
	                $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
	                $search_type        = strtoupper(search_type(get_search_meta( $search_id, 'search_type' ))) . ' ('.get_search_meta( $search_id, 'search_type' ).')';
	                $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
	                $action             = '<a href="'.admin_url('search/'.$search_id).'" data-id="'.$search_id.'">Update</a>';

	                $record[] = array(
	                    //'NF' => $checkbox, 
	                    'row_id' => $value['meta_id'],
	                    'search_id' => $search_id_column, 
	                    'cases_entered' => $cases_entered, 
	                    'name' => $name, 
	                    'ssn' => $ssn, 
	                    'dob' => $dob, 
	                    'search_type' => $search_type, 
	                    'state' => $state
	                );
	            }

	            $data = array(
	                'draw' => $draw,
	                'recordsTotal' => $total,
	                'recordsFiltered' => $total,
	                'data' => $record,
	                'get_data' => $get_data
	            );

	            $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
            }
            

        } else {

        	if ( isset($_POST['haveCases']) && $_POST['haveCases'] == 'yes' ) {
        		$get_data = $this->history_model->get_cases( $limit, $offset );
        		$total = $this->history_model->total_case_rows();
        		if ( !$get_data ) {
	                $r = array(
	                    'draw' => $draw,
	                    'recordsTotal' => 0,
	                    'recordsFiltered' => 0,
	                    'data' => array(),
	                    'get_data' => $get_data
	                );

	                echo json_encode($r);
	                exit;
	            }

		        $record = array();

	            foreach ($get_data as $key => $value) {
	                $search_id          = $value['search_ID'];
	                $checkbox           = '';
	                $search_id_column   = '<a href="/admin/history/detail_old/'.$search_id.'">'.$search_id.'</a>';
	                $cases              = unserialize($value['meta_value']);
	                $cases_entered      = $cases && (is_array($cases)||is_object($cases)) ? '<i class="fa fa-check text-success"></i>' : '';
	                $subject            = get_search_meta( $search_id, 'subject');
	                $name               = (isset($subject->first_name) ? $subject->first_name : '') . ' ' . (isset($subject->middle_name) ? $subject->middle_name : '') . ' ' . (isset($subject->last_name) ? $subject->last_name : '');
	                $ssn                = isset($subject->ssn) ? $subject->ssn : '';
	                $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
	                $search_type        = strtoupper(search_type(get_search_meta( $search_id, 'search_type' ))) . ' ('.get_search_meta( $search_id, 'search_type' ).')';
	                $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');

	                $record[] = array(
	                    //'NF' => $checkbox, 
	                    'row_id' => $value['meta_id'],
	                    'search_id' => $search_id_column, 
	                    'cases_entered' => $cases_entered, 
	                    'name' => $name, 
	                    'ssn' => $ssn, 
	                    'dob' => $dob, 
	                    'search_type' => $search_type, 
	                    'state' => $state
	                );
	            }

	            $data = array(
	                'draw' => $draw,
	                'recordsTotal' => $total,
	                'recordsFiltered' => $total,
	                'data' => $record
	            );

	            echo json_encode( $data );
	            exit;

        	} else {
        		$get_data = $this->history_model->get_old( '', $limit, $offset );
            	$total = $this->history_model->total_old_rows();
        	}
            

            if ( !$get_data ) {
                $r = array(
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => array()
                );

                echo json_encode($r);
                exit;
            }

            $record = array();

            foreach ($get_data as $key => $value) {
                $search_id          = $value->search_ID;
                $checkbox           = '';
                $search_id_column   = '<a href="/admin/history/detail_old/'.$search_id.'">'.$search_id.'</a>';
                $cases              = get_search_meta( $search_id, 'cases' );
                $cases_entered      = $cases && (is_array($cases)||is_object($cases)) ? '<i class="fa fa-check text-success"></i>' : '';
                $subject            = get_search_meta( $search_id, 'subject');
                $name               = (isset($subject->first_name) ? $subject->first_name : '') . ' ' . (isset($subject->middle_name) ? $subject->middle_name : '') . ' ' . (isset($subject->last_name) ? $subject->last_name : '');
                $ssn                = isset($subject->ssn) ? $subject->ssn : '';
                $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
                $search_type        = strtoupper(search_type(get_search_meta( $search_id, 'search_type' ))) . ' ('.get_search_meta( $search_id, 'search_type' ).')';
                $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
                $action             = '<a href="'.admin_url('search/'.$search_id).'" data-id="'.$search_id.'">Update</a>';

                $record[] = array(
                    //'NF' => $checkbox, 
                    'row_id' => $value->list_id,
                    'search_id' => $search_id_column, 
                    'cases_entered' => $cases_entered, 
                    'name' => $name, 
                    'ssn' => $ssn, 
                    'dob' => $dob, 
                    'search_type' => $search_type, 
                    'state' => $state
                );
            }

            $data = array(
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $record
            );

            $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );

        }
        
    }

    public function detail($id = '') {
        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->helper('countries_helper');
        $this->load->model('searches_model');

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

        $this->load->view( 'admin/searches/history_detail', $data );
    }

    public function detail_old($id = '') {
        if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->helper('countries_helper');
        $this->load->model('history_model');

        $result                         = get_search_meta($id, 'search_data');
        $data['data']                   = $result;
        $data['search_id']              = $id;
        $data['search_type']            = $result->search_type;
        $cases                          = get_search_meta($id, 'cases')? get_search_meta($id, 'cases') : array();
        $data['cases']                  = $cases;
        $data['edit_case']              = isset($_GET['cid']) && $_GET['cid'] != '' && isset($cases[$_GET['cid']]) ? $cases[$_GET['cid']] : false;             
        
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
        $this->load->view( 'admin/searches/history_detail_old', $data );

    }



}