<?php defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends AdminController {

    public function __construct()
    {
        parent::__construct();
        if ( ! is_admin() ) {
            access_denied('logs');
        }
    }

	public function index() 
    {
    	$data['title'] = 'Logs - AmazonScreening';
    	$data['logs'] = get_option('search_update_error_logs') != '' ? array_reverse(unserialize(get_option('search_update_error_logs')), true) : array();
    	$data['success_logs'] = get_option('search_update_success_logs') != '' ? array_reverse(unserialize(get_option('search_update_success_logs')), true) : array();
    	$this->load->view( 'admin/searches/logs', $data );
    }

    public function clear($log = '') {
    	$data['title'] = 'Clear Logs - AmazonScreening';

    	switch ($log) {
    		case 'error':
    			update_option( 'search_update_error_logs', '', 'no' );
    		break;
    		case 'success':
    			update_option( 'search_update_success_logs', '', 'no' );
    		break;
    	}

    	redirect( '/admin/logs?tab=' . $log );
        exit;
    }
}