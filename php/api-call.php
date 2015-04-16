<?php

include_once 'Api.php';

session_start();
/**
 * This example illustrates how to call the API with very basic CURL calls wrapped in a class.
 * Tested on php5.5.10, apache2.2 ubuntu 12.04
 * @company: Autodata Ltd, UK
 * @author: jakub.wrona@autodata-group.com
 * @date: 2014-04-09 
 */

/**
 * Initialise the api and get a connection.
 * 
 * Here init() takes two paremeters, a client_id and a client_secret.
 * $api is the object to use for all calls from this point on.
 */
$api = Api::getInstance()->init('put your client id string here', 'put your client secret string here');

/**
 * Example 1
 * 
 * Retrieve a list of manufacturers.
 */
$api->setEndpoint('manufacturers');
$response = $api->requestGet(['country-code' => 'gb']);
if ($response !== false) {
    $manufactures = $response->getResponseBody();
    echo 'Example 1, response manufacturers: <br/>';
    echo '<pre>'.print_r($manufactures, 1).'</pre>';
    echo '<hr />';
}

/**
 * Example 2
 * 
 * Retrieve a list of Service Schedules
 */
$api->setEndpoint('vehicles/AUD00528/service-schedules');
$response = $api->requestGet(array('language' => 'en-gb','country-code' => 'gb'));
if ($response !== false) {
    $schedules = $response->getResponseBody();
    echo 'Example 2, response service schedules: <br/>';
    echo '<pre>'.print_r($schedules, 1).'</pre>';
    echo '<hr />';
}

/**
 * Example 3
 * 
 * Retrieve a specific Service Schedule
 */
$api->setEndpoint('vehicles/AUD00528/service-schedules/'.$schedules->data[0]->service_schedule_id);
$response = $api->requestGet(array('language' => 'en-gb','country-code' => 'gb'));
if ($response !== false) {
    $operations = $response->getResponseBody();
    echo 'Example 3, response service schedules: <br/>';
    echo '<pre>'.print_r($operations, 1).'</pre>';
    echo '<hr />';
}

/**
 * Example 4
 * 
 * Show how an error is returned.  Here we fail to pass in the required country-code
 * so an error is returned.  This means that the response returned is false.
 * To get more information about what happened, get the last response from the
 * api object and then get the response header.
 */
$api->setEndpoint('vehicles/AUD00528/service-schedules');
$response = $api->requestGet();
if ($response !== false) {
    echo "OK\n";
} else {
    $response = $api->getLastResponse();
    //we should get a 400 Bad Request here because the country-code is a required param
    echo 'Example 4, an error<br />';
    echo explode("\n", $response->getResponseHeader())[0];

    //we have additional info about the error
    echo '<pre>'.print_r($response->getResponseBody()->info, 1).'</pre>';
    echo '<hr />';
}

/**
 * Example 5
 * 
 * Try a POST.
 * This is not currently supported and will return an error, so just make sure
 * that's what it is doing.
 */
$api->setEndpoint('service-schedules');
$response = $api->requestPost(array('language' => 'en-gb','country-code' => 'gb'));
if ($response !== false) {
    echo "OK\n";
} else {
    $response = $api->getLastResponse();
    //we should get a 401
    echo 'Example 5, an error<br />';
    echo explode("\n", $response->getResponseHeader())[0];
    echo '<hr />';
}
