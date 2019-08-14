<?php

namespace Violeta\CustomShippingModule\Api;

use Violeta\CustomShippingModule\Helper\Data;
use Zend\Http\Client;
use Zend\Http\Exception\RuntimeException;
use Zend\Http\Headers;
use Zend\Http\Request;

class CustomShippingApiData
{
    protected $zendClient;
    private $data;

    public function __construct(
        Client $zendClient,
        Data $data
    ) {
        $this->zendClient = $zendClient;
        $this->data = $data;
    }

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

    public function placeOrder(array $data)
    {
        return ['id' => 15];
        // TODO: restore actual
        return $this->processApiRequest(
            'https://5d317bb345e2b00014d93f1c.mockapi.io/' .
            'store1',
            'POST',
            $data
        );
    }

    public function updateOrder($apiOrderId, array $data)
    {
        return $this->processApiRequest(
            'https://5d317bb345e2b00014d93f1c.mockapi.io/' .
            'store1' . '/' . $apiOrderId,
            'PUT',
            $data
        );
    }

    public function deleteOrder($apiOrderId)
    {
        return $this->processApiRequest(
            'https://5d317bb345e2b00014d93f1c.mockapi.io/' .
            'store1' . '/' . $apiOrderId,
            'DELETE'
        );
    }

    private function processApiRequest(
        string $uri,
        string $method,
        array $body = []
    ) {
        $init = curl_init();
        $options = [
            CURLOPT_URL => $uri,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => (!empty($body) ? json_encode($body) : ''),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-type: application/json']
        ];

        curl_setopt_array($init, $options);
        $result = curl_exec($init);
        return json_decode($result, true);
    }
}