<?php

namespace Myob\AccountRightV2;

/**
 * AccountRightV2 API class
 *
 * Development Center AccountRight Live API v2: http://developer.myob.com/api/accountright/v2/
 * 
 * THIS CLASS IS NOT IN ANY WAY AFFILIATED WITH MYOB OR ACCOUNTRIGHT
 *
 * @author Michael Kimpton <https://github.com/sketchthat>
 * @version 1.0
 *
 */
class AccountRightV2 {
    /**
     * The API base URL.
     */
    const API_URL = 'https://api.myob.com/accountright/';

    /**
     * The API OAuth URL.
     */
    const API_OAUTH_URL = 'https://secure.myob.com/oauth2/account/authorize';

    /**
     * The API Token Authorize URL.
     */
    const API_TOKEN_URL = 'https://secure.myob.com/oauth2/v1/authorize';

    /**
     * The MYOB API Key.
     *
     * @var string
     */
    private $_apikey;

    /**
     * The MYOB OAuth API secret.
     *
     * @var string
     */
    private $_apisecret;

    /**
     * The callback URL.
     *
     * @var string
     */
    private $_callbackurl;

    /**
     * The user access token.
     *
     * @var string
     */
    private $_accesstoken;


    /**
     * The oauth scope.
     *
     * @var string
     */
    private $_scope;

    /**
     * The CompanyFile GUID.
     *
     * @var string
     */
    private $_guid;

    /**
     * The user Username.
     *
     * @var string
     */
    private $_username;

    /**
     * The user Password.
     *
     * @var string
     */
    private $_password;

    /**
     * The location of a saved object.
     *
     * @var string
     */
    private $_location;

    /**
     *  The API Methods.
     */
    const GET = 0;
    const PUT = 1;
    const POST = 2;
    const DELETE = 3;

    public function __construct($config) {
        if(is_array($config)) {
            if(isset($config['apiKey'])) {
                $this->_apikey = $config['apiKey'];
            }

            if(isset($config['apiSecret'])) {
                $this->_apisecret = $config['apiSecret'];
            }

            if(isset($config['apiCallback'])) {
                $this->_callbackurl = $config['apiCallback'];
            }

            if(isset($config['apiScope'])) {
                $this->_scope = $config['apiScope'];
            } else {
                $this->_scope = 'CompanyFile';
            }

            if(isset($config['username'])) {
                $this->_username = $config['username'];
            }

            if(isset($config['password'])) {
                $this->_password = $config['password'];
            }
        } else {
            throw new \Exception('Error: __construct() - Configuration data is invalid.');    
        }
    }

    /**
     * Generates the OAuth login URL.
     *
     * @return string MYOB OAuth login URL
     */
    public function getLoginUrl() {
        $params = array(
            'client_id' => $this->_apikey,
            'redirect_uri' => $this->_callbackurl,
            'response_type' => 'code',            
            'scope' => $this->_scope
        );

        return self::API_OAUTH_URL.'?'.http_build_query($params);
    }

    /**
     * Gets Access Token from MYOB.
     *
     * @return string MYOB Access Token
     */
    public function getAccessToken($accessToken) {
        $params = array(
            'client_id' => $this->_apikey,
            'client_secret' => $this->_apisecret,
            'scope' => $this->_scope,
            'code' => $accessToken,            
            'redirect_uri' => $this->_callbackurl,
            'grant_type' => 'authorization_code'
        );

        $json = $this->_makeRequest(self::API_TOKEN_URL, $params);

        return $this->saveAccessToken($json);
    }

    /**
     * Refreshes Access Token from MYOB.
     *
     * @return string MYOB Access Token
     */
    public function refreshToken($refreshToken) {
        $params = array(
            'client_id' => $this->_apikey,
            'client_secret' => $this->_apisecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        );

        $json = $this->_makeRequest(self::API_TOKEN_URL, $params);

        return $this->saveAccessToken($json);
    }

    /**
     * Saves the Access Token from MYOB in Sessions
     *
     * @return json Access Token
     */
    public function saveAccessToken($json) {
        if(isset($json->error)) {
            throw new \Exception($json->error);
        } elseif(isset($json->Errors)) {
            throw new \Exception(print_r($json->Errors, true));
        }

        $_SESSION['access_token'] = $json->access_token;
        $_SESSION['refresh'] = $json->refresh_token;

        $date = new \DateTime('NOW');
        $date->add(new \DateInterval('PT'.$json->expires_in.'S'));

        $_SESSION['expires'] = $date;

        $this->retriveAccessToken();

        return $json;
    }

    /**
     * Gets the Access Token for MYOB from Sessions
     *
     * @return bool
     */
    public function retriveAccessToken() {
        if(isset($_SESSION['access_token'])) {
            $currentDate = new \DateTime('NOW');

            if($_SESSION['expires'] < $currentDate) {
                $this->refreshToken($_SESSION['refresh']);
            }

            $this->_accesstoken = $_SESSION['access_token'];

            if(isset($_SESSION['guid'])) {
                $this->_guid = $_SESSION['guid'];
            }

            return true;
        }

        throw new \Exception('Error: retriveAccessToken() - Session invalid.');
    }

