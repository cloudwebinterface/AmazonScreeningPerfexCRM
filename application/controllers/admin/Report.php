<?php defined('BASEPATH') or exit('No direct script access allowed');

class Report extends AdminController {

    public function index() 
    {
        $this->load->model('searches_model');
        $this->load->model('report_model');
        $this->load->helper('search_helper');
 
        $data['title']      = 'Reports';
        $today              = date('F jS, Y', time());
        $today_start        = $today . ' 00:00:00';
        $today_end          = $today . ' 23:59:59';
        $start              = strtotime($today_start);
        $end                = strtotime($today_end);
        $from               = isset($_GET['date-from']) && $_GET['date-from'] != '' ? strtotime($_GET['date-from']) : $start;
        $to                 = isset($_GET['date-to']) && $_GET['date-to'] != '' ? strtotime($_GET['date-to']) : $end;
        $data['date_from']  = $from;
        $data['date_to']    = $to;

        $where              = [
            'where' => [
                'added_date >=' => $from,
                'added_date <=' => $to
            ]
        ];
        
        $where_found        = $where;
        $where_found['where']['search_status'] = 'F';
        $where_notfound     = $where;
        $where_notfound['where']['search_status'] = 'N';
        $where_canceled     = $where;
        $where_canceled['where']['search_status'] = 'C';

        $data['total']      = $this->report_model->total_rows($where);
        $data['found']      = $this->report_model->total_rows($where_found);
        $data['notfound']   = $this->report_model->total_rows($where_notfound);
        $data['canceled']   = $this->report_model->total_rows($where_canceled);

        $this->load->view( '/admin/searches/reports', $data );

    }

    public function get_data() {

        if ( ! isset($_POST['dateFrom']) ) {
            $r = array(
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array()
            );

            echo json_encode($r);
            exit;
        }

        if ( ! isset($_POST['dateTo']) ) {
            $r = array(
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array()
            );

            echo json_encode($r);
            exit;
        }

        $this->load->model('searches_model');
        $this->load->model('report_model');
        $this->load->helper('search_helper');

        $from               = $_POST['dateFrom'];
        $to                 = $_POST['dateTo'];
        $offset             = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit              = isset($_POST['length']) ? $_POST['length'] : 25;
        $draw               = isset($_POST['draw']) ? $_POST['draw'] : 1;
        $columns            = $_POST['columns'];
        $orders             = $_POST['order'];
        $order              = [];
        $orderby_column     = $orders[0]['column'];
        $order['orderby']   = isset($columns[$orderby_column]['data']) ? $columns[$orderby_column]['data'] : 'list_id';
        $order['order']     = isset($orders[0]['dir']) ? $orders[0]['dir'] : 'ASC';
        $counties           = json_decode( get_option('table_Counties'), true ); 

        $where              = [
            'where' => [
                'added_date >=' => $from,
                'added_date <=' => $to
            ]
        ];

        $rows               = $this->report_model->get($where, $limit, $offset, $order);
        $total              = $this->report_model->total_rows($where);


        if ( ! $rows ) {
            $r = array(
                'draw'              => $draw,
                'recordsTotal'      => 0,
                'recordsFiltered'   => 0,
                'data'              => array()
            );

            echo json_encode($r);
            exit;
        }

        $records = [];

        foreach ($rows as $row_id => $search) {
            $search_id          = $search->search_ID;
            $search_data        = unserialize($search->orig_data);
            $search_id_column   = '<a href="/admin/history/detail/'.$search_id.'">'.$search_id.'</a>';
            $cases              = $search->cases;
            $cases_entered      = $cases != '' ? '<i class="fa fa-check text-success"></i>' : '';
            $subject            = $search_data->subject;
            $name               = $search->first_name . ' ' . $search->middle_name . ' ' . $search->last_name;
            $ssn                = isset($subject->ssn) ? $subject->ssn : '';
            $dob                = isset($subject->date_of_birth) ? $subject->date_of_birth : '';
            $search_type        = strtoupper(search_type($search_data->search_type)) . ' ('.$search_data->search_type.')';
            $state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
            $county             = isset($search_data->search_county_id) ? $counties[$search_data->search_county_id]['county_name'] : '';
            $search_status      = $search->search_status;

            switch ($search_status) {
                case 'C':
                    $search_status = 'Canceled';
                break;

                case 'F':
                    $search_status = 'Found';
                break;

                case 'N':
                    $search_status = 'Not Found';
                break;
                
                default:
                    $search_status = $search->search_status;
                break;
            }


            $records[] = array(
                'row_id'        => $search->list_id,
                'search_id'     => $search_id_column, 
                'cases_entered' => $cases_entered, 
                'name'          => $name, 
                'ssn'           => $ssn, 
                'dob'           => $dob, 
                'search_type'   => $search_type, 
                'state'         => $state,
                'county'        => $county,
                'load_date'     => date('F j, Y H:i', $search->added_date),
                'sent_date'     => ($search->sent_date != '' ? date('F j, Y H:i', $search->sent_date) : '-'),
                'status'        => $search_status
            );
        }

        $r = array(
            'draw'              => $draw,
            'recordsTotal'      => $total,
            'recordsFiltered'   => $total,
            'data'              => $records
        );

        echo json_encode($r);
        exit;

    }

