<?php

namespace Violeta\CronModule\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Violeta\CronModule\Model\ResourceModel\UserUpdate\CollectionFactory as UserUpdateCollectionFactory;
use Violeta\CronModule\Model\UserUpdateFactory;

class CustomerChangeTracker
{
    private $customerCollectionFactory;
    private $previousCustomer;
    private $currentCustomerDataSaved;
    private $userUpdateFactory;

    public function __construct(
        CollectionFactory $customerCollectionFactory,
        UserUpdateCollectionFactory $previousCustomer,
        UserUpdateFactory $userUpdateFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->previousCustomer = $previousCustomer;
        $this->userUpdateFactory = $userUpdateFactory;
    }

    public function getChangesSinceLastTime(): array
    {
        $current = $this->getCurrentCustomers();
        $previous = $this->getPreviousCustomers();

        $collection = $this->customerCollectionFactory->create();
        $results = [];

        $createdIds = array_keys(array_diff_key($current, $previous));
        foreach ($createdIds as $customerId) {
            $results[] = [
                'action' => 'added',
                'customer_id' => $customerId,
                'data' => $collection->getItemById($customerId)->getData(),
            ];
        }

        $updatedIds = [];
        foreach ($current as $customerId => $updatedAt) {
            if (array_key_exists($customerId, $previous) && $updatedAt > $previous[$customerId]) {
                $updatedIds[] = $customerId;
            }
        }
        foreach ($updatedIds as $customerId) {
            $results[] = [
                'action' => 'updated',
                'customer_id' => $customerId,
                'data' => $collection->getItemById($customerId)->getData(),
            ];
        }

        $deletedIds = array_keys(array_diff_key($previous, $current));
        foreach ($deletedIds as $customerId) {
            $results[] = [
                'action' => 'deleted',
                'customer_id' => $customerId,
            ];
        }

        return $results;
    }

    public function remember(): void
    {
        $collection = $this->previousCustomer->create();
        foreach ($collection as $item) {
            $item->delete();
        }

        foreach ($this->currentCustomerDataSaved as $customerId => $updatedAt) {
            $new = $this->userUpdateFactory->create();
            $new->addData([
                'customer_id' => $customerId,
                'updated_at' => $updatedAt,
            ]);
            $new->save();
        }
    }

    private function saveCurrentCustomerData(array $currentCustomerData): void
    {
        $this->currentCustomerDataSaved = $currentCustomerData;
    }

    private function getCurrentCustomers(): array
    {
        $currentCustomerData = [];
        $collection = $this->customerCollectionFactory->create();
        foreach ($collection as $customer) {
            $currentCustomerData[$customer->getId()] = $customer->getData('updated_at');
        }
        $this->saveCurrentCustomerData($currentCustomerData);
        return $currentCustomerData;
    }

    private function getPreviousCustomers(): array
    {
        $previous = [];
        $collection = $this->previousCustomer->create();
        foreach ($collection as $customer) {
            $previous[$customer->getData('customer_id')] = $customer->getData('updated_at');
        }
        return $previous;
    }
}
