<?php defined('BASEPATH') or exit('No direct script access allowed');

class Report_model extends App_Model 
{
	public function __construct()
    {
        parent::__construct();
    }

    /**
     * get searches
     * @param  array $conditions  e.g array('where' => array(array('key' => 'value'), array('key' => 'value'))) 
     *                       conditions can have 2 options: where & where_in
     * @param  string $limit  [description]
     * @param  string $offset [description]
     * @return [type]         [description]
     */
    public function get($conditions = '', $limit = 10, $offset = '', $order = []) 
    {

        $this->db->where('search_status !=', 'P');
        $this->db->where('search_status !=', 'P12');
        //$this->db->where('search_status !=', 'C');

        if ( $conditions != '' && is_array($conditions) ) {
            foreach ($conditions as $syntax => $condition) {
                if ( $condition && is_array($condition) ) {
                    foreach ($condition as $key => $value) {
                        switch ($syntax) {
                            case 'where':
                                $this->db->where($key, $value);
                            break;
                            case 'where_in':
                                $this->db->where_in($key, $value);
                            break;
                        }
                    }
                }
            }
        }

        if ( $limit != '' ) {
            $offset = $offset == '' ? 0 : $offset;
            $this->db->limit($limit, $offset);
        }

        if ( $order ) {

            switch ($order['orderby']) {
                case 'row_id':
                    $orderby = 'list_id';
                    break;
                
                case 'status':
                    $orderby = 'search_status';
                    break;

                default:
                    $orderby = 'list_id';
                    break;
            }
            $this->db->order_by($orderby, $order['order']);
        }
        
        $q = $this->db->get('tblsearches');
        return $q->result();
    }

    public function total_rows($conditions = '') {

        $this->db->where('search_status !=', 'P');
        $this->db->where('search_status !=', 'P12');

        if ( $conditions != '' && is_array($conditions) ) {
            foreach ($conditions as $syntax => $condition) {
                if ( $condition && is_array($condition) ) {
                    foreach ($condition as $key => $value) {
                        switch ($syntax) {
                            case 'where':
                                $this->db->where($key, $value);
                            break;
                            case 'where_in':
                                $this->db->where_in($key, $value);
                            break;
                        }
                    }
                }
            }
        }

        $this->db->from('tblsearches');

        $total = $this->db->count_all_results(); 

        return $total;
    }
}