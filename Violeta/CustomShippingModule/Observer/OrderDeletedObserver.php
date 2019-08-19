<?php

namespace Violeta\CustomShippingModule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Violeta\CustomShippingModule\Api\CustomShippingApiClient;
use Violeta\CustomShippingModule\Logger\CustomShippingLogger;
use Violeta\CustomShippingModule\Model\ApiOrderFactory;

class OrderDeletedObserver implements ObserverInterface
{
    private $apiData;
    private $logger;
    /**
     * @var ApiOrderFactory
     */
    private $apiOrderFactory;

    public function __construct(
        CustomShippingApiClient $apiData,
        CustomShippingLogger $logger,
        ApiOrderFactory $apiOrderFactory
    ) {
        $this->apiData = $apiData;
        $this->logger = $logger;
        $this->apiOrderFactory = $apiOrderFactory;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $apiOrder = $this->apiOrderFactory
            ->create()
            ->load($order->getId(), 'order_id');
        if (!$apiOrder->isEmpty()) {
            $apiOrderData = $apiOrder->getData();
            $response = $this->apiData->deleteOrder($apiOrderData['api_order_id']);
            try {
                $apiOrder->delete();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            $this->logger->log(
                'Delete order response: ',
                ['response' => $response]
            );
        }
    }
}