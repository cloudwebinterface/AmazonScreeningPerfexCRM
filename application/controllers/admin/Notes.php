<?php defined('BASEPATH') or exit('No direct script access allowed');

class Notes extends AdminController {

	public function add() {
		if ( ! isset($_POST['sid']) ) { return; }
		if ( $_POST['sid'] == '' )    { return; }

		$this->load->helper('api_helper');
		$this->load->helper('settings_helper');
		$this->load->model('searches_model');

		$search_id 	= $_POST['sid'];
        $row 		= $_POST['row'];
        $note 		= isset($_POST['note']) ? $_POST['note'] : '';
        //$eta 		= $this->input->post('eta');

        $params = array(
            'search_id' => intval($search_id)
        );

        if ( $note != '' ) {
            $params['note_additions'] = array($note);
        } else {
            $note = '';
        }

        if ( $note == '' ) {
            echo '<pre>';
            echo json_encode(array('status' => 'error', 'message' => 'Sending empty data'));
            echo '</pre>';
            exit;
        }

        $detail = array( $params );
        $data   = array(
            'search_updates' => $detail
        );
        
        $data = json_encode($data);

        $update = ab_update_searches_data($data);

        if ( ! isset($update->completed_updates) ) {

            $errors         = get_option('search_update_error_logs') ? unserialize(get_option('search_update_error_logs')) : array();
            $time           = time();
            $current        = date('Y/m/d H:i', $time);
            $errors[$time] = json_encode($update);
            update_option('search_update_error_logs', serialize($errors), 'no');

            redirect('/admin/searches?error=' . $time, 'refresh');
            exit;
        }

        foreach ($update->completed_updates as $key => $value) {
            $sid 			= $value->search_id;
            $search_data 	= ab_reload_search_data($sid);

            $data_to_db 	= array(
                'search_ID' 	=> $search_data->search_id,
                'search_status' => $search_data->search_status,
                'first_name' 	=> $search_data->subject->first_name,
                'middle_name' 	=> $search_data->subject->middle_name,
                'last_name' 	=> $search_data->subject->last_name,
                'state' 		=> $search_data->subject->state,
                'orig_data' 	=> serialize($search_data),
                'added_date' 	=> time()
            );
            $this->searches_model->add($data_to_db);

        }

        $base64_note = base64_encode($note);

        redirect('/admin/searches?internal_notes='. $base64_note .'&sid='. $search_id, 'refresh');
        exit;

	}

}