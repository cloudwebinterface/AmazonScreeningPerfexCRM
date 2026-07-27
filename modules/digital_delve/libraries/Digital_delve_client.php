<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Minimal DigitalDelve XML client — GET ORDERS only.
 * Intentionally has no PUSH RESULTS or SEND RECEIPT methods.
 */
class Digital_delve_client
{
    protected $url;
    protected $username;
    protected $password;
    protected $account_code;
    protected $import_limit;
    protected $last_error = '';
    protected $last_raw_response = '';

    public function __construct()
    {
        $this->url          = defined('DD_API_URL') ? DD_API_URL : '';
        $this->username     = defined('DD_API_USERNAME') ? DD_API_USERNAME : '';
        $this->password     = defined('DD_API_PASSWORD') ? DD_API_PASSWORD : '';
        $this->account_code = defined('DD_ACCOUNT_CODE') ? DD_ACCOUNT_CODE : 'FFO';
        $this->import_limit = defined('DD_IMPORT_LIMIT') ? (int) DD_IMPORT_LIMIT : 3;
        if ($this->import_limit < 1) {
            $this->import_limit = 3;
        }
    }

    public function get_last_error()
    {
        return $this->last_error;
    }

    public function get_last_raw_response()
    {
        return $this->last_raw_response;
    }

    public function is_configured()
    {
        return $this->url !== '' && $this->username !== '' && $this->password !== '';
    }

    /**
     * Build GET ORDERS XML (credentials in body, matching FileFinders protocol).
     */
    public function build_get_orders_xml()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $orderXml = $dom->createElement('OrderXML');
        $orderXml->appendChild($dom->createElement('Method', 'GET ORDERS'));

        $auth = $dom->createElement('Authentication');
        $auth->appendChild($dom->createElement('Username', $this->username));
        $auth->appendChild($dom->createElement('Password', $this->password));
        $orderXml->appendChild($auth);

        $dom->appendChild($orderXml);

