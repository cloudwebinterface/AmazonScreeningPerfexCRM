<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: DigitalDelve
Description: Download-only DigitalDelve order import proof (GET ORDERS). No PUSH RESULTS / SEND RECEIPT.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('DIGITAL_DELVE_MODULE_NAME', 'digital_delve');
define('DIGITAL_DELVE_MODULE_VERSION', '1.0.0');

hooks()->add_action('admin_init', 'digital_delve_permissions');
hooks()->add_action('admin_init', 'digital_delve_init_menu');
hooks()->add_filter('module_digital_delve_action_links', 'digital_delve_action_links');

register_activation_hook(DIGITAL_DELVE_MODULE_NAME, 'digital_delve_activation_hook');
register_deactivation_hook(DIGITAL_DELVE_MODULE_NAME, 'digital_delve_deactivation_hook');
register_uninstall_hook(DIGITAL_DELVE_MODULE_NAME, 'digital_delve_uninstall_hook');
register_language_files(DIGITAL_DELVE_MODULE_NAME, [DIGITAL_DELVE_MODULE_NAME]);

function digital_delve_activation_hook()
{
    $CI = &get_instance();
    require_once __DIR__ . '/install.php';
}

function digital_delve_deactivation_hook()
{
    // Keep downloaded orders.
}

function digital_delve_uninstall_hook()
{
    require_once __DIR__ . '/uninstall.php';
}

function digital_delve_permissions()
{
    $capabilities = [
        'capabilities' => [
            'view'     => _l('digital_delve_permission_view'),
            'download' => _l('digital_delve_permission_download'),
        ],
    ];

    register_staff_capabilities(
        DIGITAL_DELVE_MODULE_NAME,
        $capabilities,
        _l('digital_delve')
    );
}

function digital_delve_init_menu()
{
    $CI = &get_instance();

    if (!is_admin() && !staff_can('view', DIGITAL_DELVE_MODULE_NAME)) {
        return;
    }

    $CI->app_menu->add_sidebar_menu_item('digital-delve', [
        'name'     => _l('digital_delve'),
        'href'     => admin_url('digital_delve'),
        'position' => 36,
        'icon'     => 'fa fa-cloud-download',
    ]);
}

function digital_delve_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('digital_delve') . '">' . _l('digital_delve_orders') . '</a>';

    return $actions;
}

function digital_delve_staff_can($capability)
{
    if (is_admin()) {
        return true;
    }

    return staff_can($capability, DIGITAL_DELVE_MODULE_NAME);
}