    /**
     * Gets the Company File and sets the guid
     *
     * @return bool
     */
    public function getCompanyFile($int) {
        $_SESSION['guid'] = null;
        unset($_SESSION['guid']);

        $this->_guid = '';

        $companyFiles = $this->_makeGetRequest();

        if(isset($companyFiles[$int])) {
            $this->_guid = $companyFiles[0]->Id;
            $_SESSION['guid'] = $this->_guid;

            return true;
        }

        throw new \Exception('Error: getCompanyFile() - Cannot find company file.');
    }

    /**
     * Returns the GUID
     *
     * @return string
     */
    public function getGuid() {
        return $this->_guid;
    }

    /**
     * Makes curl requests to MYOB for core authentication actions
     *
     * @return json object
     */
    private function _makeRequest($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Makes curl requests to MYOB for most actions
     *
     * @return json object or string
     */
    private function _doCurl($function = '', $method = self::GET, $data = array()) {
        $url = 'https://api.myob.com/accountright/';
        $headers = array(
            'Authorization: Bearer '.$this->_accesstoken,
            'x-myobapi-key: '.$this->_apikey,
            'x-myobapi-version: v2',
        );

        if($this->_guid != '') {
            $url.= $this->_guid.'/';

            array_push($headers, 'x-myobapi-cftoken: '.base64_encode($this->_username.':'.$this->_password));
        } 

        $url.= $function;

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if($method == self::POST) {
            curl_setopt($ch, CURLOPT_POST, true);
            
            $data_string = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

            array_push($headers, 'Content-Type: application/json');
            array_push($headers, 'Content-Length: '.strlen($data_string));

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        } elseif($method == self::GET) {
            if(is_array($data) && !empty($data)) {
                $url.='?json='.urlencode(json_encode($data));
            } elseif(!is_array($data) && strlen($data) > 0) {
                $url.='/?'.$data;
            }            
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $jsonData = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($jsonData, 0, $header_size);
        $body = substr($jsonData, $header_size);

        $this->_location = '';

        if(preg_match('/Location: '.str_replace('/', '\/', $url).'\/(([a-zA-Z0-9]{8})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{4})-([a-z0-9]{12}))/i', $header, $pregs)) {
            if(isset($pregs[1])) {
                $this->_location = $pregs[1];
            }
        }

        
        curl_close($ch);

        if($this->isJson($body)) {
            $json = json_decode($body);

            return $json;
        } else {
            return $body;
        }
    }

    /**
     * Makes GET curl requests to MYOB for most actions
     *
     * @return json object or string
     */
    private function _makeGetRequest($function = '', $data = array()) {
        return $this->_doCurl($function, self::GET, $data);
    }

    /**
     * Makes POST curl requests to MYOB for most actions
     *
     * @return json object or string
     */
    private function _makePostRequest($function = '', $data) {
        return $this->_doCurl($function, self::POST, $data);
    }

    /**
     * Checks if a string is JSON or something else
     *
     * @return bool
     */
    private function isJson($string) {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     *  @return string
     */ 
    public function getLocation() {
        return $this->_location;
    }

    /**
     *  Returns company information details for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/company/
     *  
     *  @return json      
     */
    public function Company() {
        return $this->_makeGetRequest('Company');
    }

    /**
     *  Return all contact types for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/contact/
     *  
     *  @return json      
     */
    public function Contact($data = array()) {
        return $this->_makeGetRequest('Contact', $data);
    }

    /**
     *  Return, update, create and delete a customer contact for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/contact/customer/
     *  
     *  @return json      
     */
    public function ContactCustomer($json = array()) {
        if(!empty($json)) {
            return $this->_makePostRequest('Contact/Customer', $json);    
        } else {
            return $this->_makeGetRequest('Contact/Customer');    
        }        
    }

    /**
     *  Return all sale invoice types for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/
     *  
     *  @return json      
     */
    public function SaleInvoice() {
        return $this->_makeGetRequest('Sale/Invoice');
    }

    /**
     *  Returns all item type sale invoices for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/invoice_item/
     *  
     *  @return json      
     */
    public function GetSaleInvoiceItem($json) {
        return $this->_makeGetRequest('Sale/Invoice/Item', $json);
    }

    /**
     *  Returns all item type sale invoices for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/invoice_item/
     *  
     *  @return json      
     */
    public function PostSaleInvoiceItem($json) {
        return $this->_makePostRequest('Sale/Invoice/Item', $json);
    }

    /**
     *  Returns item type sale invoices for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/
     *  
     *  @return json      
     */
    public function SaveInvoiceItemGet($uid) {
        $json = array('UID' => $uid);

        return $this->_makeGetRequest('Sale/Invoice/Item', $json);
    }
}