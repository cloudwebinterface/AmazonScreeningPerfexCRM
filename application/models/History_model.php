<?php defined('BASEPATH') or exit('No direct script access allowed');

class History_model extends App_Model 
{
	public function __construct()
    {
        parent::__construct();

    }

    public function get($where = '', $limit = '', $offset = '', $order = '', $cases_only = '') {

    	$this->db->where('search_status !=', 'P');

    	if ( $cases_only == true ) {
    		$this->db->where('cases !=', '');
    		$this->db->where('cases !=', 'a:0:{}');
    	}

    	if ( $where != '' && is_array($where) ) {
    		foreach ($where as $key => $value) {
    			if ( is_array($value) ) {

    				foreach ($value as $idx => $val) {
    					$operator 	= $val['operator'];
	    				$v  		= $val['value'];
	    				$statement 	= $key . ' ' . $operator;
	    				if ( $operator == 'wildcard' ) {
	    					$this->db->like($key, $v, 'after'); 
	    				} else {
	    					$this->db->where( $statement, $v);
	    				}
    				}

    			} else {
    				$this->db->where($key, $value);
    			}
    			
    		}
    	}

    	if ( is_array($order) && isset($order['order']) && $order['order'] != '' && isset($order['orderby']) && $order['orderby'] != '' ) {
			$this->db->order_by($order['orderby'], $order['order']);
		}

    	if ( $limit != '' ) {
			$offset = $offset == '' ? 0 : $offset;
			$this->db->limit($limit, $offset);
		}

		$q = $this->db->get('tblsearches');
    	return $q->result();
    }

	public function get_old($where = '', $limit = '', $offset = '') {

		$this->db->select('*');
		$this->db->where('search_status !=', 'P');
		if ( $where != '' && is_array($where) ) {
			foreach ($where as $key => $val) {
				$this->db->where($key, $val);
			}
		}
		$this->db->order_by('list_id', 'desc');

		if ( $limit != '' ) {
			$offset = $offset == '' ? 0 : $offset;
			$this->db->limit($limit, $offset);
		}
		 
		$result = $this->db->get('tblsearch_api')->result(); 

		return $result;
	}

	public function total_rows($where = '', $cases_only = '') {

		$this->db->where('search_status !=', 'P');

		if ( $cases_only == true ) {
    		$this->db->where('cases !=', '');
    		$this->db->where('cases !=', 'a:0:{}');
    	}
		
		if ( $where != '' && is_array($where) ) {
			foreach( $where as $key => $val ) {
				$this->db->where($key, $val);
			}
		}

		$this->db->from('tblsearches');

		$total = $this->db->count_all_results(); 

		return $total;

	}

	public function total_old_rows($where = '') {
		$this->db->select('list_id');
		$this->db->where('search_status !=', 'P');

		if ( $where != '' && is_array($where) ) {
			foreach ($where as $key => $val) {
				$this->db->where($key, $val);
			}
		}
		$q = $this->db->get('tblsearch_api');

		return $q->num_rows();
	}

	public function total_case_rows($where = '') {
		$this->db->select('meta_id');
		$this->db->where('meta_key', 'cases');
		$this->db->where('meta_value !=', '');
		$this->db->where('meta_value !=', 'a:0:{}');

		$q = $this->db->get('tblsearch_meta');

		return $q->num_rows();
	}

	public function get_cases($limit = '', $offset = '') {
		$this->db->where('meta_key', 'cases');
		$this->db->where('meta_value !=', '');
		$this->db->where('meta_value !=', 'a:0:{}');
		if ( $limit != '' ) {
			$offset = $offset == '' ? 0 : $offset;
			$this->db->limit($limit, $offset);
		}
		$q = $this->db->get('tblsearch_meta');

		return $q->result_array();
	}

	public function search($search = array(), $limit = '', $offset = '') {
		$this->load->helper('search_helper');
		$this->load->helper('search_meta_helper');

		$search_by = $search['search_by'];

		switch ($search_by) {
			case 'name':

				$first_name 	= $search['first_name'];
				$flength 		= $first_name != '' ? strlen($first_name) : 0;
				$middle_name 	= $search['middle_name'];
				$mlength 		= $middle_name != '' ? strlen($middle_name) : 0;
				$last_name 		= $search['last_name'];
				$llength 		= $last_name != '' ? strlen($last_name) : 0;

				$fname = '"first_name";s:'.$flength.':"'.$first_name.'"';
				$mname = '"middle_name";s:'.$mlength.':"'.$middle_name.'"';
				$lname = '"last_name";s:'.$llength.':"'.$last_name.'"';

				$this->db->where('meta_key', 'subject');

				if ( $flength != 0 ) {
					$this->db->like('meta_value', $fname);
				}

				if ( $mlength != 0 ) {
					$this->db->like('meta_value', $mname);
				}

				if ( $llength != 0 ) {
					$this->db->like('meta_value', $lname);
				}

				if ( $limit != '' ) {
					$offset = $offset == '' ? 0 : $offset;
					$this->db->limit($limit, $offset);
				}
				
			break;

			case 'state':
				$state = $search['search_value'];
				$state_length = strlen($state);
				$state_field = '"state";s:'.$state_length.':"'.strtoupper($state).'"';

				$this->db->where('meta_key', 'subject');
				$this->db->like('meta_value', $state_field);

				if ( $limit != '' ) {
					$offset = $offset == '' ? 0 : $offset;
					$this->db->limit($limit, $offset);
				}

			break;
		}

		$meta = $this->db->get(db_prefix().'search_meta');

		$result = $meta->result_array();

		return $result;

	}
}
