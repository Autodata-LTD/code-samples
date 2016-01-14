/*************************************************************
Copyright (C) Autodata Limited 1974-2015.
All Rights Reserved.
NOTICE: Code example provide to developers wishing to integrate with the
Autodata API. Only to be use under an NDA or valid contract with Autodata.
Autodata are not liable for this code being re-used or modified.
*/
<?php
//initialize session
session_start();

/**
 * Class OAUTH2Connection
 */
class OAUTH2Connection
{
    //URL to get access token
    const OAUTH2_ACCESS_TOKEN_URL = "https://account.autodata-group.com/oauth/access_token";

    //please put here your OAUTH2 client ID and client secret
    const OAUTH2_ACCESS_TOKEN_CLIENT_ID = "";
    const OAUTH2_ACCESS_TOKEN_CLIENT_SECRET = "";

    //other parameters required to get OAUTH2 access token
    const OAUTH2_ACCESS_TOKEN_GRANT_TYPE = "client_credentials";
    const OAUTH2_ACCESS_TOKEN_SCOPE = "scope1";
    const OAUTH2_ACCESS_TOKEN_STATE = "123456789";

    //base URL of the API call
    const API_CALL_BASE_URL = "http://api.autodata-group.com/";

    //request URL
    private $requestPath = null;

    //response
    private $response = null;

    //country code
    private $countryCode = null;

    //language code
    private $languageCode = null;

    //property for storing class instance
    private static $instance = null;

    //'not authorized' error code
    protected static $notAuthorizedErrorCode = 'M03140300';

    /**
     * Private constructor needed for singleton implementation
     *
     */
    private function __construct()
    {
    }

    /**
     * Singleton
     *
     * @return null|OAUTH2Connection
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new OAUTH2Connection();
        }

        return self::$instance;
    }


    /**
     * Get OAUTH2 access token using CURL library. If $forceNew is set to true do not get access token from session even if it is not expired
     *
     * @param $forceNew
     * @return $accessToken
     */
    public function getAccessToken($forceNew = false)
    {
        //get access token from session if it is not expired
        if (!$forceNew) {
            if (isset($_SESSION['access_token_expiration_time']) && ($_SESSION['access_token_expiration_time'] >= time())) {
                if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
                    return $_SESSION['access_token'];
                }
            }
        }

        //parameters needed to be sent to get access token
        $clientTokenPostData = array(
            "grant_type"    => self::OAUTH2_ACCESS_TOKEN_GRANT_TYPE,
            "client_id"     => self::OAUTH2_ACCESS_TOKEN_CLIENT_ID,
            "client_secret" => self::OAUTH2_ACCESS_TOKEN_CLIENT_SECRET,
            "scope"         => self::OAUTH2_ACCESS_TOKEN_SCOPE,
            "state"         => self::OAUTH2_ACCESS_TOKEN_STATE
        );

        //send request to get access token
        $curl = curl_init(self::OAUTH2_ACCESS_TOKEN_URL);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $clientTokenPostData);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $jsonResponse = curl_exec($curl);

        curl_close($curl);

        //decode JSON data returned from server
        $response = json_decode($jsonResponse, true);

        if (isset($response['access_token']) && !empty($response['access_token'])) {
            $accessToken = $response['access_token'];

            //store access token and its expiration time to session for later use. Use "expire_in" field from response to calculate the expiration time
            $_SESSION['access_token']                 = $accessToken;
            $_SESSION['access_token_expiration_time'] = time() + $response['expires_in'];

            return $accessToken;
        } else {
            throw new Exception($jsonResponse);
        }
    }

    /**
     * Set request path
     *
     * @param $path
     */
    public function setRequestPath($path)
    {
        $this->requestPath = $path;
    }

    /**
     * Get request path
     *
     * @return $requestPath
     * @throws Exception
     */
    public function getRequestPath()
    {
        if (!is_null($this->requestPath)) {
            return $this->requestPath;
        }

        throw new Exception('Request path has not been specified');
    }

    /**
     * Get request URL
     *
     * @return $requestUrl
     */
    public function getRequestUrl()
    {
        return self::API_CALL_BASE_URL . $this->getRequestPath() . '?country-code=' . $this->getCountryCode();
    }

    /**
     * Set language code
     *
     * @param $languageCode
     */
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;
    }

    /**
     * Get language code
     *
     * @return $languageCode
     * @throws Exception
     */
    public function getLanguageCode()
    {
        if (!is_null($this->languageCode)) {
            return $this->languageCode;
        }

        throw new Exception('Language code has not been specified');
    }

    /**
     * Set country code
     *
     * @param $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Get country code
     *
     * @return $countryCode
     * @throws Exception
     */
    public function getCountryCode()
    {
        if (!is_null($this->countryCode)) {
            return $this->countryCode;
        }

        throw new Exception('Country code has not been specified');
    }

    /**
     * Send OAUTH2 request using CURL library
     *
     * @param $accessToken
     */
    public function sendRequest($accessToken = null)
    {
        //get request URL and language code
        $requestUrl   = $this->getRequestUrl();
        $languageCode = $this->getLanguageCode();

        if (!is_null($accessToken)) {

            //send request to get data from server
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Accept-Language: $languageCode",
                "Authorization: Bearer $accessToken"    //passing the access token header to the request
            ));

            $response       = curl_exec($ch);
            $this->response = json_decode($response, true);

            curl_close($ch);
        } else {
            throw new Exception('Access token has not been specified');
        }
    }

    /**
     * Get response
     *
     * @return $response
     */
    public function getResponse()
    {
        if (!is_null($this->response)) {

            //if error code M03140300 is returned then new access token is fetched and one more request is sent to server using the new access token
            if (isset($this->response['code']) && $this->response['code'] == self::$notAuthorizedErrorCode) {
                $accessToken = $this->getAccessToken(true); //force new access token
                $this->sendRequest($accessToken);
            }

            return $this->response;
        }

        return null;
    }

    /**
     * Clear access token session variables
     *
     */
    public function clearAccessTokenSession()
    {
        if (isset($_SESSION['access_token']) && isset($_SESSION['access_token_expiration_time'])) {
            unset($_SESSION['access_token']);
            unset($_SESSION['access_token_expiration_time']);
        }
    }
}

//example calls
try {
    //get object instance
    $oauth2Connection = OAUTH2Connection::getInstance();

    //set country code and language code
    $oauth2Connection->setCountryCode('gb');
    $oauth2Connection->setLanguageCode('en-gb');

    //get access token
    $accessToken = $oauth2Connection->getAccessToken();

    //first example: vehicle data
    echo '<h2>First example: Vehicle data</h2>';
    $oauth2Connection->setRequestPath("v1/vehicles/AUD00528");
    $oauth2Connection->sendRequest($accessToken);

    //print response
    echo '<pre>';
    print_r($oauth2Connection->getResponse());
    echo '</pre>';


    echo '<hr>';

    //second example: manufacturers data
    echo '<h2>Second example: Manufacturers data</h2>';
    $oauth2Connection->setRequestPath("v1/manufacturers");
    $oauth2Connection->sendRequest($accessToken);

    //print response
    echo '<pre>';
    print_r($oauth2Connection->getResponse());
    echo '</pre>';


} catch (Exception $e) {
    //print exception
    echo $e->getMessage();
}
