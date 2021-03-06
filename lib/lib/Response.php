<?php

/**
 * Parses the response from a Curl request into an object containing
 * the response body and an associative array of headers
**/
class Response {

    /**
     * The body of the response without the headers block
     *
     * @var string
    **/
    public $body = '';

    /**
     * An associative array containing the response's headers
     *
     * @var array
    **/
    public $headers = array();

    /**
     * Accepts the result of a curl request as a string
     *
     * @param string $response
    **/
    function __construct($response) {
        # Headers regex
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

        # Extract headers from response
        preg_match_all($pattern, $response, $matches);

        $headers_string = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

        # Remove headers from the response body

        $this->body = str_replace($headers_string, '', $response);

        if(isset($matches[0])){
            $_headers_string = array_pop($matches[0]);
            $this->body = str_replace($_headers_string, '', $this->body);
        }
        # Extract the version and status from the first header
        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->headers['httpVersion'] = $matches[1];
        $this->headers['statusCode'] = $matches[2];
        $this->headers['status'] = $matches[2].' '.$matches[3];

        # Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
    }

    /**
     * Returns the response body as string
     *
     * @return string
    **/
    function __toString() {
        return $this->body;
    }
    function decode() {
        return json_decode($this->body, true);
    }
}
