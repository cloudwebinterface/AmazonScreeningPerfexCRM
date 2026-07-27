<?php defined('BASEPATH') or exit('No direct script access allowed');

class Summary_model extends App_Model 
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
	public function get($conditions = '', $limit = '', $offset = '') 
    {

        $this->db->where('search_status', 'P');
        $this->db->where('cases !=', '');
		$this->db->where('cases !=', 'a:0:{}');

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
    	
    	$q = $this->db->get('tblsearches');
    	return $q->result();
    }

	public function total_rows($where = '') {

		$this->db->where('search_status', 'P');
        $this->db->where('cases !=', '');
		$this->db->where('cases !=', 'a:0:{}');
		
		if ( $where != '' && is_array($where) ) {
			foreach( $where as $key => $val ) {
				$this->db->where($key, $val);
			}
		}

		$this->db->from('tblsearches');

		$total = $this->db->count_all_results(); 

		return $total;
	}
}
