<?php

namespace Violeta\CustomShipping\API;

use Violeta\CustomShipping\Helper\Data;
use Zend\Http\Client;
use Zend\Http\Exception\RuntimeException;
use Zend\Http\Request;
use Zend\Http\Response;


class CustomShippingApiData
{
    const API_PATH_GET_TOKEN = 'https://5d317bb345e2b00014d93f1c.mockapi.io/auth/658764298';
    const API_PATH_GET_DATA = 'https://5d317bb345e2b00014d93f1c.mockapi.io/';
    protected $zendClient;
    private $data;


    /**
     * CustomShippingApiData constructor.
     * @param Client $zendClient
     * @param Data $data
     */
    public function __construct(
        Client $zendClient,
        Data $data
    ) {
        $this->zendClient = $zendClient;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        $response = '';
        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($this->data->getConfigValue('apiurl'));
            $this->zendClient->setMethod(Request::METHOD_GET);
            $this->zendClient->setParameterGet([
                'content-type' => 'application/json',
            ]);
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();
        } catch (RuntimeException $runtimeException) {
            echo $runtimeException->getMessage();
        }
        $response = json_decode($response->getBody(), true);
        return $response['authToken'];
    }

    /**
     * @param $countryCode
     * @return array|mixed|Response
     */
    public function getApiData($countryCode)
    {
        $response = [];
        try {
            $apiToken = $this->getApiToken();
            $this->zendClient->reset();
            $this->zendClient->setUri(self::API_PATH_GET_DATA  . $apiToken . '/' . $countryCode);
            $this->zendClient->setMethod(Request::METHOD_GET);
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();
        } catch (RuntimeException $runtimeException) {
            echo $runtimeException->getMessage();
        }
        $response = json_decode($response->getBody(), true);
        return $response;
    }
}
