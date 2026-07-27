<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* Base Site URL
* --------------------------------------------------------------------------
*
* URL to your CodeIgniter root. Typically this will be your base URL,
* WITH a trailing slash:
*
*   http://example.com/
*
*/
define('APP_BASE_URL', 'https://example.com/');

/*
* --------------------------------------------------------------------------
* Encryption Key
* IMPORTANT: Do not change this ever!
* --------------------------------------------------------------------------
*
* Generate a random 32-character string for new installations.
* http://codeigniter.com/user_guide/libraries/encryption.html
*/
define('APP_ENC_KEY', '');

/**
 * Database Credentials
 */
define('APP_DB_HOSTNAME', 'localhost');
define('APP_DB_USERNAME', '');
define('APP_DB_PASSWORD', '');
define('APP_DB_NAME', '');

/**
 * @since  2.3.0
 */
define('APP_DB_CHARSET', 'utf8');
define('APP_DB_COLLATION', 'utf8_general_ci');

/**
 * Session handler driver
 *
 * For files session use:
 * define('SESS_DRIVER', 'files');
 * define('SESS_SAVE_PATH', NULL);
 */
define('SESS_DRIVER', 'database');
define('SESS_SAVE_PATH', 'sessions');

/**
 * Enables CSRF Protection
 */
define('APP_CSRF_PROTECTION', true);

/**
 * Module CSRF exclusions (safe to extend)
 * Example for NJ Court Search signed webhooks:
 *   $app_csrf_exclude_uris[] = 'nj_court_search/webhook';
 */
// $app_csrf_exclude_uris = [];

/**
 * Accurate Background API Creds
 */
define('AB_API_USER', '');
define('AB_API_PASS', '');
define('AB_API_HOST', '');

define('AB_DATA', 'testapi'); // api (production), testapi (development)
