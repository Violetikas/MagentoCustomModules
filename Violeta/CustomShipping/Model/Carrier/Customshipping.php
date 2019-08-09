<?php

namespace Violeta\CustomShipping\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Violeta\CustomShipping\API\CurrencyRatesApiData;
use Violeta\CustomShipping\API\CustomShippingApiData;
use Violeta\CustomShipping\Helper\CurrencyData;
use Violeta\CustomShipping\Helper\MappingHelper;

class Customshipping extends AbstractCarrier implements CarrierInterface
{
    protected $_code = 'customshipping';
    protected $_isFixed = true;
    private $rateResultFactory;
    private $rateMethodFactory;
    private $apiData;
    private $country;
    private $currency;
    private $currencyFactory;
    private $storeManager;
    private $helper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        CustomShippingApiData $apiData,
        CurrencyRatesApiData $currency,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        MappingHelper $helper
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->apiData = $apiData;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->helper = $helper;
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $this->country = $request->getDestCountryId();
        $dataByCountry = $this->apiData->getApiData($this->country);
        $result = $this->rateResultFactory->create();
        $method = $this->rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->helper->formatName($dataByCountry['carierName'], 'carrier'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->helper->formatName($dataByCountry['methodName'], 'method'));
        $shippingCost = (float)$dataByCountry['price'];
        $convertedPrice = $this->convertPrice($shippingCost, $this->country);
        $method->setPrice($convertedPrice);
        $method->setCost($convertedPrice);
        $result->append($method);
        return $result;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    private function convertPrice($amount, $countryCode)
    {
        $currency = $this->currency->getCurrencyData($countryCode);
        $rate = $this->currencyFactory->create();
        $rate = $rate
            ->load($currency)
            ->getAnyRate(
                $this->storeManager->getStore()->getCurrentCurrencyCode()
            );
        return round(($amount * $rate), 0);
    }

    private function makeNameHumanReadable($name)
    {
        $result = preg_replace('/[\W-_]/', ' ', $name);
        return $result;
    }
}
