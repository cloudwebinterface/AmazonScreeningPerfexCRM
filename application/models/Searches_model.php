<?php defined('BASEPATH') or exit('No direct script access allowed');

class Searches_model extends App_Model 
{
	public function __construct()
    {
        parent::__construct();

    }

   	public function search_id_exists($search_id) {
    	$this->db->select('search_ID');
    	$this->db->where('search_ID', $search_id);

    	$check = $this->db->get('tblsearches');

    	if ( $check->num_rows() > 0 ) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function add($data) 
    {

		if ( ! is_array($data) ) { return; }

    	$search_id = $data['search_ID'];

    	$check = $this->search_id_exists($search_id);

    	if ( !$check ) {

    		$this->db->insert('tblsearches', $data); 

    	} else {

    		if ( isset($data['added_date']) ) { unset($data['added_date']); }

    		if ( isset($data['meta']) ) { unset($data['meta']); }
    		
    		$this->db->where('search_ID', $search_id);
    		$this->db->update('tblsearches', $data); 
    	}
	}
	
	public function update($id, $search_status = '', $data = '') 
    {
		if ( $data != '' && !is_array($data) ) { return; }

		if ( $search_status != '' ) {
			$search_data = array(
				'search_status' => $search_status
			);
		}

		if ( $data ) {
			if ( isset($data['search_status']) && isset($search_data['search_status']) && $data['search_status'] == $search_data['search_status'] ) {
				unset($search_data['search_status']);
			} elseif ( !isset($data['search_status']) && isset($search_data['search_status']) ) {
				$data['search_status'] = $search_data['search_status'];
				unset($search_data['search_status']);
			}

			$search_data = $data;
		}

		$this->db->update(db_prefix().'searches', $search_data, array('search_ID' => $id));
	
    }

    public function reset_search_status() {
    	$search_data = array(
			'search_status' => 'C'
		);

		$this->db->update(db_prefix().'searches', $search_data, array('search_status' => 'P'));
    }

    public function remove_from_new_list($id) {
    	$search_data = array(
			'meta' => ''
		);

		$this->db->update(db_prefix().'searches', $search_data, array('search_ID' => $id));
    }

    public function total_rows($where = '')
    {

		if ( $where != '' && is_array($where) ) {
    		foreach ($where as $key => $value) {
    			if ( is_array($value) ) {

    				foreach ($value as $idx => $val) {
    					$operator 	= $val['operator'];
	    				$v  		= $val['value'];
	    				$statement 	= $key . ' ' . $operator;
	    				$this->db->where( $statement, $v);
    				}

    			} else {
    				$this->db->where($key, $value);
    			}
    			
    		}
    	}

		$this->db->from('tblsearches');

		$q = $this->db->count_all_results(); 

		return $q;

    }

    public function total_search_row($search = array()) {
    	$this->load->helper('search_helper');
		$this->load->helper('search_meta_helper');

		$search_by = $search['search_by'];

		$result = '';

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
			
			$meta = $this->db->get(db_prefix().'search_meta');

			$result = $meta->num_rows();

			break;
			case 'state':
				$state = $search['search_value'];
				$state_length = strlen($state);
				$state_field = '"state";s:'.$state_length.':"'.strtoupper($state).'"';

				$this->db->where('meta_key', 'subject');
				$this->db->like('meta_value', $state_field);

				$meta = $this->db->get(db_prefix().'search_meta');

				$result = $meta->num_rows();
			break;
		}

		return $result;
    }

    public function get_ids(int $start, int $limit) {
    	$this->load->helper('search_helper');
		$this->load->helper('search_meta_helper');

		$this->db->select('search_ID');

		$this->db->where('search_status', 'P');

		$this->db->limit($limit, $start);

		$q = $this->db->get(db_prefix() . 'search_api');

		return $q->result();
    }

    public function get_row_id($search_id) {
    	$this->db->select('list_id');
    	$this->db->where('search_ID', $search_id);
    	$q = $this->db->get(db_prefix() . 'search_api');
    	$result = $q->row();

    	if ( $result ) {
    		return $result->list_id;
    	} else {
    		return false;
    	}
    }

