<?php

/**
 * Class ApiResponse
 * 
 * This is returned from a call to the api.  It contains all the data and information
 * about the result of the call.
 */
class ApiResponse {
    protected $rawResponse;
    protected $responseHeader;
    protected $responseBody;

    /**
     * Used by the Api class to create a new response.
     * 
     * @param string $rawResponse
     * @param resource $curlResource
     */
    public function __construct($rawResponse, $curlResource) {
        $this->rawResponse = $rawResponse;
        $headerSize = curl_getinfo($curlResource, CURLINFO_HEADER_SIZE);
        $this->responseHeader = substr($this->rawResponse, 0, $headerSize);
        $this->responseBody = substr($this->rawResponse, $headerSize);
    }

    /**
     * Method to retrieve the response body
     * 
     * @return mixed $responseBody
     */
    public function getResponseBody() {
        return json_decode($this->responseBody);
    }

    /**
     * Method to retrieve the response header
     * 
     * @return mixed $responseHeader
     */
    public function getResponseHeader() {
        return $this->responseHeader;
    }

    /**
     * Method to retrieve the raw response from the server
     * 
     * @return mixed $rawResponse
     */
    public function getRawResponse() {
        return $this->rawResponse;
    }
}
