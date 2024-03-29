<?php

namespace Violeta\CustomShippingModule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Violeta\CustomShippingModule\Api\CustomShippingApiClient;
use Violeta\CustomShippingModule\Logger\CustomShippingLogger;
use Violeta\CustomShippingModule\Model\ApiOrderFactory;

class OrderCreatedObserver implements ObserverInterface
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
        $data = $this->formOrderDataArray($order);
        $placeOrder = $this->apiData->placeOrder($data);

        $newOrder = $this->apiOrderFactory->create();
        $newOrder->addData([
            'order_id' => $order->getId(),
            'api_order_id' => $placeOrder['id']
        ]);
        $newOrder->save();

        $this->logger->log(
            'Create order response: ',
            ['placeOrderResponse' => $placeOrder]
        );
    }

    protected function formOrderDataArray($order)
    {
        $orderData = $order->getData();
        $orderDataArray = [
            'createdAt' => $orderData['created_at'],
            'customerName' => $this->getCustomerNameData($order),
            'status' => $orderData['status'],
            'address' => $this->getAddressData($order),
            'items' => $this->getOrderItemsData($order),
            'shippingMethod' => $orderData['shipping_description']
        ];
        return $orderDataArray;
    }

    protected function getAddressData($order)
    {
        $addressData = $order->getShippingAddress()->getData();
        $address = [
            'city' => $addressData['city'],
            'country' => $addressData['country_id'],
            'postCode' => $addressData['postcode'],
            'street' => $addressData['street']
        ];
        return $address;
    }

    protected function getCustomerNameData($order)
    {
        $orderData = $order->getData();
        $customerName = $orderData['customer_firstname'] . ' ' . $orderData['customer_lastname'];

        return $customerName;
    }

    protected function getOrderItemsData($order)
    {
        $orderItemsData = [];
        $items = $order->getAllItems();
        foreach ($items as $item) {
            $orderItemsData[] = [
                'name' => $item->getName(),
                'price' => $item->getPrice(),
                'qty' => $item->getQtyOrdered()
            ];
        }
        return $orderItemsData;
    }
}