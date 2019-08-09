<?php

namespace Violeta\CustomShipping\API;

use Violeta\CustomShipping\Helper\Data;
use Zend\Http\Client;
use Zend\Http\Exception\RuntimeException;
use Zend\Http\Request;

class CustomShippingApiData
{
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
        $apiUri = $this->data->getConfigValue('apiurl');
        $apiUserId = $this->data->getConfigValue('apiuserid');
        $response = '';
        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($apiUri . $apiUserId);
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

    public function getApiData($countryCode)
    {
        $apiUriForData = $this->data->getConfigValue('apiurlfordata');
        $response = [];
        try {
            $apiToken = $this->getApiToken();
            $this->zendClient->reset();
            $this->zendClient->setUri($apiUriForData . $apiToken . '/' . $countryCode);
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
