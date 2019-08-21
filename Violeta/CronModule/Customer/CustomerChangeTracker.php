<?php

namespace Violeta\CronModule\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Violeta\CronModule\Model\ResourceModel\UserUpdate\CollectionFactory as UserUpdateCollectionFactory;
use Violeta\CronModule\Model\UserUpdateFactory;
use Violeta\CronModule\Model\ResourceModel\UserUpdateFactory as ResourceModelFactory;

class CustomerChangeTracker
{
    private $customerCollectionFactory;
    private $previousCustomer;
    private $currentCustomerDataSaved;
    private $userUpdateFactory;
    private $resourceModelFactory;

    public function __construct(
        CollectionFactory $customerCollectionFactory,
        UserUpdateCollectionFactory $previousCustomer,
        UserUpdateFactory $userUpdateFactory,
        ResourceModelFactory $resourceModelFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->previousCustomer = $previousCustomer;
        $this->userUpdateFactory = $userUpdateFactory;
        $this->resourceModelFactory = $resourceModelFactory;
    }

    public function getChangesSinceLastTime(): array
    {
        $current = $this->getCurrentCustomers();
        $previous = $this->getPreviousCustomers();

        $createdCustomers = $this->findCreatedCustomers($current, $previous);
        $updatedCustomers = $this->findUpdatedCustomers($current, $previous);
        $deletedCustomers = $this->findDeletedCustomers($current, $previous);

        $results = [];

        if (!empty($createdCustomers)) {
            $results[] = $createdCustomers;
        }

        if (!empty($updatedCustomers)) {
            $results[] = $updatedCustomers;
        }

        if (!empty($deletedCustomers)) {
            $results[] = $deletedCustomers;
        }

        return $results;
    }

    public function rememberCurrentState(): void
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
            $this->resourceModelFactory->create()->save($new);
        }
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

    private function findCreatedCustomers($current, $previous): array
    {
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
        return $results;
    }

    private function findUpdatedCustomers($current, $previous): array
    {
        $results = [];
        $collection = $this->customerCollectionFactory->create();
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
        return $results;
    }

    private function findDeletedCustomers($current, $previous): array
    {
        $results = [];
        $deletedIds = array_keys(array_diff_key($previous, $current));
        foreach ($deletedIds as $customerId) {
            $results[] = [
                'action' => 'deleted',
                'customer_id' => $customerId,
            ];
        }
        return $results;
    }

    private function saveCurrentCustomerData(array $currentCustomerData): void
    {
        $this->currentCustomerDataSaved = $currentCustomerData;
    }
}
