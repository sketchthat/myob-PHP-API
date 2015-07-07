<?php

namespace MYOB\AccountRightV2;

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
     * The OAuth token URL.
     */
    const API_OAUTH_TOKEN_URL = '';

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
     * The user GUID.
     *
     * @var string
     */
    private $guid;

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

            }

            if(isset($config['apiSecret'])) {

            }

            if(isset($config['apiCallback'])) {

            }
        }
        
        throw new AccountRightV2Exception('Error: __construct() - Configuration data is invalid.');
    }

    /**
     * Generates the OAuth login URL.
     *
     * @return string MYOB OAuth login URL
     *
     * @throws \MYOB\AccountRightV2\AccountRightV2Exception
     */
    public function getLoginUrl() {
        
    }

    private function _makeGetRequest($function) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, CLOUD_ENDPOINT.$this->guid.'/'.$function);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $jsonData = curl_exec($ch);

        curl_close($ch);

        return $jsonData;
    }
}