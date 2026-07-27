<?php defined('BASEPATH') or exit('No direct script access allowed');

class Convert extends AdminController
{
	public function index() {

		if ( !isset($_GET['pass']) ) { return; }
		if ( $_GET['pass'] != 'bismillah' ) { return; }

		$this->load->model('conversion_model');
		$this->load->helper('settings_helper');
		$this->load->helper('search_meta_helper');

		echo '<div style="text-align:center">Mulai membuat tabel baru</div><br>';
		// create main table
		$this->conversion_model->create_main_table();

		echo '<div style="text-align:center">Tabel baru telah di buat, selanjutnya memindahkan semua data lama ke dalam tabel baru</div><br>';

		// move all active data to a new table
		$searches   = unserialize(get_option('unprocessed_searches'));
		$multiple 	= 10;
		$start 		= isset($_GET['start']) ? $_GET['start'] : 0;
		$next_stop 	= intval($start)+$multiple;
		$total 		= count($searches);
		$persen 	= intval(($start/$total)*100);

		echo '<div style="text-align:center;margin-bottom:30px">Total baris: '.$total.'</div><br>';

		echo '<img src="'.site_url('assets/images/ajax-loader.gif').'" alt="loading data" style="margin: 0 auto; display: block;">';

		echo '<h2 style="text-align:center">'.$persen.'% data dipindahkan</h2><br>';

		for ($i = $start; $i < $next_stop; $i++ ) {

			$cases = get_search_meta( $searches[$i]->search_id, 'cases' ) ? serialize(get_search_meta( $searches[$i]->search_id, 'cases' )) : '';
			
			$data = array(
				'search_ID' => $searches[$i]->search_id,
				'search_status' => $searches[$i]->search_status,
				'first_name' => $searches[$i]->subject->first_name,
				'middle_name' => $searches[$i]->subject->middle_name,
				'last_name' => $searches[$i]->subject->last_name,
				'state' => $searches[$i]->subject->state,
				'orig_data' => serialize($searches[$i]),
				'cases' => $cases,
				'added_date' => time()
			);

			$this->conversion_model->add($data);

			if ( $i == ($total-1) ) {
				echo '<script>setTimeout(function(){ window.location = "/admin/searches"; }, 1000);</script>';
                exit;
			} elseif ( $i == ($next_stop-1) ) {
				echo '<script>setTimeout(function(){ window.location = "/admin/convert?start='.$next_stop.'"; }, 1000);</script>';
                exit;
			}
		}

	}

	public function update_search_table() {
		if ( !isset($_GET['pass']) ) { return; }
		if ( $_GET['pass'] != 'bismillah' ) { return; }

		$this->load->model('conversion_model');
		$this->load->helper('settings_helper');
		$this->load->helper('search_meta_helper');
		$this->conversion_model->update_search_table();
		echo '<script>setTimeout(function(){ window.location = "/admin/searches"; }, 1000);</script>';
        exit;
	}

	public function move_sent_date() {

		if ( !isset($_GET['pass']) ) { return; }
		if ( $_GET['pass'] != 'bismillah' ) { return; }

        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');

        $logs   = get_option('search_update_success_logs');
        $d 		= $logs != '' ? array_reverse(unserialize($logs), true) : array();

        //delete_option('rearranged_success_logs');
        $get_neat_data = get_option('rearranged_success_logs');

        $this->load->model('conversion_model');

        if ( $get_neat_data ) {
        	$list = unserialize($get_neat_data);
        	$data_from = 'option';
        } else {
	        $list = array();
	    	foreach ($d as $time => $value) {
	            $logs = json_decode($value, true);
	            if ( isset($logs['completed_updates']) ) {
	                foreach ( $logs['completed_updates'] as $idx => $report ) {
	                    $search_id = $report['search_id'];
	                    $list[] = array( 'search_ID' => $search_id, 'sent_date' => $time );
	                }
	            }
	        }
	        $data_from = 'logs';

	        update_option('rearranged_success_logs', serialize($list), 'No');
        }

        echo '<pre>';
        echo 'total: ' . count($list) . '<br>';
        echo 'data di dapat dari ' . $data_from . '<br>';
        echo '</pre>';

        $increment 	= 200;
        $split_data = array_chunk($list, $increment);
        $total 		= count($list);
        $page 		= isset($_GET['page']) ? $_GET['page'] : 0;
        $total_page = count($split_data);
        $data_next 	= $page + 1;

        echo '<pre>';
        echo 'split by: ' . $increment . '<br>';
        echo 'total page: ' . $total_page . '<br>';
        echo 'current page: ' . $page . '<br>';
        echo '<h3>Please wait and don\'t close your browser. We are moving data page ' . $page . ' of ' . ($total_page-1) . '</h3>';
        echo '</pre>';
        echo '<img src="'.site_url('assets/images/ajax-loader.gif').'" alt="loading data" style="display: block;">';

        // update batch
        if ( $page < $total_page ) {
        	$data = $split_data[$page];
        	$this->conversion_model->update_batch($data, 'search_ID');
        	echo '<script>setTimeout(function(){ window.location = "/admin/convert/move_sent_date?pass=bismillah&page='.$data_next.'"; }, 1000);</script>';
    		exit;
        } else {
        	echo '<h3>Update complete</h3>';
        	echo '<script>setTimeout(function(){ window.location = "/admin/searches"; }, 1000);</script>';
    		exit;
        }

	}

}