    public function get_searches($limit = '', $where = '') {

    	if ( $limit != '' ) {
    		$this->db->limit($limit);
    	}

    	if ( $where != '' && is_array($where) ) {
    		foreach ($where as $key => $value) {
    			$this->db->where($key, $value);
    		}
    	}
    	
    	$q = $this->db->get('tblsearches');
    	return $q->result();
    }

    public function get_detail($search_id) {
    	$this->db->where('search_ID', $search_id);
    	$q = $this->db->get('tblsearches');
    	return $q->row();
    }

    public function get_load_date($search_id) {
    	$this->db->select('added_date');
    	$this->db->where('search_ID', $search_id);
    	$q = $this->db->get('tblsearches');
    	$result = $q->row();
    	if ( $result ) {
    		return $result->added_date;
    	} else {
    		return '';
    	}
    }

    public function get_cases($search_id) {
    	$this->db->select('cases');
    	$this->db->where('search_ID', $search_id);
    	$q = $this->db->get('tblsearches');
    	$result = $q->row();
    	if ( $result ) {
    		return unserialize($result->cases);
    	} else {
    		return array();
    	}
    }

    public function case_exists($case_number, $search_id) {
    	$cases 	= $this->get_cases($search_id);
    	$c 		= array_column($cases, 'case_number');

    	return in_array($case_number, $c);

    }

    public function submit_case($search_id, $cases = '') {

    	$check 	= $this->search_id_exists($search_id);
    	if ( $cases != '' ) {
    		$data 	= array( 'cases' => serialize($cases) );
    	} else {
    		$data 	= array( 'cases' => '' );
    	}

    	if ( $check ) {
    		$this->db->where('search_ID', $search_id);
    		$this->db->update('tblsearches', $data); 
    	}

    }

