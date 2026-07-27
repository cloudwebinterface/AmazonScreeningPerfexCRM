<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cases extends AdminController
{
	public function index() {
		if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $data['title'] = 'Cases - AmazonScreening';
        $this->load->model('searches_model');
        $data['data'] = json_encode($this->searches_model->get('', array('search_status' => 'P')));
        $this->load->view( 'admin/searches/searches', $data );
    }

    public function clone($search_id = '') {
    	if ( $search_id == '' ) {
    		show_404();
    		exit;
    	}

    	$this->load->helper('search_helper');
    	$this->load->helper('search_meta_helper');
    	$this->load->model('searches_model');

    	// check if sid is exist
    	$check = search_id_exists($search_id);

    	if ( !$check ) { show_404(); exit; }

    	$table_data	= $this->searches_model->get_detail($search_id);
        $result		= unserialize($table_data->orig_data);
        $where 		= array(
            'first_name'    => $table_data->first_name,
            'last_name'     => $table_data->last_name
        );

        $middle_name	= isset($table_data->middle_name) && $table_data->middle_name != '' ? $table_data->middle_name : '';

        if ( $middle_name != '' ) {
            $where['middle_name'] = $middle_name;
        }

        $duplicates	= $this->searches_model->get_searches( '', $where );
        $search_ids = array();
        if ( count($duplicates) > 1 ) {

            foreach ($duplicates as $idx => $s) {
                $od = unserialize($s->orig_data);
                if ( $result->subject->ssn != $od->subject->ssn ) { continue; }
                $search_ids[] = $s->search_ID;
            }

        }

    	if ( count($search_ids) <= 1 ) {
            show_404();
    		exit; 
        }

        // get cases
        $cases = $table_data->cases;

        $this->searches_model->duplicate_cases($cases, $search_ids);

        foreach ($search_ids as $sid) {
        	update_search_meta( $sid, 'already_cloned', 'yes' );
        }

        redirect('/admin/search/'. $search_id .'?duplicate=' . base64_encode(json_encode($search_ids)), 'refresh');
        exit;

    }
}