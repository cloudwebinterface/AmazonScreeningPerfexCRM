<?php defined('BASEPATH') or exit('No direct script access allowed');

class Codes extends AdminController
{
	public function index() {
		if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }
        $data['title'] = 'Code Tables - AmazonScreening';

        $this->load->helper('api_helper');
        $data['data'] = ab_table_names();
        $this->load->view( 'admin/searches/codes', $data );
	}

	public function code_detail($code_name = '') {
		if (!has_permission('customers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('customers', '', 'create')) {
                access_denied('customers');
            }
        }

        $this->load->helper('api_helper');
        $data['title'] = 'Code Detail - AmazonScreening';
        $table_name = ab_table_names($code_name);
        $data['code_name'] = $table_name;
        $data['data'] = ab_get_table($table_name);
        $this->load->view( 'admin/searches/code_detail', $data );
	}
}