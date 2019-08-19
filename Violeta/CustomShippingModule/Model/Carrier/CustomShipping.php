<?php

namespace Violeta\CustomShippingModule\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Violeta\CustomShippingModule\Api\CustomShippingApiClient;
use Violeta\CustomShippingModule\Helper\MappingHelper;

class Customshipping extends AbstractCarrier implements CarrierInterface
{
    protected $_code = 'customshipping';
    protected $_isFixed = true;
    private $rateResultFactory;
    private $rateMethodFactory;
    private $apiData;
    private $country;
    private $currencyFactory;
    private $storeManager;
    private $helper;
    private $objectManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        CustomShippingApiClient $apiData,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        MappingHelper $helper
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->apiData = $apiData;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->helper = $helper;
    }

    public function collectRates(RateRequest $request)
    {
        $freeShippingEnabled = $request->getFreeShipping();
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

        $shippingPrice = (float)$dataByCountry['price'];
        $shippingCost = 0;

        if (!$freeShippingEnabled && !$this->isFreeShippingEnabled($dataByCountry['methodName'])) {
            $shippingCost = $this->convertPrice($shippingPrice, $this->country);
        }
        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);
        $result->append($method);
        return $result;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    private function convertPrice($amount, $countryCode)
    {
        $countryData = $this->apiData->getApiData($countryCode);
        $currency = $countryData['currency'];
        $rate = $this->currencyFactory->create();
        $rate = $rate
            ->load($currency)
            ->getAnyRate(
                $this->storeManager->getStore()->getCurrentCurrencyCode()
            );
        return round(($amount * $rate), 0);
    }

    private function isFreeShippingEnabled($shippingMethod)
    {
        $table = json_decode($this->helper->getMethodMapping(), true);
        foreach ($table as $id => $data) {
            if ($data['default_name'] === $shippingMethod) {
                return $data['free_shipping_applicable'] === '1';
            }
        }
        return true;
    }
}
