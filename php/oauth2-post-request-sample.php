
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

//please put here your OAUTH2 client ID and client secret
define("OAUTH2_AUTHENTICATION_CLIENT_ID", "");
define("OAUTH2_AUTHENTICATION_CLIENT_SECRET", "");

/**
 * Get OAUTH2 access token using CURL library. If $forceNew is set to true do not get access token from session even if it is not expired
 *
 * @return $accessToken
 */
function getOauth2AccessToken($forceNew = false)
{
    //get access token from session if it is not expired
    if (!$forceNew) {
        if (isset($_SESSION['access_token_expiration_time']) && ($_SESSION['access_token_expiration_time'] >= time())) {
            if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
                return $_SESSION['access_token'];
            }
        }
    }

    //url to get access token
    $oauth2TokenUrl = "https://account.autodata-group.com/oauth/access_token";

    //params needed to be sent to get access token
    $clientTokenPostData = array(
        "grant_type"    => "client_credentials",
        "client_id"     => OAUTH2_AUTHENTICATION_CLIENT_ID,
        "client_secret" => OAUTH2_AUTHENTICATION_CLIENT_SECRET,
        "scope"         => "scope1",
        "state"         => "123456789"
    );

    //send request to get access token
    $curl = curl_init($oauth2TokenUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $clientTokenPostData);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $jsonResponse = curl_exec($curl);

    curl_close($curl);

    //decode JSON data returned from server
    $authObj = json_decode($jsonResponse, true);

    if (isset($authObj['access_token']) && !empty($authObj['access_token'])) {
        $accessToken = $authObj['access_token'];

        //store access token and its expiration time to session for later use. Use "expire_in" field from response to calculate the expiration time
        $_SESSION['access_token']                 = $accessToken;
        $_SESSION['access_token_expiration_time'] = time() + $authObj['expires_in'];

        return $accessToken;
    } else {
        //display error response
        var_dump($authObj);
        exit;
    }
}

/**
 * Send OAUTH2 request using CURL library
 *
 * @param $url
 * @param $accessToken
 * @return $result
 */
function sendOauth2Request($url, $accessToken)
{
    //send request to get data from server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Language: en-gb",
        "Authorization: Bearer $accessToken"    //passing the access token header to the request
    ));

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

/**
 * Get data from URL using OAUTH2 access token
 *
 * @param $url
 * @param $accessToken
 *
 * @return $data
 */
function getDataFromUrl($url, $accessToken)
{
    //send OAUTH2 request
    $data = json_decode(sendOauth2Request($url, $accessToken), true);

    //if error code M03140300 is returned then new access token is fetched then one more request is sent to server using the new access token
    if (isset($data['code']) && $data['code'] == 'M03140300') {
        $accessToken = getOauth2AccessToken(true);
        $data        = json_decode(sendOauth2Request($url, $accessToken), true);
    }

    return $data;
}

/**
 * Clear access token session variables
 *
 */
function clearAccessTokenSession()
{
    if (isset($_SESSION['access_token']) && isset($_SESSION['access_token_expiration_time']))
    {
        unset($_SESSION['access_token']);
        unset($_SESSION['access_token_expiration_time']);
    }
}

//get vehicle data from API
$accessToken = getOauth2AccessToken();
$result      = getDataFromUrl("http://api.autodata-group.com/v1/vehicles/AUD00528?country-code=gb", $accessToken);

//print response
echo '<pre>';
print_r($result);
