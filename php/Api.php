/*************************************************************
Copyright (C) Autodata Limited 1974-2015.
All Rights Reserved.
NOTICE: Code example provide to developers wishing to integrate with the
Autodata API. Only to be use under an NDA or valid contract with Autodata.
Autodata are not liable for this code being re-used or modified.
*/
<?php

include_once "ApiResponse.php";

/**
 * Class Api
 * 
 * This is the main class used form accessing the api.  It is a singleton class
 * which means that only one instance of the api will exist whilst it is being
 * used so that authentication happens only once.
 */
class Api {
    protected $baseUrl = 'http://api.autodata-group.com/';
    protected $tokenUrl = 'https://account.autodata-group.com/oauth/access_token';
    protected $headers = array('Accept-Language: en-gb;q=0.8,en;q=0.7,fr-fr;q=0.4');
    protected $version;
    protected $endpoint = '';
    protected $accessToken = '';
    protected $grantType = '';
    protected $clientId = '';
    protected $clientSecret = '';
    protected $scope = '';
    protected $state = '';
    protected $lastResponse;

    /**
     * Singleton
     */
    private function __construct() {}

    /**
     * Create the Api object or, if it already exists, return it.
     * 
     * @return Api
     */
    public static function getInstance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    /**
     * This is the main initialisation method for the singleton.
     * The only two parameters that need to be passed in are the clientId and
     * clientSecret.
     * 
     * This only needs to be called once.
     * 
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @param string $scope
     * @param string $state
     * @return \Api
     */
    public function init($clientId, $clientSecret, $grantType='client_credentials', $scope='scope1', $state='123456789') {
        $this->grantType = $grantType;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
        $this->state = $state;
        return $this;
    }

    /**
     * Method to set the endpoint that the query is to be made on.
     * 
     * @param string $endpoint the actual endpoint 
     * @param string $version the version of the api you wish to use
     */
    public function setEndpoint($endpoint, $version = 'v1') {
        $this->endpoint = $endpoint;
        $this->version = $version;
    }

    /**
     * Method to retrieve the last response that was received from the api.
     * Useful should a call fail, return 'false' and you want to get more
     * information about the failure.
     * 
     * @return ApiResponse $response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Method to authenticate with a api server and retrieve the token
     * needed for further operations.
     * 
     * @return string access_token
     */
    private function getToken(){
        if(isset($_SESSION['valid']) && $_SESSION['valid'] > time()){
            return $_SESSION['token'];
        } else {
            $params = array();
            $params['grant_type'] = $this->grantType;
            $params['client_id'] = $this->clientId;
            $params['client_secret'] = $this->clientSecret;
            $params['scope'] = $this->scope;
            $params['state'] = $this->state;

            $qS = $this->queryString($params);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $qS);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            $result = curl_exec($ch);
            $this->lastResponse = new ApiResponse($result, $ch);
            curl_close($ch);

            $responseBody = $this->lastResponse->getResponseBody();
            $_SESSION['valid'] = $responseBody->expires_in + time();
            $_SESSION['token'] = $responseBody->access_token;
            return $responseBody->access_token;
        }
    }

    /**
     * Method to execute a GET request on the api
     * 
     * @param array $params additional parameters to the call
     * @return ApiResponse $response
     */
    public function requestGet($params = array()) {
        $params['access_token'] = $this->getToken();
        $qS = $this->queryString($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.'/'.$this->version.'/'.$this->endpoint.'?'.$qS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $this->lastResponse = new ApiResponse($result, $ch);
        curl_close($ch);

        if (strstr($this->lastResponse->getResponseHeader(), '200 OK')) {
            return $this->lastResponse;
        } else {
            return false;
        }
    }

    /**
     * Method to execute a POST request on the api
     * 
     * @param array $params additional parameters to the call
     * @return ApiResponse $response
     */
    public function requestPost($params = array()) {
        $params['access_token'] = $this->getToken();
        $qS = $this->queryString($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.'/'.$this->version.'/'.$this->endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $this->lastResponse = new ApiResponse($result, $ch);
        curl_close($ch);

        if (strstr($this->lastResponse->getResponseHeader(), '200 OK')) {
            return $this->lastResponse;
        } else {
            return false;
        }
    }

    /**
     * Method to execute a PUT request on the api
     * 
     * @param $id the id of the resource you want to PUT to
     * @param array $params additional parameters to the call
     * @return ApiResponse $response
     */
    public function requestPut($id, $params = array()) {
        $params['access_token'] = $this->getToken();
        $qS = $this->queryString($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl.'/'.$this->version.'/'.$this->endpoint.'/'.$id);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        $this->lastResponse = new ApiResponse($result, $ch);
        curl_close($ch);

        if (strstr($this->lastResponse->getResponseHeader(), '200 OK')) {
            return $this->lastResponse;
        } else {
            return false;
        }
    }

    /**
     * Helper method to create a http query string.
     * 
     * @param type $params
     * @return string
     */
    protected function queryString($params) {
        return http_build_query($params);
    }
}