    public function get($id = '', $where = '', $limit = '', $offset = '', $draw = '', $order = '', $counties = array())
    {

    	$this->load->helper('search_helper');
    	$this->load->helper('settings_helper');
		$this->load->helper('search_meta_helper');
		
		if ( $id != '' ) {
			$where['search_ID'] = $id;
		}

		if ( $where != '' && is_array($where) ) {
    		foreach ($where as $key => $value) {
    			if ( is_array($value) ) {

    				foreach ($value as $idx => $val) {
    					$operator 	= $val['operator'];
	    				$v  		= $val['value'];
	    				$statement 	= $key . ' ' . $operator;
	    				$this->db->where( $statement, $v);
    				}

    			} else {
    				$this->db->where($key, $value);
    			}
    			
    		}
    	}

		/*if ( is_array($order) && isset($order['order']) && $order['order'] != '' && isset($order['orderby']) && $order['orderby'] != '' ) {
			$this->db->order_by($order['orderby'], $order['order']);
		}*/

		$this->db->order_by('first_name', 'ASC');

		if ( $limit != '' ) {
			$offset = $offset == '' ? 0 : $offset;
			$this->db->limit($limit, $offset);
		}

    	$q = $this->db->get(db_prefix() . 'searches'); 

		if ( $q->num_rows() == 0 ) { return; }
		
		$search_status = array(
			'P' => 'Pending',
			'N' => 'Not found',
			'F' => 'Search found'
		);

    	$data = array();
    	foreach ($q->result_array() as $key => $value) {
			$search_id 			= $value['search_ID'];
			$search_data 		= unserialize($value['orig_data']);
			$checkbox 			= '<input type="checkbox" name="selected_list[]" value="'.$search_id.'"/>';
			$search_id_column 	= '<a href="/admin/search/'.$search_id.'">'.$search_id.'</a>';
			$cases 				= $value['cases'];
			$cases_entered 		= $cases != '' ? '<i class="fa fa-check text-success"></i>' : '';
    		$subject 			= $search_data->subject;
			$name 				= $value['first_name'] . ' ' . $value['middle_name'] . ' ' . $value['last_name'];
			$aka_names 			= '';

			if ( isset($subject->aka_names) ) {
				$aka_names .= '<ul>';
				foreach ($subject->aka_names as $key => $aka) {
					$aka_names .= '<li>'.$aka->first_name.' '.$aka->middle_name.' '.$aka->last_name.'</li>';
				}
				$aka_names .= '</ul>';
			}

			$ssn 				= isset($subject->ssn) ? $subject->ssn : '';
			$dob 				= isset($subject->date_of_birth) ? $subject->date_of_birth : '';
			$search_type 		= strtoupper(search_type($search_data->search_type)) . ' ('.$search_data->search_type.')';
			$state              = (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');
			$county             = isset($search_data->search_county_id) ? $counties[$search_data->search_county_id]['county_name'] : '';
			$load_date 			= $value['added_date'] != '' ? date('m/d/Y h:i a', $value['added_date']) : '-';
			$client_notes 		= isset($search_data->client_notes) ? nl2br($search_data->client_notes) : '';
			$internal_notes 	= isset($search_data->internal_notes) ? nl2br($search_data->internal_notes) : '';
			$aka_dropdown 		= $aka_names != '' ? '<span class="aka-dropdown">Show aliases <i class="fa fa-caret-down"></i></span>' : '';

			$data[] = array(
				//'detail' => '+',
    			'NF' => $checkbox, 
    			'row_id' => $value['list_id'],
    			'search_id' => $search_id_column, 
    			'cases_entered' => $cases_entered, 
				'name' => $name . $aka_dropdown, 
				'ssn' => $ssn, 
				'dob' => dob_format($dob), 
				'search_type' => $search_type, 
				'state' => $state,
				'county' => $county,
				'load_date' => $load_date,
				'sent_date' => '-',
				'client_notes' => $client_notes,
				'internal_notes' => $internal_notes,
				'aka_names' => $aka_names,
				'sid' => $search_id
    		);
    	}

    	// find multiple records

    	return array(
    		'draw' => $draw == '' ? 1 : $draw,
    		'recordsTotal' => $this->total_rows($where),
    		'recordsFiltered' => $this->total_rows($where),
    		'data' => $data
    	);
	}

	public function get_searches_by_ssn($ssn, $search_statuses = ['F','N'])
	{
		
		$ssn_length 	= strlen((string)$ssn);
		$ssn_to_find 	= '"ssn";s:' . $ssn_length . ':"' . $ssn . '"';
		$query 			= '';
		$where 			= '';
		
		$this->db->select('list_id, search_ID, first_name, middle_name, last_name, orig_data, search_status');

		$n 		= 0;
		$total 	= count($search_statuses); 
		foreach( $search_statuses as $search_status ) {
			$n++;
			$last 	= $total == $n ? ') ' : '';

			if ( $n > 1 ) {
				$where .= "OR search_status='" . $search_status . "'" . $last;
			} else {
				$where .= "(search_status='" . $search_status . "'" . $last;
			}
			
		}

		$this->db->where($where);
		$this->db->like('orig_data', $ssn);

		$q 		= $this->db->get(db_prefix() . 'searches'); 
		$result = $q->result_array();

		return $result;

	}

	public function get_duplicate_searches() 
	{
		$this->db->select('orig_data');
		$this->db->where('search_status', 'P');
		$q 		= $this->db->get(db_prefix() . 'searches'); 
		$result = $q->result_array();
		$data 	= [];
		$find  	= [];
		$data_by_ssn = [];

		if ( $result ) {
			foreach ( $result as $key => $orig_data ) {
				$search 		= unserialize($orig_data['orig_data']);
				$user_data 		= $search->subject;
				$data[] 		= $user_data->ssn;
				$first_name 	= isset($user_data->first_name) ? $user_data->first_name : '';
				$middle_name 	= isset($user_data->middle_name) ? $user_data->middle_name : '';
				$last_name 		= isset($user_data->last_name) ? $user_data->last_name : '';

				$data_by_ssn[$user_data->ssn] = [
					'ssn' 	=> $user_data->ssn,
					'name' 	=> $first_name . ' ' . $middle_name . ' ' . $last_name
				];
			}
		}

		// lets collect the multiple records
		foreach ( array_count_values($data) as $ssn => $count ) {
			if ( $count == 1 ) {
				continue;
			}

			$find[] = [
				'count' => $count,
				'ssn'	=> $ssn,
				'name' 	=> $data_by_ssn[$ssn]['name']
			];

		}

		return $find;
	}

	public function check_tables() 
	{
		$this->load->dbforge();
		if ( ! $this->db->table_exists('search_api') ) {
			$search_field = array(
				'list_id' => array(
					'type' => 'INT',
					'constraint' => 20,
					'unsigned' => true,
					'auto_increment' => true
				),
				'search_ID' => array(
					'type' => 'INT',
					'constraint' => 20,
					'unsigned' => true
				),
				'search_status' => array(
					'type' => 'VARCHAR',
					'constraint' => 5,
					'default' => 'P'
				)
			);

			$this->dbforge->add_field($search_field);
			$this->dbforge->add_key('list_id', TRUE);
			$this->dbforge->create_table('search_api', TRUE);

		}

		if ( ! $this->db->table_exists('search_meta') ) {
			$meta_field = array(
				'meta_id' => array(
					'type' => 'INT',
					'constraint' => 20,
					'unsigned' => true,
					'auto_increment' => true
				),
				'search_ID' => array(
					'type' => 'INT',
					'constraint' => 30,
					'unsigned' => true
				),
				'meta_key' => array(
					'type' => 'VARCHAR',
					'constraint' => 200,
					'null' => true
				),
				'meta_value' => array(
					'type' => 'LONGTEXT',
					'null' => true
				)
			);

			$this->dbforge->add_field($meta_field);
			$this->dbforge->add_key('meta_id', TRUE);
			$this->dbforge->create_table('search_meta', TRUE);
			
		}

	}

	public function get_status($search_id) {
		$this->load->helper('search_helper');
		$this->load->helper('search_meta_helper');

		$this->db->select('search_status');
		$this->db->where('search_ID', $search_id);

		$q = $this->db->get(db_prefix() . 'search_api'); 
		$row = $q->row();
		return ($row) ? $row->search_status : false;

	}

	public function search($search = array(), $where = '', $limit = '', $offset = '', $draw = '', $order = '') 
	{
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

		$data = array();
		foreach ($result as $key => $object) {
			$search_id 			= $object['search_ID'];
			$checkbox 			= '<input type="checkbox" name="selected_list[]" value="'.$search_id.'"/>';
			$search_id_column 	= '<a href="/admin/search/'.$search_id.'">'.$search_id.'</a>';
			$cases 				= get_search_meta( $search_id, 'cases' );
			$cases_entered 		= $cases && (is_array($cases)||is_object($cases)) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
    		$subject 			= unserialize($object['meta_value']);
			$name 				= (isset($subject->first_name) ? $subject->first_name : '') . ' ' . (isset($subject->middle_name) ? $subject->middle_name : '') . ' ' . (isset($subject->last_name) ? $subject->last_name : '');
			$ssn 				= isset($subject->ssn) ? $subject->ssn : '';
			$dob 				= isset($subject->date_of_birth) ? $subject->date_of_birth : '';
			$search_type 		= strtoupper(search_type(get_search_meta( $search_id, 'search_type' ))) . ' ('.get_search_meta( $search_id, 'search_type' ).')';
			$state 				= (isset($subject->city) ? $subject->city : '') . ' - ' . (isset($subject->state) ? $subject->state : '');

			$list_q = 

			$data[] = array(
    			'NF' => $checkbox, 
    			'row_id' => $this->get_row_id($search_id),
    			'search_id' => $search_id_column, 
    			'cases_entered' => $cases_entered, 
				'name' => $name, 
				'ssn' => $ssn, 
				'dob' => $dob, 
				'search_type' => $search_type, 
				'state' => $state
    		);
		}

    	return array(
    		'draw' => $draw == '' ? 1 : $draw,
    		'recordsTotal' => $this->total_search_row($search),
    		'recordsFiltered' => $this->total_search_row($search),
    		'data' => $data
    	);
	}

	public function duplicate_cases($cases = '', $ids = array()) {

		if ( $cases == '' ) { return; }

		if ( !is_array($ids) ) { return; }

		$search_data = array(
			'cases' => $cases
		);

		$this->db->where_in( 'search_id', $ids );
		$this->db->update( db_prefix().'searches', $search_data );

	}
	
}
