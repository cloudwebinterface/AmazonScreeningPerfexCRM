<?php defined('BASEPATH') or exit('No direct script access allowed');

class Summary extends AdminController 
{
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
        
        $data['title'] 		= 'Case summary - Amazonscreening';
        $data['current'] 	= 'summary';
        $this->load->view( 'admin/searches/summary', $data );
    }

    public function get_data() {
        $offset = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $order = array();

        $rows = $this->summary_model->get('', $limit, $offset);
        $total_rows = $this->summary_model->total_rows();

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
        	$search_id 			= $search->search_ID;
			$search_data 		= unserialize($search->orig_data);
			$checkbox 			= '<input type="checkbox" name="selected_list[]" value="'.$search_id.'"/>';
			$search_id_column 	= '<a href="/admin/search/'.$search_id.'">'.$search_id.'</a>';
			$cases 				= $search->cases;
			$cases_entered 		= $cases != '' ? '<i class="fa fa-check text-success"></i>' : '';
    		$subject 			= $search_data->subject;
			$name 				= $search->first_name . ' ' . $search->middle_name . ' ' . $search->last_name;
			$ssn 				= isset($subject->ssn) ? $subject->ssn : '';
			$dob 				= isset($subject->date_of_birth) ? $subject->date_of_birth : '';
			$search_type 		= strtoupper(search_type($search_data->search_type)) . ' ('.$search_data->search_type.')';
			$state 				= (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');

			$records[] = array(
    			'NF' => $checkbox, 
    			'row_id' => $search->list_id,
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
            'recordsTotal' => $total_rows,
            'recordsFiltered' => $total_rows,
            'data' => $records
        );

        echo json_encode($data);
    	exit;

    }

    public function bulk_submit() {
        if ( ! isset($_POST['selected_list']) ) {
            show_404();
            exit;
        }

        if ( empty($_POST['selected_list']) ) {
            redirect('/admin/summary', 'refresh');
            exit;
        }

        $list = $_POST['selected_list'];

        $conditions = array(
            'where_in'=> array(
                'search_ID' => $list
            )
        );

        $rows = $this->summary_model->get($conditions);
        if ( !$rows ) { redirect('/admin/summary'); exit; }

        $searches = array();
        foreach ($rows as $key => $search) {
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
                $this->searches_model->update($sid, 'F');
            }

            $success        = get_option('search_update_success_logs') ? unserialize(get_option('search_update_success_logs')) : array();
            $success[$t]    = json_encode($update);
            update_option('search_update_success_logs', serialize($success), 'no'); 
        }
        
        redirect('/admin/summary/?submit_status=success&t=' . $t, 'refresh' );
        exit;
    }
}