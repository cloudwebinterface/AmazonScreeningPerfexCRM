<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Digital_delve extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('digital_delve/digital_delve_model');
        $this->load->library('digital_delve/digital_delve_client');
    }

    /**
     * List downloaded DigitalDelve orders.
     */
    public function index()
    {
        if (!digital_delve_staff_can('view')) {
            access_denied(DIGITAL_DELVE_MODULE_NAME);
        }

        $data['title']  = _l('digital_delve_orders');
        $data['orders'] = $this->digital_delve_model->get_recent(200);
        $data['total']  = $this->digital_delve_model->count_all();
        $data['last_download_at']    = get_option('digital_delve_last_download_at');
        $data['last_download_count'] = get_option('digital_delve_last_download_count');
        $data['can_download']        = digital_delve_staff_can('download');
        $data['import_limit']        = defined('DD_IMPORT_LIMIT') ? (int) DD_IMPORT_LIMIT : 3;
        $data['configured']          = $this->digital_delve_client->is_configured();

        $this->load->view('manage', $data);
    }

    /**
     * GET ORDERS only — cap at DD_IMPORT_LIMIT new rows. Never ack/push.
     */
    public function download()
    {
        if (!digital_delve_staff_can('download')) {
            access_denied(DIGITAL_DELVE_MODULE_NAME);
        }

        if ($this->input->method(true) !== 'POST') {
            set_alert('warning', _l('digital_delve_download_post_required'));
            redirect(admin_url('digital_delve'));
        }

        if (!$this->digital_delve_client->is_configured()) {
            set_alert('danger', _l('digital_delve_not_configured'));
            redirect(admin_url('digital_delve'));
        }

        $existing = $this->digital_delve_model->get_existing_order_ids();
        $orders   = $this->digital_delve_client->download_new_orders($existing);

        if ($orders === false) {
            $err = $this->digital_delve_client->get_last_error();
            log_message('error', 'DigitalDelve download failed: ' . $err);
            set_alert('danger', _l('digital_delve_download_failed') . ($err ? ': ' . $err : ''));
            redirect(admin_url('digital_delve'));
        }

        $saved = 0;
        foreach ($orders as $order) {
            if ($this->digital_delve_model->insert_if_new($order) > 0) {
                $saved++;
            }
        }

        update_option('digital_delve_last_download_at', date('Y-m-d H:i:s'));
        update_option('digital_delve_last_download_count', (string) $saved);

        if ($saved === 0) {
            set_alert('warning', _l('digital_delve_download_none'));
        } else {
            set_alert('success', sprintf(_l('digital_delve_download_success'), $saved));
        }

        redirect(admin_url('digital_delve'));
    }
}