        return $dom->saveXML();
    }

    /**
     * POST XML as form field REQUEST.
     *
     * @return string|false
     */
    public function post_request($xml)
    {
        $this->last_error = '';
        $this->last_raw_response = '';

        if (!$this->is_configured()) {
            $this->last_error = 'DigitalDelve API is not configured (DD_API_URL / USERNAME / PASSWORD).';

            return false;
        }

        $ch = curl_init();
        $opts = [
            CURLOPT_URL            => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => ['REQUEST' => $xml],
        ];

        // Windows PHP often lacks a CA bundle; allow override via DD_SSL_VERIFY.
        $verifySsl = defined('DD_SSL_VERIFY') ? (bool) DD_SSL_VERIFY : true;
        $caInfo    = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if ($verifySsl && $caInfo && is_file($caInfo)) {
            $opts[CURLOPT_SSL_VERIFYPEER] = true;
            $opts[CURLOPT_SSL_VERIFYHOST] = 2;
            $opts[CURLOPT_CAINFO]         = $caInfo;
        } else {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
        }

        curl_setopt_array($ch, $opts);

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            $this->last_error = 'cURL error: ' . $error;

            return false;
        }

        $this->last_raw_response = (string) $response;

        if ($httpCode >= 400) {
            $this->last_error = 'HTTP ' . $httpCode . ' from DigitalDelve';

            return false;
        }

        if ($response === false || $response === '') {
            $this->last_error = 'Empty response from DigitalDelve';

            return false;
        }

        return $response;
    }

    /**
     * Parse GET ORDERS response into normalized order arrays.
     * Filters by account code. Does not persist.
     *
     * @return array|false
     */
    public function parse_orders($responseXml)
    {
        $this->last_error = '';

        $dom = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $dom->loadXML($responseXml);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        if (!$ok) {
            $this->last_error = 'Failed to parse DigitalDelve XML response';

            return false;
        }

        $countNode = $dom->getElementsByTagName('OrderCount')->item(0);
        $orderCount = $countNode ? (int) $countNode->nodeValue : 0;
        if ($orderCount < 1) {
            return [];
        }

        $orders = [];
        $orderNodes = $dom->getElementsByTagName('Order');
        foreach ($orderNodes as $orderNode) {
            $accountCode = $this->node_text($orderNode, 'AccountCode');
            if ($this->account_code !== '' && strcasecmp($accountCode, $this->account_code) !== 0) {
                continue;
            }

            $orderDetail = $orderNode->getElementsByTagName('OrderDetail')->item(0);
            if (!$orderDetail) {
                continue;
            }

            $orderId = $this->attr_text($orderDetail, 'OrderId');
            if ($orderId === '') {
                continue;
            }

            $aliases = [];
            $aliasesNodes = $orderNode->getElementsByTagName('Aliases');
            foreach ($aliasesNodes as $aliasesNode) {
                foreach ($aliasesNode->getElementsByTagName('Alias') as $aliasNode) {
                    $af = $this->node_text($aliasNode, 'FirstName');
                    $al = $this->node_text($aliasNode, 'LastName');
                    if ($af !== '' || $al !== '') {
                        $aliases[] = trim($al . ', ' . $af, ', ');
                    }
                }
            }

            $rawPiece = $dom->saveXML($orderNode);

            $orders[] = [
                'order_detail_order_id' => $orderId,
                'account_code'          => $accountCode,
                'service_code'          => $this->attr_text($orderDetail, 'ServiceCode'),
                'first_name'            => $this->node_text($orderNode, 'FirstName'),
                'middle_name'           => $this->node_text($orderNode, 'MiddleName'),
                'last_name'             => $this->node_text($orderNode, 'LastName'),
                'dob'                   => $this->node_text($orderNode, 'DOB'),
                'ssn'                   => $this->node_text($orderNode, 'SSN'),
                'address_street'        => $this->node_text($orderNode, 'StreetAddress'),
                'address_city'          => $this->node_text($orderNode, 'City'),
                'address_state'         => $this->node_text($orderNode, 'State'),
                'address_zip'           => $this->node_text($orderNode, 'Zipcode'),
                'county'                => $this->node_text($orderDetail, 'County'),
                'state'                 => $this->node_text($orderDetail, 'State'),
                'records_requested'     => $this->node_text($orderDetail, 'RecordsRequested'),
                'years_to_search'       => $this->node_text($orderDetail, 'YearsToSearch'),
                'court_docs_requested'  => $this->node_text($orderDetail, 'CourtDocsRequested'),
                'rush_requested'        => $this->node_text($orderDetail, 'RushRequested'),
                'special_instructions'  => $this->node_text($orderDetail, 'SpecialInstructions'),
                'reference_number'      => $this->node_text($orderDetail, 'ReferenceNo') !== ''
                    ? $this->node_text($orderDetail, 'ReferenceNo')
                    : $this->node_text($orderNode, 'ReferenceNo'),
                'aliases'               => implode('|', $aliases),
                'raw_xml'               => $rawPiece,
            ];
        }

        return $orders;
    }

    /**
     * Download orders and return up to import_limit items not already in $existingIds.
     *
     * @param array $existingIds order_detail_order_id values already stored
     * @return array|false
     */
    public function download_new_orders(array $existingIds = [])
    {
        $xml = $this->build_get_orders_xml();
        // Never log password — strip before logging.
        $safeXml = preg_replace('/<Password>.*?<\/Password>/s', '<Password>***</Password>', $xml);
        log_message('info', 'DigitalDelve GET ORDERS request (sanitized): ' . $safeXml);

        $response = $this->post_request($xml);
        if ($response === false) {
            return false;
        }

        $snippet = substr($response, 0, 2000);
        $snippet = preg_replace('/<Password>.*?<\/Password>/s', '<Password>***</Password>', $snippet);
        log_message('info', 'DigitalDelve GET ORDERS response snippet: ' . $snippet);

        $parsed = $this->parse_orders($response);
        if ($parsed === false) {
            return false;
        }

        $existingLookup = array_flip($existingIds);
        $fresh = [];
        foreach ($parsed as $order) {
            if (isset($existingLookup[$order['order_detail_order_id']])) {
                continue;
            }
            $fresh[] = $order;
            if (count($fresh) >= $this->import_limit) {
                break;
            }
        }

        return $fresh;
    }

    protected function node_text(DOMNode $parent, $tag)
    {
        if (!($parent instanceof DOMElement) && !($parent instanceof DOMDocument)) {
            return '';
        }
        $nodes = $parent->getElementsByTagName($tag);
        if ($nodes->length < 1 || !$nodes->item(0)) {
            return '';
        }

        return trim($nodes->item(0)->nodeValue);
    }

    protected function attr_text(DOMElement $el, $attr)
    {
        return $el->hasAttribute($attr) ? trim($el->getAttribute($attr)) : '';
    }
}
