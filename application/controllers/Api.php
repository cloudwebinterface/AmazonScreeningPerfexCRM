<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
    }

	public function index() {
		show_404();
    	exit;
	}

    public function cek() {
        // load api config
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');

        $searches = unserialize(get_option('unprocessed_searches'));

        echo '<pre>';
        print_r($searches);
        echo '</pre>';
    }

    public function autofetchdata() {
    	
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');

        $token          = generate_api_token();
        $load_data      = ab_fetch_data_no_cache($token, 100, '' );
        $decoded_data   = json_decode( $load_data );
        $searches 		= isset($decoded_data->searches) ? $decoded_data->searches : array();

        update_option('unprocessed_searches', serialize($searches), 'no' );

        if ( isset($decoded_data->next) ) {
        	$page 		= 1;
        	$has_data 	= 1;
        	$next_id 	= $decoded_data->next;

        	while (0 != $has_data) {

        	    $load_next_data 	= ab_fetch_data_no_cache($token, 100, $next_id );
        	    $next_decoded_data	= json_decode( $load_next_data );
        	    $old_searches   	= unserialize(get_option('unprocessed_searches'));
	            $new_searches   	= $next_decoded_data->searches;
	            $searches       	= array_merge($old_searches, $new_searches);
	            $next_id           	= isset($next_decoded_data->next) ? $next_decoded_data->next : '';
	            $page 				= $page+1;

	            update_option('unprocessed_searches', serialize($searches), 'no' ); 

	            if ( $next_id == '' ) {
	            	$has_data = 0;
	            }
        	}

        }

        $cached_data    = get_option('unprocessed_searches');
        $searches       = unserialize($cached_data);

        $this->load->model('searches_model');
        $this->searches_model->reset_search_status();

        if ( !$searches ) { 
            return; 
        }

        foreach ($searches as $key => $search) {

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
                'meta' => serialize(array('new' => 1)),
                'added_date' => time()
            );

            $this->searches_model->add($data);

        }

    }

    public function load_data() {

    	if ( ! isset($_SERVER['HTTP_ORIGIN']) ) {
    		show_404();
    		exit;
    	}

    	header('Access-Control-Allow-Origin: '.site_url());
    	header('Access-Control-Allow-Methods: POST');

        // load api config
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');

        $token = generate_api_token();

        $next = isset($_POST['next']) ? $_POST['next'] : '';

        $data = ab_fetch_data_no_cache($token, 50, $next );

        $decoded_data = @json_decode( $data );
        
        if ( ! $decoded_data ) { echo json_encode(array('status' => 'error', 'data' => $data)); exit; }
        
        $decoded_data 	= json_decode( $data );
        $searches 		= isset($decoded_data->searches) ? $decoded_data->searches : array();
        $next 			= isset($decoded_data->next) ? $decoded_data->next: '';
        $load_time 		= $_POST['load_key'];

        if ( $searches ) {
            $this->load->model('searches_model');
            $ids = array();
            foreach ($searches as $key => $search) {
                $this->searches_model->add($search, $load_time);
                $ids[] = $search->search_id;
            }

            update_option('search_next', $next, false);
            update_option('last_load_key', $load_time, false);
            echo json_encode(array('status' => 'success', 'search_ids' => $ids, 'next' => $next));
            exit;
        } else {
        	update_option('last_load_key', $load_time, false);
        	echo json_encode(array('status' => 'error', 'message' => 'no searches found', 'data' => $decoded_data));
            exit;
        }
    }

    public function load_fresh_data() {
        // load api config
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');

        if ( ! is_admin() ) {
            show_404();
            exit;
        }

        $data['title'] = 'Reload data - Amazon screening';

        $token          = generate_api_token();

        echo '<pre>';
        print_r($token);
        echo '</pre>';
        exit;

        $next_id        = isset($_GET['next']) ? $_GET['next'] : '';
        $response       = ab_fetch_data_no_cache($token, 100, $next_id );
        $decoded_data   = json_decode( $response );

        if ( !isset($_GET['next']) ) {
            echo '<h2 style="text-align: center;margin-top: 45px;">Please wait, we are downloading fresh data</h2>';
        } elseif ( isset($_GET['next']) && $_GET['next'] != 'done' ) {
            echo '<h2 style="text-align: center;margin-top: 45px;">Please wait, we are downloading fresh data</h2>';
        } else {
            echo '<h2 style="text-align: center;margin-top: 45px;">Downloaded fresh data</h2>';
        }

        echo '<div style="text-align:center;margin-bottom:30px">the process may take a few minutes depending on the amount of data being downloaded</div>';

        if ( !isset($_GET['next']) || (isset($_GET['next']) && $_GET['next'] != 'done') ) {
            echo '<img src="'.site_url('assets/images/ajax-loader.gif').'" alt="loading data" style="margin: 0 auto; display: block;">';
        }
        
        if ( ! isset($_GET['next']) ) {

            $searches   = array();
            $time       = time();
            $searches   = $decoded_data->searches;
            $next       = isset($decoded_data->next) ? $decoded_data->next : '';
            update_option('unprocessed_searches', serialize($searches), 'no' );
            update_option('last_load_key', $time, 'no');

            // clear search_status from dtabase
            $this->load->model('searches_model');
            $this->searches_model->reset_search_status();

            if ( $next != '' ) {
                echo '<h3 style="text-align: center">downloading page 1</h3>';
                echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next='.$next.'&page=1"; }, 500);</script>';
                exit;
            } else {
                echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next=done"; }, 500);</script>';
                exit;
            }

        } elseif ( isset($_GET['next']) && $_GET['next'] != 'done' ) {

            $old_searches   = unserialize(get_option('unprocessed_searches'));
            $new_searches   = $decoded_data->searches;
            $searches       = array_merge($old_searches, $new_searches);
            $next           = isset($decoded_data->next) ? $decoded_data->next : '';
            update_option('unprocessed_searches', serialize($searches), 'no' );  

            if ( $next != '' ) {
                $page = intval($_GET['page'])+1;
                echo '<h3 style="text-align: center">downloading page '.$page.'</h3>';
                echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next='.$next.'&page='.$page.'"; }, 500);</script>';
                exit;
            } else {
                echo '<h3 style="text-align: center">done</h3>';
                echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next=done"; }, 500);</script>';
                exit;
            }
        } else {

            $this->load->model('searches_model');

            $searches   = unserialize(get_option('unprocessed_searches'));
            $total      = count($searches);
            $load_time  = get_option('last_load_key');
            $multiple   = 20;
            echo '<div style="text-align:center">We have successfully download <strong>' . $total . ' searches</strong> data from API</div>';

            echo '<h2 style="text-align: center;margin-top: 45px;">Now we are moving the data to database</h2>';

            if ( ! isset($_GET['process']) ) {
                echo '<img src="'.site_url('assets/images/ajax-loader.gif').'" alt="loading data" style="margin: 0 auto; display: block;">';
            } elseif ( isset($_GET['process']) && $_GET['process'] != 'completed') {
                echo '<img src="'.site_url('assets/images/ajax-loader.gif').'" alt="loading data" style="margin: 0 auto; display: block;">';
            }

            if ( ! isset( $_GET['process'] ) ) {

                if ( $total <= $multiple ) {

                    foreach ($searches as $key => $search) {

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
                            'meta' => serialize(array('new' => 1)),
                            'added_date' => time()
                        );

                        $this->searches_model->add($data);
                    }

                    for ($i = 0; $i < $total; $i++) {
                        $middle_name    = isset($searches[$i]->subject->middle_name) ? $searches[$i]->subject->middle_name : '';
                        $last_name      = isset($searches[$i]->subject->last_name) ? $searches[$i]->subject->last_name : '';
                        $state          = isset($searches[$i]->subject->state) ? $searches[$i]->subject->state : '';
                        $data = array(
                            'search_ID' => $searches[$i]->search_id,
                            'search_status' => $searches[$i]->search_status,
                            'first_name' => $searches[$i]->subject->first_name,
                            'middle_name' => $middle_name,
                            'last_name' => $last_name,
                            'state' => $state,
                            'orig_data' => serialize($searches[$i]),
                            'meta' => serialize(array('new' => 1)),
                            'added_date' => time()
                        );

                        $this->searches_model->add($data);

                        if ( $i == ($total-1) ) {
                            echo '<div style="text-align: center">All data have been imported to database</div>';
                            echo '<script>setTimeout(function(){ window.location = "/admin/searches"; }, 500);</script>';
                            exit;
                        }
                    }

                } else {
                    echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next=done&process=0"; }, 1000);</script>';
                    exit;
                }
                
            } else {

                $process    = $_GET['process'];

                if ( $process != 'completed' ) {

                    $total_load = intval($process)+$multiple;
                    $percentage = intval(($process/$total)*100);

                    echo '<h3 style="text-align: center">'.$percentage.'% data processed</h3>';

                    for($i = $process; $i < $total_load; $i++) {

                        $middle_name    = isset($searches[$i]->subject->middle_name) ? $searches[$i]->subject->middle_name : '';
                        $last_name      = isset($searches[$i]->subject->last_name) ? $searches[$i]->subject->last_name : '';
                        $state          = isset($searches[$i]->subject->state) ? $searches[$i]->subject->state : '';
                        $data = array(
                            'search_ID'     => $searches[$i]->search_id,
                            'search_status' => $searches[$i]->search_status,
                            'first_name'    => $searches[$i]->subject->first_name,
                            'middle_name'   => $middle_name,
                            'last_name'     => $last_name,
                            'state'         => $state,
                            'orig_data' => serialize($searches[$i]),
                            'meta' => serialize(array('new' => 1)),
                            'added_date' => time()
                        );

                        $this->searches_model->add($data);

                        if ( $i == ($total-1) ) {
                            echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next=done&process=completed"; }, 1000);</script>';
                            exit;
                        }

                        if ( $i == ($total_load-1) ) {
                            echo '<script>setTimeout(function(){ window.location = "/api/load_fresh_data?next=done&process='.$total_load.'"; }, 1000);</script>';
                        }

                    }

                    exit;

                } else {
                    echo '<div style="text-align: center">All data have been imported to database</div>';
                    echo '<script>setTimeout(function(){ window.location = "/admin/searches"; }, 500);</script>';
                    exit;
                    
                }

            }
        }

    }

    public function refresh_search_data() {
    	if ( ! isset($_SERVER['HTTP_ORIGIN']) ) {
    		show_404();
    		exit;
    	}

    	header('Access-Control-Allow-Origin: '.site_url());
    	header('Access-Control-Allow-Methods: POST');

        $this->load->helper('api_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->model('searches_model');

        //$total_rows = $this->searches_model->total_rows();

        $limit 			= 50;
        $start_row 		= isset($_POST['start_row']) && $_POST['start_row'] != '' ? $_POST['start_row'] : 0;
        $ids 			= $this->searches_model->get_ids($start_row, $limit);
        $last_row 		= (count($ids) == $limit) ? count($ids) + $start_row : '';
        $last_load_key 	= get_option('last_load_key');
        $updated_ids 	= array();
        
        foreach ($ids as $key => $search) {
        	$search_id = $search->search_ID;
        	$load_key = get_search_meta($search_id, 'last_load_key');
        	$search_status = $this->searches_model->get_status($search_id);

        	if ( $load_key == $last_load_key ) { continue; }

        	if ( $search_status != 'P' ) { continue; }

        	$updated_ids[$search_id] = array('sid' => $search_id, 'last_load' => $load_key);

        	$this->searches_model->update($search_id, 'C');
        	
        }

        echo json_encode(array('status' => 'success', 'start_row' => $last_row, 'last_load' => $last_load_key, 'ids' => $updated_ids));
        exit;
    }

    public function load_tables() {

        if ( ! isset($_SERVER['HTTP_ORIGIN']) ) {
            show_404();
            exit;
        }

        header('Access-Control-Allow-Origin: '.site_url());
        header('Access-Control-Allow-Methods: POST');

        if ( ! isset($_POST['table_name']) ) { echo json_encode(array('status' => 'error', 'message' => 'please provide table name')); exit; }

        // load api config
        $this->load->helper('api_helper');
        $this->load->helper('settings_helper');

        $token = generate_api_token();

        $tables = ab_load_table($_POST['table_name']);

        echo json_encode(array('status'=> 'success', 'table_name' => $_POST['table_name'], 'result' => $tables));
        exit;

    }
}