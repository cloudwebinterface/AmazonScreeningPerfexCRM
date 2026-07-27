<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Update_search extends AdminController
{
    public function index()
    {
        $this->load->helper('url');
        if ( ! isset($_POST['selected_list']) ) {
            redirect('/admin/searches', 'refresh');
        }

        $submit_mode    = $_POST['submit-mode'];
        $search_ids     = $_POST['selected_list'];
        $data           = array();

        if ( $submit_mode == 'no-record' ) {

            $detail = array();

            foreach ($search_ids as $key => $search_id) {
                $detail[] = array(
                    'search_id' => $search_id,
                    'search_status' => 'N'
                );
            }

            $data = array(
                'search_updates' => $detail
            );

            $data = json_encode($data);
        }

        $this->load->helper('api_helper');
        $update = ab_update_searches_data($search_ids, $data);
        if ( ! isset($update->completed_updates) ) {
            return array( 'status' => 'error', 'message' => json_encode($update) );
        }

        redirect('/admin/searches', 'refresh');
    }
}