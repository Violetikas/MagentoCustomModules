<?php

namespace Violeta\CustomShipping\API;

use Zend\Http\Client;
use Zend\Http\Exception\RuntimeException;
use Zend\Http\Request;
use Zend\Http\Response;

class CurrencyRatesApiData
{
    const API_PATH = 'https://restcountries.eu/rest/v1/alpha/';
    protected $zendClient;

    /**
     * CustomShippingApiData constructor.
     * @param Client $zendClient
     */
    public function __construct(
        Client $zendClient
    ) {
        $this->zendClient = $zendClient;
    }

    public function getCurrencyData($countryCode)
    {
        $response = '';
        try {
            $this->zendClient->setUri(self::API_PATH . $countryCode);
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
        $result = $response['currencies'];
        return $result[0];
    }
}
