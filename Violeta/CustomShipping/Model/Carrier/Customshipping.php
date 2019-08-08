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

/**
 * Custom shipping model
 */
class Customshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customshipping';
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;
    private $rateMethodFactory;
    /**
     * @var CustomShippingApiData
     */
    private $apiData;
    /**
     * @var string
     */
    private $country;
    private $currency;
    private $currencyFactory;
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param CustomShippingApiData $apiData
     * @param CurrencyData $helper
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param array $data
     */
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
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->apiData = $apiData;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $this->country = $request->getDestCountryId();

        $dataByCountry = $this->apiData->getApiData($this->country);

        /** @var Result $result */
        $result = $this->rateResultFactory->create();

        /** @var Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($dataByCountry['carierName']);

        $method->setMethod($this->_code);
        $method->setMethodTitle($dataByCountry['methodName']);

        $shippingCost = (float)$dataByCountry['price'];
        $convertedPrice = $this->convertPrice($shippingCost, $this->country);

        $method->setPrice($convertedPrice);
        $method->setCost($convertedPrice);

        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
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

        return $amount * $rate;
    }
}
