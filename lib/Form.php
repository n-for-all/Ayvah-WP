<?php

require_once 'lib/Request.php';
require_once 'lib/Response.php';

class Form
{
    /** @var Request $client */
    protected $client;
    protected $apiKey;
    // protected $endpoint = 'https://setback.io/form/%domain%/%id%/%locale%/';
    protected $endpoint = 'https://setback.io/form/%domain%/%id%/%locale%/';
    protected $headers = [];

    public function __construct($domain, $id, $locale)
    {

        $this->endpoint = preg_replace('/%domain%/', $domain, $this->endpoint);
        $this->endpoint = preg_replace('/%id%/', $id, $this->endpoint);
        $this->endpoint = preg_replace('/%locale%/', $locale, $this->endpoint);

        $this->headers = [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/x-www-form-urlencoded' //to be update to support file boundaries curl
        ];

        $this->client = new Request($this->headers);
    }

    public function get($uri = '', $args = [], $timeout = 10)
    {
        return $this->call('get', $uri, $args, $timeout);
    }

    public function post($uri = '', $args = [], $timeout = 10)
    {
        return $this->call('post', $uri, $args, $timeout);
    }

    public function patch($uri = '', $args = [], $timeout = 10)
    {
        return $this->call('patch', $uri, $args, $timeout);
    }

    public function put($uri = '', $args = [], $timeout = 10)
    {
        return $this->call('put', $uri, $args, $timeout);
    }

    public function delete($uri = '', $args = [], $timeout = 10)
    {
        return $this->call('delete', $uri, $args, $timeout);
    }

    public function call($type = 'get', $uri = '', $args = [], $timeout = 10)
    {
        $response = null;

        try {
            switch ($type) {
                case 'post':
                    $response = $this->client->request('POST', $uri, $args);
                    break;

                case 'patch':
                    $response = $this->client->request('PATCH', $uri, [
                        'body' => json_encode($args),
                        'timeout' => $timeout,
                        'headers' => $this->headers,
                    ]);
                    break;

                case 'put':
                    $response = $this->client->request('PUT', $uri, [
                        'query' => $args,
                        'timeout' => $timeout,
                        'headers' => $this->headers,
                    ]);
                    break;

                case 'delete':
                    $response = $this->client->request('DELETE', $uri, [
                        'query' => $args,
                        'timeout' => $timeout,
                        'headers' => $this->headers,
                    ]);
                    break;

                case 'get':
                default:
                    $response = $this->client->request('GET', $uri, [
                        'query' => $args,
                        'timeout' => $timeout,
                        'headers' => $this->headers,
                    ]);
                    break;
            }

            return $response;
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }
    public function submit($fields)
    {
        $response = $this->post($this->endpoint, $fields);
        $output = $response->decode();
        return $output;
    }
}

 ?>
