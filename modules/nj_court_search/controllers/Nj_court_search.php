<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Nj_court_search extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('nj_court_search/nj_court_search_model');
        $this->load->helper('nj_court_search/nj_court_search');
    }

    /**
     * List all searches (DataTables AJAX).
     */
    public function index()
    {
        if (!nj_court_search_staff_can('view')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path(NJ_COURT_SEARCH_MODULE_NAME, 'table'));
        }

        $data['title'] = _l('nj_court_search_all_searches');
        $data['statuses'] = nj_court_search_statuses();
        $this->load->view('manage', $data);
    }

    /**
     * New search form + submit.
     */
    public function create()
    {
        if (!nj_court_search_staff_can('create')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if (get_option('nj_court_search_enabled') != '1' && !nj_court_search_mock_mode_enabled()) {
            set_alert('warning', _l('nj_court_search_integration_disabled'));
        }

        if ($this->input->post()) {
            $this->create_post();

            return;
        }

        $data['title'] = _l('nj_court_search_new_search');
        $data['idempotency_key'] = nj_court_search_generate_uuid();
        $data['form'] = [
            'first_name'    => '',
            'middle_name'   => '',
            'last_name'     => '',
            'suffix'        => '',
            'dob'           => '',
            'reference_id'  => '',
            'lead_id'       => '',
            'client_id'     => '',
            'contact_id'    => '',
            'notes'         => '',
        ];
        $this->load->view('form', $data);
    }

    /**
     * Handle new search POST.
     */
    protected function create_post()
    {
        // Idempotency: prefer posted key for double-submit protection; always validate format.
        $idempotency = $this->input->post('idempotency_key', true);
        if (!$idempotency || !preg_match('/^[a-f0-9\-]{36}$/i', $idempotency)) {
            $idempotency = nj_court_search_generate_uuid();
        }

        $existing = $this->nj_court_search_model->get_by_idempotency_key($idempotency);
        if ($existing) {
            set_alert('warning', _l('nj_court_search_duplicate_submission'));
            redirect(admin_url('nj_court_search/view/' . $existing['id']));
        }

        $first = trim((string) $this->input->post('first_name', true));
        $last  = trim((string) $this->input->post('last_name', true));
        $dobIn = $this->input->post('dob', true);
        $dob   = nj_court_search_normalize_dob($dobIn);

        $middle = trim((string) $this->input->post('middle_name', true));
        $suffix = trim((string) $this->input->post('suffix', true));
        $ref    = trim((string) $this->input->post('reference_id', true));
        $notes  = trim((string) $this->input->post('notes', false));

        $leadId    = (int) $this->input->post('lead_id');
        $clientId  = (int) $this->input->post('client_id');
        $contactId = (int) $this->input->post('contact_id');
        $leadId    = $leadId > 0 ? $leadId : null;
        $clientId  = $clientId > 0 ? $clientId : null;
        $contactId = $contactId > 0 ? $contactId : null;

        $form = [
            'first_name'   => $first,
            'middle_name'  => $middle,
            'last_name'    => $last,
            'suffix'       => $suffix,
            'dob'          => $dobIn,
            'reference_id' => $ref,
            'lead_id'      => $leadId,
            'client_id'    => $clientId,
            'contact_id'   => $contactId,
            'notes'        => $notes,
        ];

        $errors = [];
        if ($first === '') {
            $errors[] = _l('nj_court_search_error_first_name');
        }
        if ($last === '') {
            $errors[] = _l('nj_court_search_error_last_name');
        }
        if ($dob === false) {
            $errors[] = _l('nj_court_search_error_dob');
        }

        $linkErrors = $this->nj_court_search_model->validate_links($leadId, $clientId, $contactId);
        $errors = array_merge($errors, $linkErrors);

        if (!empty($errors)) {
            set_alert('danger', implode(' ', $errors));
            $labels = $this->nj_court_search_model->resolve_link_labels($leadId, $clientId, $contactId);
            $data['title'] = _l('nj_court_search_new_search');
            $data['idempotency_key'] = $idempotency;
            $data['form'] = array_merge($form, $labels);
            $this->load->view('form', $data);

            return;
        }

        $searchId = $this->nj_court_search_model->create_search([
            'idempotency_key' => $idempotency,
            'first_name'      => $first,
            'middle_name'     => $middle !== '' ? $middle : null,
            'last_name'       => $last,
            'suffix'          => $suffix !== '' ? $suffix : null,
            'dob'             => $dob,
            'reference_id'    => $ref !== '' ? $ref : null,
            'lead_id'         => $leadId,
            'client_id'       => $clientId,
            'contact_id'      => $contactId,
            'notes'           => $notes !== '' ? $notes : null,
            'status'          => 'draft',
            'submitted_by'    => get_staff_user_id(),
        ]);

        $this->nj_court_search_model->add_event($searchId, 'search_created', [
            'new_status' => 'draft',
            'event_data' => [
                'reference_id' => $ref !== '' ? $ref : null,
                'has_lead'     => (bool) $leadId,
                'has_client'   => (bool) $clientId,
                'has_contact'  => (bool) $contactId,
            ],
        ]);

        $canSubmit = get_option('nj_court_search_enabled') == '1' || nj_court_search_mock_mode_enabled();
        if (!$canSubmit) {
            $this->nj_court_search_model->transition_status($searchId, 'submission_failed', [
                'error_code'    => 'integration_disabled',
                'error_message' => 'Module integration is disabled.',
            ], 'api_submission_failed');

            set_alert('danger', _l('nj_court_search_integration_disabled'));
            redirect(admin_url('nj_court_search/view/' . $searchId));
        }

        $result = $this->nj_court_search_model->submit_to_api($searchId);

        if ($result['success']) {
            set_alert('success', _l('nj_court_search_submitted_success'));
        } else {
            set_alert('danger', _l('nj_court_search_submitted_failed') . ' ' . $result['message']);
        }

        redirect(admin_url('nj_court_search/view/' . $searchId));
    }

    /**
     * Search detail.
     */
    public function view($id = '')
    {
        if (!nj_court_search_staff_can('view')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        $id = (int) $id;
        $search = $this->nj_court_search_model->get($id);
        if (!$search) {
            set_alert('danger', _l('nj_court_search_not_found'));
            redirect(admin_url('nj_court_search'));
        }

        $canSensitive = nj_court_search_staff_can('view_sensitive');
        $results = null;

        if (!empty($search['result_json'])) {
            $decoded = json_decode($search['result_json'], true);
            if (is_array($decoded) && !empty($decoded['_purged'])) {
                $results = $decoded;
            } elseif ($canSensitive) {
                $results = $decoded;
                $sessionKey = 'nj_court_sensitive_viewed_' . $id;
                if (!$this->session->userdata($sessionKey)) {
                    $this->nj_court_search_model->add_event($id, 'sensitive_result_viewed', [
                        'staff_id'   => get_staff_user_id(),
                        'event_data' => ['result_count' => (int) $search['result_count']],
                    ]);
                    $this->session->set_userdata($sessionKey, 1);
                }
            } else {
                $results = [
                    '_masked'      => true,
                    'result_count' => (int) $search['result_count'],
                ];
            }
        }

        $data['title']         = _l('nj_court_search_detail');
        $data['search']        = $search;
        $data['events']        = $this->nj_court_search_model->get_events($id);
        $data['results']       = $results;
        $data['can_sensitive'] = $canSensitive;
        $data['can_retry']     = nj_court_search_staff_can('retry')
            && in_array($search['status'], nj_court_search_retryable_statuses(), true);
        $data['can_cancel']    = nj_court_search_staff_can('cancel')
            && in_array($search['status'], nj_court_search_cancellable_statuses(), true);
        $data['can_refresh']   = !empty($search['external_job_id'])
            && !in_array($search['status'], ['draft'], true);

        $this->load->view('detail', $data);
    }

    /**
     * Manual status refresh (POST).
     */
    public function refresh($id = '')
    {
        if (!nj_court_search_staff_can('view')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if ($this->input->method(true) !== 'POST') {
            show_error('Method not allowed', 405);
        }

        $id = (int) $id;
        $result = $this->nj_court_search_model->refresh_status($id, get_staff_user_id(), 'manual_refresh');

        if ($result['success']) {
            set_alert('success', _l('nj_court_search_refresh_success'));
        } else {
            set_alert('warning', $result['message']);
        }

        redirect(admin_url('nj_court_search/view/' . $id));
    }

    /**
     * Retry (POST + CSRF).
     */
    public function retry($id = '')
    {
        if (!nj_court_search_staff_can('retry')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if ($this->input->method(true) !== 'POST') {
            show_error('Method not allowed', 405);
        }

        $id = (int) $id;
        $result = $this->nj_court_search_model->retry_search($id, get_staff_user_id());

        if ($result['success']) {
            set_alert('success', _l('nj_court_search_retry_success'));
        } else {
            set_alert('danger', $result['message']);
        }

        redirect(admin_url('nj_court_search/view/' . $id));
    }

    /**
     * Cancel (POST + CSRF).
     */
    public function cancel($id = '')
    {
        if (!nj_court_search_staff_can('cancel')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if ($this->input->method(true) !== 'POST') {
            show_error('Method not allowed', 405);
        }

        $id = (int) $id;
        $result = $this->nj_court_search_model->cancel_search($id, get_staff_user_id());

        if ($result['success']) {
            set_alert('success', _l('nj_court_search_cancel_success'));
        } else {
            set_alert('danger', $result['message']);
        }

        redirect(admin_url('nj_court_search/view/' . $id));
    }

    /**
     * Settings page.
     */
    public function settings()
    {
        if (!nj_court_search_staff_can('manage_settings')) {
            access_denied(NJ_COURT_SEARCH_MODULE_NAME);
        }

        if ($this->input->post()) {
            $this->save_settings();

            return;
        }

        $data['title'] = _l('nj_court_search_settings');
        $data['settings'] = [
            'enabled'               => get_option('nj_court_search_enabled'),
            'api_base_url'          => get_option('nj_court_search_api_base_url'),
            'api_timeout'           => get_option('nj_court_search_api_timeout'),
            'poll_interval'         => get_option('nj_court_search_poll_interval'),
            'poll_batch_size'       => get_option('nj_court_search_poll_batch_size'),
            'webhook_tolerance'     => get_option('nj_court_search_webhook_tolerance'),
            'webhook_enabled'       => get_option('nj_court_search_webhook_enabled'),
            'cron_polling_enabled'  => get_option('nj_court_search_cron_polling_enabled'),
            'result_retention_days' => get_option('nj_court_search_result_retention_days'),
            'retention_batch_size'  => get_option('nj_court_search_retention_batch_size') ?: '50',
            'role_restrictions'     => get_option('nj_court_search_role_restrictions'),
            'purge_on_uninstall'    => get_option('nj_court_search_purge_on_uninstall'),
            'api_key_mask'          => nj_court_search_secret_mask('nj_court_search_api_key_configured'),
            'webhook_secret_mask'   => nj_court_search_secret_mask('nj_court_search_webhook_secret_configured'),
            'webhook_url'           => site_url('nj_court_search/webhook'),
            'mock_allowed'          => nj_court_search_mock_mode_allowed(),
            'mock_mode'             => get_option('nj_court_search_mock_mode'),
            'mock_scenario'         => get_option('nj_court_search_mock_scenario'),
        ];

        $this->load->view('settings', $data);
    }

    protected function save_settings()
    {
        update_option('nj_court_search_enabled', $this->input->post('enabled') ? '1' : '0');
        update_option('nj_court_search_api_base_url', rtrim(trim((string) $this->input->post('api_base_url', true)), '/'));
        update_option('nj_court_search_api_timeout', (string) max(5, (int) $this->input->post('api_timeout')));
        update_option('nj_court_search_poll_interval', (string) max(15, (int) $this->input->post('poll_interval')));
        update_option('nj_court_search_poll_batch_size', (string) max(1, min(100, (int) $this->input->post('poll_batch_size'))));
        update_option('nj_court_search_webhook_tolerance', (string) max(60, (int) $this->input->post('webhook_tolerance')));
        update_option('nj_court_search_webhook_enabled', $this->input->post('webhook_enabled') ? '1' : '0');
        update_option('nj_court_search_cron_polling_enabled', $this->input->post('cron_polling_enabled') ? '1' : '0');
        update_option('nj_court_search_result_retention_days', (string) max(0, (int) $this->input->post('result_retention_days')));
        update_option(
            'nj_court_search_retention_batch_size',
            (string) max(1, min(200, (int) $this->input->post('retention_batch_size')))
        );
        update_option('nj_court_search_role_restrictions', trim((string) $this->input->post('role_restrictions', true)));
        update_option('nj_court_search_purge_on_uninstall', $this->input->post('purge_on_uninstall') ? '1' : '0');

        // Mock mode: never enable in production
        if (nj_court_search_mock_mode_allowed()) {
            update_option('nj_court_search_mock_mode', $this->input->post('mock_mode') ? '1' : '0');
            $scenario = trim((string) $this->input->post('mock_scenario', true));
            $allowedScenarios = [
                'success_flow', 'processing', 'completed', 'no_results',
                'failed', 'submission_failure', 'timeout', 'malformed',
            ];
            if (!in_array($scenario, $allowedScenarios, true)) {
                $scenario = 'success_flow';
            }
            update_option('nj_court_search_mock_scenario', $scenario);
        } else {
            update_option('nj_court_search_mock_mode', '0');
        }

        $apiKey = $this->input->post('api_key', false);
        if (is_string($apiKey) && trim($apiKey) !== '' && strpos($apiKey, '•') === false) {
            nj_court_search_set_secret('nj_court_search_api_key', trim($apiKey));
            update_option('nj_court_search_api_key_configured', '1');
        }

        $webhookSecret = $this->input->post('webhook_secret', false);
        if (is_string($webhookSecret) && trim($webhookSecret) !== '' && strpos($webhookSecret, '•') === false) {
            nj_court_search_set_secret('nj_court_search_webhook_secret', trim($webhookSecret));
            update_option('nj_court_search_webhook_secret_configured', '1');
        }

        $this->nj_court_search_model->add_event(null, 'settings_changed', [
            'staff_id'   => get_staff_user_id(),
            'event_data' => [
                'enabled'         => get_option('nj_court_search_enabled'),
                'webhook_enabled' => get_option('nj_court_search_webhook_enabled'),
                'cron_enabled'    => get_option('nj_court_search_cron_polling_enabled'),
                'mock_mode'       => get_option('nj_court_search_mock_mode'),
            ],
        ]);

        set_alert('success', _l('settings_updated'));
        redirect(admin_url('nj_court_search/settings'));
    }

    /**
     * AJAX test connection.
     */
    public function test_connection()
    {
        if (!nj_court_search_staff_can('manage_settings')) {
            ajax_access_denied();
        }

        if ($this->input->method(true) !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $this->load->library('nj_court_search/nj_court_api_client');
        $result = $this->nj_court_api_client->test_connection();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool) $result['success'],
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX searchable lead picker (create permission required).
     */
    public function ajax_search_leads()
    {
        $this->ajax_picker_guard();
        $q = (string) $this->input->post('q');
        header('Content-Type: application/json');
        echo json_encode($this->nj_court_search_model->search_leads($q));
    }

    /**
     * AJAX searchable customer picker (create permission required).
     */
    public function ajax_search_customers()
    {
        $this->ajax_picker_guard();
        $q = (string) $this->input->post('q');
        header('Content-Type: application/json');
        echo json_encode($this->nj_court_search_model->search_customers($q));
    }

    /**
     * AJAX searchable contact picker (create permission required).
     * Optional POST contact_userid limits results to that customer.
     */
    public function ajax_search_contacts()
    {
        $this->ajax_picker_guard();
        $q = (string) $this->input->post('q');
        $clientId = (int) $this->input->post('contact_userid');
        $clientId = $clientId > 0 ? $clientId : null;
        header('Content-Type: application/json');
        echo json_encode($this->nj_court_search_model->search_contacts($q, $clientId));
    }

    /**
     * Shared auth for module-local relation pickers.
     */
    protected function ajax_picker_guard()
    {
        if (!nj_court_search_staff_can('create')) {
            ajax_access_denied();
        }

        if ($this->input->method(true) !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
    }
}
