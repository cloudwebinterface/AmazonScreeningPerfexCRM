<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get all countries stored in database
 * @return array
 */
function get_all_countries()
{
    return hooks()->apply_filters('all_countries', get_instance()->db->get(db_prefix().'countries')->result_array());
}
/**
 * Get country row from database based on passed country id
 * @param  mixed $id
 * @return object
 */
function get_country($id)
{
    $CI = & get_instance();

    $country = $CI->app_object_cache->get('db-country-' . $id);

    if (!$country) {
        $CI->db->where('country_id', $id);
        $country = $CI->db->get(db_prefix().'countries')->row();
        $CI->app_object_cache->add('db-country-' . $id, $country);
    }

    return $country;
}
/**
 * Get country short name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_short_name($id)
{
    $country = get_country($id);
    if ($country) {
        return $country->iso2;
    }

    return '';
}
/**
 * Get country name by passed id
 * @param  mixed $id county id
 * @return mixed
 */
function get_country_name($id)
{
    $country = get_country($id);
    if ($country) {
        return $country->short_name;
    }

    return '';
}

function get_country_name_by_code($code) {
    $countries = array_column( get_all_countries(), 'short_name', 'iso2');
    return $countries[$code];
}

function us_states($state = '') {
    $states = array(
        'AL'=>'Alabama',
        'AK'=>'Alaska',
        'AZ'=>'Arizona',
        'AR'=>'Arkansas',
        'CA'=>'California',
        'CO'=>'Colorado',
        'CT'=>'Connecticut',
        'DE'=>'Delaware',
        'DC'=>'District of Columbia',
        'FL'=>'Florida',
        'GA'=>'Georgia',
        'HI'=>'Hawaii',
        'ID'=>'Idaho',
        'IL'=>'Illinois',
        'IN'=>'Indiana',
        'IA'=>'Iowa',
        'KS'=>'Kansas',
        'KY'=>'Kentucky',
        'LA'=>'Louisiana',
        'ME'=>'Maine',
        'MD'=>'Maryland',
        'MA'=>'Massachusetts',
        'MI'=>'Michigan',
        'MN'=>'Minnesota',
        'MS'=>'Mississippi',
        'MO'=>'Missouri',
        'MT'=>'Montana',
        'NE'=>'Nebraska',
        'NV'=>'Nevada',
        'NH'=>'New Hampshire',
        'NJ'=>'New Jersey',
        'NM'=>'New Mexico',
        'NY'=>'New York',
        'NC'=>'North Carolina',
        'ND'=>'North Dakota',
        'OH'=>'Ohio',
        'OK'=>'Oklahoma',
        'OR'=>'Oregon',
        'PA'=>'Pennsylvania',
        'RI'=>'Rhode Island',
        'SC'=>'South Carolina',
        'SD'=>'South Dakota',
        'TN'=>'Tennessee',
        'TX'=>'Texas',
        'UT'=>'Utah',
        'VT'=>'Vermont',
        'VA'=>'Virginia',
        'WA'=>'Washington',
        'WV'=>'West Virginia',
        'WI'=>'Wisconsin',
        'WY'=>'Wyoming',
    );

    return $state == '' ? $states : $states[$state];

}