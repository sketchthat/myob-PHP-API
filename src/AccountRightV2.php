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

        return $this->saveAccessToken(json_decode($json));
    }

    public function refreshToken($refreshToken) {
        $params = array(
            'client_id' => $this->_apikey,
            'client_secret' => $this->_apisecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        );

        $json = $this->_makeRequest(self::API_TOKEN_URL, $params);

        return $this->saveAccessToken(json_decode($json));
    }

    public function saveAccessToken($json) {
        $_SESSION['access_token'] = $json->access_token;
        $_SESSION['refresh'] = $json->refresh_token;

        $date = new \DateTime('NOW');
        $date->add(new \DateInterval('PT'.$json->expires_in.'S'));

        $_SESSION['expires'] = $date;

        $this->retriveAccessToken();

        return $json;
    }

    public function retriveAccessToken() {
        if(isset($_SESSION['access_token']) && isset($_SESSION['uid'])) {
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

    public function getGuid() {
        return $this->_guid;
    }

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

        return $response;
    }

    private function _makeGetRequest($function = '') {
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
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $jsonData = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($jsonData);

        return $json;
    }

    /** Sale **/
    public function SaleCustomerPayment() {
        
    }

    public function SaleCustomerPaymentCalculateDiscountsFees() {
        
    }

    public function SaleCustomerPaymentRecordWithDiscountsAndFees() {
        
    }

    public function SaleCreditRefund() {
        
    }

    public function SaleCreditSettlement() {
        
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
    public function Contact() {
        return $this->_makeGetRequest('Contact');
    }

    /**
     *  Return, update, create and delete a customer contact for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/contact/customer/
     *  
     *  @return json      
     */
    public function ContactCustomer() {
        return $this->_makeGetRequest('Contact/Customer');
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
     *  Return, update, create and delete item type sale invoices for an AccountRight company file
     *  http://developer.myob.com/api/accountright/v2/sale/invoice/invoice_item/
     *
     *  @return json
     */
    public function SaleInvoiceItem($method = self::POST, $uid, $date, $customer, $lines, $rowVersion = '') {
        //GET
        //PUT
        //POST
        //DELETE
        $this->_makeGetRequest('Sale/Invoice/Item');
    }

    public function SaleInvoiceService() {
        
    }

    public function SaleInvoiceProfessional() {
        
    }

    public function SaleInvoiceTimeBilling() {
        
    }

    public function SaleInvoiceMiscellaneous() {
        
    }

    public function SaleInvoiceRenderAsPDF() {
        
    }

    public function SaleOrder() {
        
    }

    public function SaleOrderItem() {
        
    }

    public function SaleOrderService() {
        
    }

    public function SaleOrderProfessional() {
        
    }

    public function SaleOrderTimeBilling() {
        
    }

    public function SaleOrderMiscellaneous() {
        
    }

    public function SaleOrderRenderAsPDF() {

    }
}