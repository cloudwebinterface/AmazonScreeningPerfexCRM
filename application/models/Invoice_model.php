<?php defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends App_Model 
{
	public function __construct()
    {
        parent::__construct();
    }

    public function add($invoice_data = array(), $items = array()) {

    	if ( $invoice_data ) {
    		$this->db->insert('tblinvoice', $invoice_data); 
    		$invoice_id = $this->db->insert_id();
    		$data_items = array();
    		if ( $items ) {
    			foreach ($items as $key => $val) {
    				$data_items[] = array(
    					'invoice_id' => $invoice_id,
    					'description' => $val['search_id'],
    					'price' => $val['amount']
    				);
    			}
    		}

    		$this->db->insert_batch('tblinvoice_item', $data_items);

    	}
    }

    public function get($where = '', $limit = '', $offset = '', $order = '') {

    	//$this->db->where('search_status !=', 'P');

    	if ( $where != '' && is_array($where) ) {
    		foreach ($where as $key => $value) {
    			$this->db->where($key, $value);
    		}
    	}

    	if ( is_array($order) && isset($order['order']) && $order['order'] != '' && isset($order['orderby']) && $order['orderby'] != '' ) {
			$this->db->order_by($order['orderby'], $order['order']);
		}

    	if ( $limit != '' ) {
			$offset = $offset == '' ? 0 : $offset;
			$this->db->limit($limit, $offset);
		}

		$q = $this->db->get('tblinvoice');
    	return $q->result();
    }

    public function get_items($id = '') {
    	$this->db->where('invoice_id', $id);
    	$q = $this->db->get('tblinvoice_item');
    	return $q->result();
    }

    public function get_invoice($id = '') {
    	$this->db->where('id', $id);
    	$q = $this->db->get('tblinvoice');
    	return $q->row();
    }

    public function delete($id) {
   		$this->db->delete('tblinvoice', array('id' => $id));
   		$this->db->delete('tblinvoice_item', array('invoice_id' => $id));
    }

    public function total_rows($where = '') {

		//$this->db->where('search_status !=', 'P');
		
		if ( $where != '' && is_array($where) ) {
			foreach( $where as $key => $val ) {
				$this->db->where($key, $val);
			}
		}

		$this->db->from('tblinvoice');

		$total = $this->db->count_all_results(); 

		return $total;

	}

    public function create_invoice_table()
    {
    	$this->load->dbforge();

		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 20,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'inv_ID' => array(
				'type' => 'INT',
				'constraint' => '20',
				'unsigned' => TRUE
			),
			'cust_name' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
			),
			'cust_phone' => array(
				'type' =>'VARCHAR',
				'constraint' => '10',
				'null' => TRUE
			),
			'cust_company' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'cust_address' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'cust_city' => array(
				'type' =>'VARCHAR',
				'constraint' => '50',
				'null' => TRUE
			),
			'cust_state' => array(
				'type' =>'VARCHAR',
				'constraint' => '5',
				'null' => TRUE
			),
			'cust_postal_code' => array(
				'type' =>'VARCHAR',
				'constraint' => '10',
				'null' => TRUE
			),
			'created_date' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'due_date' => array(
				'type' =>'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'addl_data' => array(
				'type' => 'TEXT',
				'null' => TRUE
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('invoice', true);

		$fields = array(
			'inv_ID' => array(
				'type' => 'VARCHAR',
				'constraint' => '20',
				'null' => TRUE
			)
		);
		$this->dbforge->modify_column('invoice', $fields);

    }

    public function create_invoice_item_table()
    {
    	$this->load->dbforge();

		$fields = array(
			'item_id' => array(
				'type' => 'INT',
				'constraint' => 20,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'invoice_id' => array(
				'type' => 'INT',
				'constraint' => 20,
				'unsigned' => TRUE
			),
			'description' => array(
				'type' =>'VARCHAR',
				'constraint' => '200',
			),
			'price' => array(
				'type' =>'VARCHAR',
				'constraint' => '10',
				'null' => TRUE
			),
			'addl_field' => array(
				'type' =>'TEXT',
				'null' => TRUE,
			)
		);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('item_id', TRUE);
		$this->dbforge->create_table('invoice_item', true);

    }

}