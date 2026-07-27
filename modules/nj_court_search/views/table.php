<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasSensitive = function_exists('nj_court_search_staff_can') && nj_court_search_staff_can('view_sensitive');

$aColumns = [
    db_prefix() . 'nj_court_searches.created_at',
    'CONCAT(' . db_prefix() . 'nj_court_searches.first_name, \' \', ' . db_prefix() . 'nj_court_searches.last_name)',
    db_prefix() . 'nj_court_searches.dob',
    db_prefix() . 'nj_court_searches.lead_id',
    db_prefix() . 'nj_court_searches.reference_id',
    db_prefix() . 'nj_court_searches.status',
    db_prefix() . 'nj_court_searches.result_count',
    'CONCAT(' . db_prefix() . 'staff.firstname, \' \', ' . db_prefix() . 'staff.lastname)',
    db_prefix() . 'nj_court_searches.updated_at',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'nj_court_searches';

$join = [
    'LEFT JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'nj_court_searches.submitted_by',
];

$where = [];

$status = $this->ci->input->post('status');
if ($status !== null && $status !== '') {
    array_push($where, 'AND ' . db_prefix() . 'nj_court_searches.status = "' . $this->ci->db->escape_str($status) . '"');
}

$from = $this->ci->input->post('from');
if ($from) {
    $fromSql = to_sql_date($from);
    if ($fromSql) {
        array_push($where, 'AND DATE(' . db_prefix() . 'nj_court_searches.created_at) >= "' . $this->ci->db->escape_str($fromSql) . '"');
    }
}

$to = $this->ci->input->post('to');
if ($to) {
    $toSql = to_sql_date($to);
    if ($toSql) {
        array_push($where, 'AND DATE(' . db_prefix() . 'nj_court_searches.created_at) <= "' . $this->ci->db->escape_str($toSql) . '"');
    }
}

$additionalSelect = [
    db_prefix() . 'nj_court_searches.id',
    db_prefix() . 'nj_court_searches.client_id',
    db_prefix() . 'nj_court_searches.contact_id',
    db_prefix() . 'nj_court_searches.first_name',
    db_prefix() . 'nj_court_searches.last_name',
    db_prefix() . 'nj_court_searches.middle_name',
    db_prefix() . 'nj_court_searches.suffix',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = _dt($aRow[db_prefix() . 'nj_court_searches.created_at']);

    $name = nj_court_search_subject_name($aRow);
    $subject = '<a href="' . admin_url('nj_court_search/view/' . $aRow['id']) . '">' . html_escape($name) . '</a>';
    $subject .= '<div class="row-options">';
    $subject .= '<a href="' . admin_url('nj_court_search/view/' . $aRow['id']) . '">' . _l('view') . '</a>';
    $subject .= '</div>';
    $row[] = $subject;

    $row[] = nj_court_search_format_dob($aRow[db_prefix() . 'nj_court_searches.dob'], $hasSensitive);
    $row[] = nj_court_search_linked_record_html($aRow);
    $row[] = !empty($aRow[db_prefix() . 'nj_court_searches.reference_id'])
        ? html_escape($aRow[db_prefix() . 'nj_court_searches.reference_id'])
        : '—';
    $row[] = nj_court_search_status_badge($aRow[db_prefix() . 'nj_court_searches.status']);
    $row[] = (int) $aRow[db_prefix() . 'nj_court_searches.result_count'];

    $staffKey = 'CONCAT(' . db_prefix() . 'staff.firstname, \' \', ' . db_prefix() . 'staff.lastname)';
    $row[] = !empty($aRow[$staffKey]) ? html_escape($aRow[$staffKey]) : '—';
    $row[] = _dt($aRow[db_prefix() . 'nj_court_searches.updated_at']);

    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
