<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Digital_delve_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function table()
    {
        return db_prefix() . 'digital_delve_orders';
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function get($id)
    {
        $this->db->where('id', (int) $id);
        $row = $this->db->get($this->table())->row_array();

        return $row ?: null;
    }

    /**
     * @return array list of order_detail_order_id
     */
    public function get_existing_order_ids()
    {
        $rows = $this->db->select('order_detail_order_id')->get($this->table())->result_array();
        $ids = [];
        foreach ($rows as $row) {
            $ids[] = $row['order_detail_order_id'];
        }

        return $ids;
    }

    /**
     * Insert if order_detail_order_id is new. Returns insert id or 0 if skipped.
     *
     * @param array $order
     * @return int
     */
    public function insert_if_new(array $order)
    {
        $orderId = isset($order['order_detail_order_id']) ? (string) $order['order_detail_order_id'] : '';
        if ($orderId === '') {
            return 0;
        }

        $this->db->where('order_detail_order_id', $orderId);
        if ($this->db->count_all_results($this->table()) > 0) {
            return 0;
        }

        $now = date('Y-m-d H:i:s');
        $insert = [
            'order_detail_order_id' => $orderId,
            'account_code'          => isset($order['account_code']) ? $order['account_code'] : null,
            'service_code'          => isset($order['service_code']) ? $order['service_code'] : null,
            'first_name'            => isset($order['first_name']) ? $order['first_name'] : null,
            'middle_name'           => isset($order['middle_name']) ? $order['middle_name'] : null,
            'last_name'             => isset($order['last_name']) ? $order['last_name'] : null,
            'dob'                   => isset($order['dob']) ? $order['dob'] : null,
            'ssn'                   => isset($order['ssn']) ? $order['ssn'] : null,
            'address_street'        => isset($order['address_street']) ? $order['address_street'] : null,
            'address_city'          => isset($order['address_city']) ? $order['address_city'] : null,
            'address_state'         => isset($order['address_state']) ? $order['address_state'] : null,
            'address_zip'           => isset($order['address_zip']) ? $order['address_zip'] : null,
            'county'                => isset($order['county']) ? $order['county'] : null,
            'state'                 => isset($order['state']) ? $order['state'] : null,
            'records_requested'     => isset($order['records_requested']) ? $order['records_requested'] : null,
            'years_to_search'       => isset($order['years_to_search']) ? $order['years_to_search'] : null,
            'court_docs_requested'  => isset($order['court_docs_requested']) ? $order['court_docs_requested'] : null,
            'rush_requested'        => isset($order['rush_requested']) ? $order['rush_requested'] : null,
            'special_instructions'  => isset($order['special_instructions']) ? $order['special_instructions'] : null,
            'reference_number'      => isset($order['reference_number']) ? $order['reference_number'] : null,
            'aliases'               => isset($order['aliases']) ? $order['aliases'] : null,
            'status'                => 'new',
            'response_sent'         => 'No',
            'raw_xml'               => isset($order['raw_xml']) ? $order['raw_xml'] : null,
            'imported_at'           => $now,
            'created_at'            => $now,
            'updated_at'            => $now,
        ];

        $this->db->insert($this->table(), $insert);

        return (int) $this->db->insert_id();
    }

    /**
     * @param int $limit
     * @return array
     */
    public function get_recent($limit = 100)
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit((int) $limit);

        return $this->db->get($this->table())->result_array();
    }

    /**
     * @return int
     */
    public function count_all()
    {
        return (int) $this->db->count_all_results($this->table());
    }
}
