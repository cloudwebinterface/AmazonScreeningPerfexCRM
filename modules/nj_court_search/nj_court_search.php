<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: NJ Court Search
Description: Submit and track NJ Court (Promis Gavel) searches via the external NJ Court API middleware. Links results to Perfex leads, customers, and contacts.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('NJ_COURT_SEARCH_MODULE_NAME', 'nj_court_search');
define('NJ_COURT_SEARCH_MODULE_VERSION', '1.0.0');

require_once __DIR__ . '/helpers/nj_court_search_helper.php';

hooks()->add_action('admin_init', 'nj_court_search_permissions');
hooks()->add_action('admin_init', 'nj_court_search_init_menu');
hooks()->add_action('after_cron_run', 'nj_court_search_cron');
hooks()->add_filter('module_nj_court_search_action_links', 'nj_court_search_action_links');

register_activation_hook(NJ_COURT_SEARCH_MODULE_NAME, 'nj_court_search_activation_hook');
register_deactivation_hook(NJ_COURT_SEARCH_MODULE_NAME, 'nj_court_search_deactivation_hook');
register_uninstall_hook(NJ_COURT_SEARCH_MODULE_NAME, 'nj_court_search_uninstall_hook');
register_language_files(NJ_COURT_SEARCH_MODULE_NAME, [NJ_COURT_SEARCH_MODULE_NAME]);

/**
 * Activation: create tables and default options.
 */
function nj_court_search_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

/**
 * Deactivation: keep all search history and settings.
 */
function nj_court_search_deactivation_hook()
{
    // Intentionally no data deletion.
}

/**
 * Uninstall: only purge tables when explicit option is enabled.
 */
function nj_court_search_uninstall_hook()
{
    require_once __DIR__ . '/uninstall.php';
}

/**
 * Staff capabilities (Perfex feature = nj_court_search).
 * Conceptual names: nj_court_search_view, nj_court_search_create, etc.
 */
function nj_court_search_permissions()
{
    $capabilities = [
        'capabilities' => [
            'view'             => _l('nj_court_search_permission_view'),
            'create'           => _l('nj_court_search_permission_create'),
            'view_sensitive'   => _l('nj_court_search_permission_view_sensitive'),
            'retry'            => _l('nj_court_search_permission_retry'),
            'cancel'           => _l('nj_court_search_permission_cancel'),
            'manage_settings'  => _l('nj_court_search_permission_manage_settings'),
        ],
        'help' => [
            'view_sensitive'  => _l('nj_court_search_permission_view_sensitive_help'),
            'manage_settings' => _l('nj_court_search_permission_manage_settings_help'),
        ],
    ];

    register_staff_capabilities(
        NJ_COURT_SEARCH_MODULE_NAME,
        $capabilities,
        _l('nj_court_search')
    );
}

/**
 * Left navigation: NJ Court Searches
 */
function nj_court_search_init_menu()
{
    $CI = &get_instance();

    if (!nj_court_search_staff_can('view')) {
        return;
    }

    $CI->app_menu->add_sidebar_menu_item('nj-court-search', [
        'collapse' => true,
        'name'     => _l('nj_court_search'),
        'position' => 35,
        'icon'     => 'fa fa-gavel',
    ]);

    $CI->app_menu->add_sidebar_children_item('nj-court-search', [
        'slug'     => 'nj-court-search-list',
        'name'     => _l('nj_court_search_all_searches'),
        'href'     => admin_url('nj_court_search'),
        'position' => 1,
    ]);

    if (nj_court_search_staff_can('create')) {
        $CI->app_menu->add_sidebar_children_item('nj-court-search', [
            'slug'     => 'nj-court-search-new',
            'name'     => _l('nj_court_search_new_search'),
            'href'     => admin_url('nj_court_search/create'),
            'position' => 2,
        ]);
    }

    if (nj_court_search_staff_can('manage_settings')) {
        $CI->app_menu->add_sidebar_children_item('nj-court-search', [
            'slug'     => 'nj-court-search-settings',
            'name'     => _l('nj_court_search_settings'),
            'href'     => admin_url('nj_court_search/settings'),
            'position' => 99,
        ]);
    }

    if (nj_court_search_staff_can('create')) {
        $CI->app->add_quick_actions_link([
            'name'       => _l('nj_court_search_new_search'),
            'url'        => 'nj_court_search/create',
            'permission' => NJ_COURT_SEARCH_MODULE_NAME,
            'position'   => 57,
        ]);
    }
}

/**
 * Module action links on Setup → Modules.
 */
function nj_court_search_action_links($actions)
{
    if (nj_court_search_staff_can('manage_settings')) {
        $actions[] = '<a href="' . admin_url('nj_court_search/settings') . '">' . _l('settings') . '</a>';
    }

    return $actions;
}

/**
 * Cron: poll non-terminal searches + optional result retention purge.
 */
function nj_court_search_cron()
{
    $CI = &get_instance();
    $CI->load->model('nj_court_search/nj_court_search_model');

    // Retention purge is independent of status polling (runs when days > 0).
    $CI->nj_court_search_model->purge_expired_results();

    if (get_option('nj_court_search_enabled') != '1'
        && !nj_court_search_mock_mode_enabled()) {
        return;
    }

    if (get_option('nj_court_search_cron_polling_enabled') != '1') {
        return;
    }

    $CI->nj_court_search_model->poll_pending_searches();
}
