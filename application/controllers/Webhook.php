<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
    }

	public function index() {
		show_404();
        exit;
	}

    public function new() {

        $this->load->helper('settings_helper');
        $this->load->helper('api_helper');
        $this->load->model('searches_model');

        if ( isset( $_POST ) ) {

            $webhooks = file_get_contents('php://input');

            if ( ! $webhooks ) { exit; }

            update_option('received_webhooks_new', $webhooks, 'no');

        	$search_ids = json_decode( $webhooks, true );
        	$load_time = get_option('last_load_key');
        	$new_searches = get_option('new_searches') != '' ? unserialize(get_option('new_searches')) : array();

        	if ( ! isset($search_ids['search_ids']) ) { exit; }

        	foreach ($search_ids['search_ids'] as $key => $search_id) {

        		$search 		= ab_reload_search_data($search_id);

        		if ( ! $search ) { continue; }

        		$middle_name    = isset($search->subject->middle_name) ? $search->subject->middle_name : '';
                $last_name      = isset($search->subject->last_name) ? $search->subject->last_name : '';
                $state          = isset($search->subject->state) ? $search->subject->state : '';
                $data = array(
                    'search_ID' => $search->search_id,
                    'search_status' => $search->search_status,
                    'first_name' => $search->subject->first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'state' => $state,
                    'orig_data' => serialize($search),
                    'added_date' => time()
                );

                $this->searches_model->add($data);

        		if ( in_array( $search_id, $new_searches ) ) { continue; }
        		array_push( $new_searches, $search_id );

        	}

        	update_option('new_searches', serialize($new_searches), 'no');

        }

        exit;
    }

    public function changed() {

    	$this->load->helper('settings_helper');
        $this->load->helper('api_helper');
        $this->load->model('searches_model');

        if ( isset( $_POST ) ) {

            $webhooks = file_get_contents('php://input');

            if ( ! $webhooks ) { exit; }

            update_option('received_webhooks_modified', $webhooks, 'no');

        	$search_ids = json_decode( $webhooks, true );

        	if ( ! isset($search_ids['search_ids']) ) { exit; }

        	foreach ($search_ids['search_ids'] as $key => $search_id) {

        		$search 		= ab_reload_search_data($search_id);

        		if ( ! $search ) { continue; }

        		$middle_name    = isset($search->subject->middle_name) ? $search->subject->middle_name : '';
                $last_name      = isset($search->subject->last_name) ? $search->subject->last_name : '';
                $state          = isset($search->subject->state) ? $search->subject->state : '';
                $data = array(
                    'search_ID' => $search->search_id,
                    'search_status' => $search->search_status,
                    'first_name' => $search->subject->first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'state' => $state,
                    'orig_data' => serialize($search),
                    'added_date' => time()
                );

                $this->searches_model->add($data);

        	}

        }

        exit;

    }

    public function canceled() {

        $this->load->helper('settings_helper');
        $this->load->helper('api_helper');
        $this->load->model('searches_model');

        if ( isset( $_POST ) ) {

            $webhooks = file_get_contents('php://input');

            if ( ! $webhooks ) { exit; }

            update_option('received_webhooks_canceled', $webhooks, 'no');

        	$search_ids = json_decode( $webhooks, true );

        	if ( ! isset($search_ids['search_ids']) ) { exit; }

        	foreach ($search_ids['search_ids'] as $key => $search_id) {

        		$this->searches_model->update($search_id, 'C');

        	}

        }

        exit;

    }

}