    public function import_sent_date($from = '', $to = '') {

        $data['title']  = 'Import Sent Date';
        $data['from']   = $from;
        $data['to']     = $to;
        $this->load->view( '/admin/searches/import_sentdate', $data );
    }

    public function import_sent_date_process() {
        if ( !isset($_POST['date-from']) ) { return; }
        if ( !isset($_POST['date-to']) ) { return; }
        if ( $_POST['date-from'] == '' ) { return; }
        if ( $_POST['date-to'] == '' ) { return; }

        $from       = strtotime($_POST['date-from']);
        $to         = strtotime($_POST['date-to'] . ' 23:59:59');

        redirect('/admin/report/import_sent_date/' . $from . '/' . $to, 'refresh');
        exit;
    }

    private function convert_data($data) {
    	$this->load->model('searches_model');
    	$list = array();
    	foreach ($data as $time => $value) {
            $logs = json_decode($value, true);
            if ( isset($logs['completed_updates']) ) {

                foreach ( $logs['completed_updates'] as $idx => $report ) {
                    $search_id = $report['search_id'];
                    $load_date = $this->searches_model->get_load_date($search_id) != '' ? $this->searches_model->get_load_date($search_id) : '';
                    $download_date = $load_date == '' ? '' : date('F j, Y H:i', $load_date);
                    $sent_date = date('F j, Y H:i', $time);
                    $completed = $report['completed'] == true ? '<i class="fa fa-check" style="color:green"></i>' : '';
                    $list[] = array( $search_id, $download_date, $sent_date, $completed, $report['message'] );
                    
                }
            }
        }

        return json_encode($list);
    }

    private function generate_csv($data) {
    	$this->load->model('searches_model');
    	$this->load->helper('settings_helper');
    	$last_file = get_option( 'last_report_csv_file' );
    	if ( $last_file && file_exists($last_file) ) {
    		unlink($last_file);
    	}
    	$url = '/uploads/searches_' . time() . '.csv';
        $file = FCPATH . $url;
        update_option( 'last_report_csv_file', $file, 'no' );
        $fp = fopen($file, 'w');

        $columns_header = array('search_id', 'load_date', 'sent_date', 'completed', 'message');
        fputcsv($fp, $columns_header);

        foreach ($data as $time => $value) {
            $logs = json_decode($value, true);
            if ( isset($logs['completed_updates']) ) {
                foreach ( $logs['completed_updates'] as $idx => $report ) {
                    $search_id = $report['search_id'];
                    $load_date = $load_date = $this->searches_model->get_load_date($search_id) != '' ? $this->searches_model->get_load_date($search_id) : '';
                    $download_date = $load_date == '' ? '' : date('F j, Y H:i', $load_date);
                    $sent_date = date('F j, Y H:i', $time);
                    $new_list = array( $search_id, $download_date, $sent_date, $report['completed'], $report['message'] );
                    fputcsv($fp, $new_list);
                }
            }
        }

        fclose($fp);

        return $url;
    }

}