<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!has_permission('invoices', '', 'view')) {
            access_denied('invoice');
        }

        $this->load->helper('search_helper');
        $this->load->helper('search_meta_helper');
        $this->load->helper('settings_helper');
        $this->load->helper('countries_helper');
        $this->load->model('invoice_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = '')
    {
        $data['title'] = 'Invoices - AmazonScreening';
        $this->load->view( 'admin/searches/invoices', $data );
    }

    public function initial() {
        if ( ! isset($_GET['bismillah']) ) {
            redirect('/admin/searches', 'refresh');
            exit;
        }
        
        $this->invoice_model->create_invoice_table();
        $this->invoice_model->create_invoice_item_table();

        redirect('/admin/invoice', 'refresh');
        exit;
    }

    public function new() {
        $data['title']  = 'Create New Invoice - AmazonScreening';
        $data['states'] = us_states();
        $this->load->view( 'admin/searches/new_invoice', $data );
    }

    public function delete($id = '') {
        $this->invoice_model->delete($id);
        redirect('/admin/invoice', 'refresh');
        exit;
    }

    public function get_data() {

        $offset = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit  = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw   = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $where  = '';
        $order              = array();
        $order['orderby']   = 'id';
        $order['order']     = 'ASC';

        $rows = $this->invoice_model->get($where, $limit, $offset, $order);
        $total_rows = $this->invoice_model->total_rows();
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

        foreach ($rows as $row_id => $invoice) {
            $addl_data      = isset($invoice->addl_data) && $invoice->addl_data != '' ? unserialize($invoice->addl_data) : array();
            $id             = $invoice->id;
            $inv_prefix     = isset($addl_data['invoice_prefix']) ? $addl_data['invoice_prefix'] : '';
            $inv_id         = $inv_prefix . $invoice->inv_ID;
            $cust_name      = $invoice->cust_name . ', ' . $invoice->cust_company;
            $cust_address   = $invoice->cust_address . ', ' . $invoice->cust_city;
            $get_items      = $this->invoice_model->get_items($id);
            $items          = '-';
            $created_date   = $invoice->created_date;
            $due_date       = $invoice->due_date;
            $total_bill     = 0;
            $action         = '<a href="/admin/invoice/download/'.$id.'" target="_blank">download</a> | <a href="/admin/invoice/delete/'.$id.'" onclick="return confirm(\'Are you sure want to delete invoice '.$inv_id.'?\');">delete</a>';

            if ( $get_items ) {
                $items = '';
                foreach ($get_items as $key => $item) {
                    $items .= $item->description . '<br/>';
                }

                $amounts = array_column( $get_items, 'price');
                $total_bill = '$' . array_sum($amounts);
            }

            $records[] = array(
                'id' => $id,
                'inv_ID' => $inv_id,
                'cust_name' => $cust_name,
                'cust_address' => $cust_address,
                'items' => $items,
                'created_date' => $created_date,
                'due_date' => $due_date,
                'total_bill' => $total_bill,
                'action' => $action
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

    public function download($id = '') {

    	$invoice = $this->invoice_model->get_invoice($id);

    	$data['id'] 				= $id;
    	$data['time'] 				= time();
    	$data['inv_ID'] 			= $invoice->inv_ID;
    	$data['cust_name'] 			= $invoice->cust_name;
	    $data['cust_phone'] 		= $invoice->cust_phone;
	    $data['cust_company'] 		= $invoice->cust_company;
	    $data['cust_address'] 		= $invoice->cust_address;
	    $data['cust_city'] 			= $invoice->cust_city;
	    $data['cust_state'] 		= $invoice->cust_state;
	    $data['cust_postal_code'] 	= $invoice->cust_postal_code;
	    $data['created_date'] 		= $invoice->created_date;
	    $data['due_date'] 			= $invoice->due_date;
    	$addl_data 					= isset($invoice->addl_data) && $invoice->addl_data != '' ? unserialize($invoice->addl_data) : array();
    	$prefix 					= isset($addl_data['invoice_prefix']) ? $addl_data['invoice_prefix'] : '';
    	$data['prefix'] 			= $prefix;
    	$items 						= $this->invoice_model->get_items($id);
    	$amounts 					= array_column( $items, 'price');
        $total_bill 				= array_sum($amounts);
    	$data['items'] 				= $items;
        $data['total_bill']         = $total_bill;
    	$data['company_name'] 		= get_option('invoice_company_name');
    	$data['company_address'] 	= get_option('invoice_company_address');
    	$data['company_address2'] 	= get_option('invoice_company_city') . ', ' . get_option('company_state') . ' ' . get_option('invoice_company_postal_code');

    	$this->load->view('admin/searches/invoice_pdf', $data);
    }

    public function submit() {
        if ( ! isset( $_POST['submit_type'] ) ) {
            exit;
        }

        $submit_type            = $_POST['submit_type'];
        $invoice_prefix         = isset($_POST['invoice_prefix']) ? $_POST['invoice_prefix'] : '';
        $invoice_id             = isset($_POST['invoice_id']) ? $_POST['invoice_id'] : '';
        $invoice_date           = isset($_POST['invoice_date']) ? $_POST['invoice_date'] : '';
        $invoice_due_date       = isset($_POST['invoice_due_date']) ? $_POST['invoice_due_date'] : '';
        $client_name            = isset($_POST['client_name']) ? $_POST['client_name'] : '';
        $client_company_name    = isset($_POST['client_company_name']) ? $_POST['client_company_name'] : '';
        $client_address         = isset($_POST['client_address']) ? $_POST['client_address'] : '';
        $client_city            = isset($_POST['client_city']) ? $_POST['client_city'] : '';
        $client_state           = isset($_POST['client_state']) ? $_POST['client_state'] : '';
        $client_postal_code     = isset($_POST['client_postal_code']) ? $_POST['client_postal_code'] : '';
        $client_phone           = isset($_POST['client_phone']) ? $_POST['client_phone'] : '';
        $invoice_items          = isset($_POST['item'][0]['search_id']) ? $_POST['item'] : array();
        $addl_data              = array(
            'invoice_prefix' => $invoice_prefix
        );

        $invoice_data = array(
            'inv_ID'            => $invoice_id,
            'cust_name'         => $client_name,
            'cust_phone'        => $client_phone,
            'cust_company'      => $client_company_name,
            'cust_address'      => $client_address,
            'cust_city'         => $client_city,
            'cust_state'        => $client_state,
            'cust_postal_code'  => $client_postal_code,
            'created_date'      => $invoice_date,
            'due_date'          => $invoice_due_date,
            'addl_data'         => serialize($addl_data)
        );

        $this->invoice_model->add($invoice_data, $invoice_items);

        redirect('/admin/invoice', 'refresh');
        exit;

    }

}
