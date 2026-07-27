<?php defined('BASEPATH') or exit('No direct script access allowed');

class Conversion_model extends App_Model 
{
	public function __construct()
    {
        parent::__construct();
    }

    public function create_main_table()
    {
    	$this->load->dbforge();

		$fields = array(
			'list_id' => array(
				'type' => 'INT',
				'constraint' => 20,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'search_ID' => array(
				'type' => 'INT',
				'constraint' => 20,
				'unsigned' => TRUE
			),
			'search_status' => array(
				'type' => 'VARCHAR',
				'constraint' => '5',
				'default' => 'P'
			),
			'first_name' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
			),
			'middle_name' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'last_name' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'state' => array(
				'type' =>'VARCHAR',
				'constraint' => '5'
			),
			'orig_data' => array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'modified_data' => array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'cases' => array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'added_date' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('list_id', TRUE);
		$this->dbforge->create_table('searches', true);

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

    public function add($data) {
    	$search_id = $data['search_ID'];

    	$check = $this->search_id_exists($search_id);

    	if ( !$check ) {
    		$this->db->insert('tblsearches', $data); 
    	} else {
    		$this->db->where('search_ID', $search_id);
    		$this->db->update('tblsearches', $data); 
    	}
    }

    public function update_search_table() {
    	$this->load->dbforge();
    	$fields = array(
    		'sent_date' => array(
    			'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
    		)
    	);
		$this->dbforge->add_column('searches', $fields);
    }

    public function update_search($search_id = '', $columns = '') {

    	if ( $search_id == '' ) { return false; }

    	if ( $columns == '' ) { return false; }

    	if ( ! is_array($columns) ) { return false; }

		return $this->db->update(db_prefix().'searches', $columns, array('search_ID' => $search_id));
    }

    public function update_batch($data = '', $update_by = '') {
    	if ( $data == '' ) { return false; }
    	if ( $update_by == '' ) { return false; }
    	if ( ! is_array($data) ) { return false; }

    	return $this->db->update_batch(db_prefix().'searches', $data, $update_by);
    }

}