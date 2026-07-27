<?php

defined('BASEPATH') or exit('No direct script access allowed');

function app_init_admin_sidebar_menu_items()
{
    if ( is_admin() ) {
        sidebar_menu_admin();
    } else {
        sidebar_menu_employee();
    }
}

function total_new_requests() {
    $CI = &get_instance();
    $CI->db->where('meta !=', '');
    $CI->db->where('search_status', 'P');
    $meta = $CI->db->get(db_prefix().'searches');

    return $meta->num_rows();
}

function total_pending_searches() {
    $CI = &get_instance();
    $CI->db->where('search_status', 'P');
    $meta = $CI->db->get(db_prefix().'searches');

    return $meta->num_rows();
}

function sidebar_menu_admin() {
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('new_requests', [
        'name'     => 'New Requests ('.total_new_requests().')',
        'href'     => admin_url('new_requests'),
        'position' => 0,
        'icon'     => 'fa fa-search-plus',
    ]);

    $CI->app_menu->add_sidebar_menu_item('searches', [
        'name'     => 'Pending Searches ('.total_pending_searches().')',
        'href'     => admin_url('searches'),
        'position' => 1,
        'icon'     => 'fa fa-search',
    ]);

    $CI->app_menu->add_sidebar_menu_item('summary', [
        'name'     => 'Case Summary',
        'href'     => admin_url('summary'),
        'position' => 2,
        'icon'     => 'fa fa-folder'
    ]);

    $CI->app_menu->add_sidebar_menu_item('history', [
        'name'     => 'History',
        'href'     => admin_url('history'),
        'position' => 3,
        'icon'     => 'fa fa-history'
    ]);

    if (has_permission('staff', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('staff', [
            'name'     => _l('als_staff'),
            'href'     => admin_url('staff'),
            'position' => 4,
            'icon'     => 'fa fa-user-o'
        ]);
    }


    $CI->app_menu->add_sidebar_menu_item('roles', [
        'name'     => 'Roles',
        'href'     => admin_url('roles'),
        'position' => 5,
        'icon'     => 'fa fa-address-card'
    ]);
    

    $CI->app_menu->add_sidebar_menu_item('invoices', [
        'name'     => 'Invoices',
        'href'     => admin_url('invoice'),
        'position' => 6,
        'icon'     => 'fa fa-file'
    ]);

    $CI->app_menu->add_sidebar_menu_item('report', [
        'name'     => 'Reports',
        'href'     => admin_url('report'),
        'position' => 7,
        'icon'     => 'fa fa-book'
    ]);

    $CI->app_menu->add_sidebar_menu_item('settings', [
        'name'     => 'Settings',
        'href'     => admin_url('settings'),
        'position' => 8,
        'icon'     => 'fa fa-gear'
    ]);

    $CI->app_menu->add_sidebar_menu_item('logs', [
        'name'     => 'Logs',
        'href'     => admin_url('logs'),
        'position' => 9,
        'icon'     => 'fa fa-file-text'
    ]);
}

function sidebar_menu_employee() {
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('new_requests', [
        'name'     => 'New Requests ('.total_new_requests().')',
        'href'     => admin_url('new_requests'),
        'position' => 0,
        'icon'     => 'fa fa-search-plus',
    ]);

    $CI->app_menu->add_sidebar_menu_item('searches', [
        'name'     => 'Pending Searches',
        'href'     => admin_url('searches'),
        'position' => 1,
        'icon'     => 'fa fa-search',
    ]);

    $CI->app_menu->add_sidebar_menu_item('summary', [
        'name'     => 'Case Summary',
        'href'     => admin_url('summary'),
        'position' => 2,
        'icon'     => 'fa fa-folder'
    ]);

    $CI->app_menu->add_sidebar_menu_item('history', [
        'name'     => 'History',
        'href'     => admin_url('history'),
        'position' => 3,
        'icon'     => 'fa fa-history'
    ]);

    $CI->app_menu->add_sidebar_menu_item('report', [
        'name'     => 'Reports',
        'href'     => admin_url('report'),
        'position' => 4,
        'icon'     => 'fa fa-book'
    ]);

    $CI->app_menu->add_sidebar_menu_item('profile', [
        'name'     => 'Profile',
        'href'     => admin_url('profile'),
        'position' => 5,
        'icon'     => 'fa fa-address-card'
    ]);

}