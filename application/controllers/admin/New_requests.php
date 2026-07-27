<?php defined('BASEPATH') or exit('No direct script access allowed');

class New_requests extends AdminController {

    public function index() 
    {
        $data['title'] = 'New Requests';

        $this->load->view( '/admin/searches/new_requests', $data );

    }

    public function get()
    {
        $offset     = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit      = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw       = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $where      = array(
            'search_status' => 'P'
        );
        $where['meta'][] = array(
            'operator' => '!=',
            'value' => ''
        );

        $counties   = json_decode( get_option('table_Counties'), true ); 
        $order      = array();

        $order['orderby'] = 'list_id';
        $order['order'] = 'asc';

        $this->load->model('searches_model'); 

        $data = $this->searches_model->get( '', $where, $limit, $offset, $draw, $order, $counties );

        if ( $data ) {
            $this->output->set_content_type('application/json')->set_output( json_encode( $data ) );
        } else {
            $this->output->set_content_type('application/json')->set_output( json_encode( array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array() ) ) );
        }
        
    }

}