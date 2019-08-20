<?php

namespace Violeta\CronModule\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Violeta\CronModule\Model\ResourceModel\UserUpdate\CollectionFactory as UserUpdateCollectionFactory;
use Violeta\CronModule\Model\UserUpdateFactory;

class CustomerChangeTracker
{
    private $customer;
    private $previousCustomer;
    private $current = [];
    private $userUpdateFactory;

    public function __construct(
        CollectionFactory $customer,
        UserUpdateCollectionFactory $previousCustomer,
        UserUpdateFactory $userUpdateFactory
    ) {
        $this->customer = $customer;
        $this->previousCustomer = $previousCustomer;
        $this->userUpdateFactory = $userUpdateFactory;
    }

    public function getChangesSinceLastTime(): array
    {
        $current = [];
        $collection = $this->customer->create();
        foreach ($collection as $customer) {
            $current[$customer->getId()] = $customer->getData('updated_at');
        }
        $this->setCurrent($current);

        $previous = [];
        foreach ($this->previousCustomer->create() as $customer) {
            $previous[$customer->getData('customer_id')] = $customer->getData('updated_at');
        }

        $createdIds = array_keys(array_diff_key($current, $previous));
        $deletedIds = array_keys(array_diff_key($previous, $current));
        $updatedIds = [];
        foreach ($current as $customerId => $updatedAt) {
            // TODO extract to private method
            if (array_key_exists($customerId, $previous) && $updatedAt > $previous[$customerId]) {
                $updatedIds[] = $customerId;
            }
        }

        $results = [];
        foreach ($createdIds as $customerId) {
            $results[] = [
                'action' => 'added',
                'customer_id' => $customerId,
                'data' => $collection->getItemById($customerId)->getData(),
            ];
        }
        foreach ($updatedIds as $customerId) {
            $results[] = [
                'action' => 'updated',
                'customer_id' => $customerId,
                'data' => $collection->getItemById($customerId)->getData(),
            ];
        }
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
        $collection = $this->userUpdateFactory->create();
        foreach ($collection as $item) {
            $item->delete();
        }

        foreach ($this->current as $customerId => $updatedAt) {
            $new = $this->userUpdateFactory->create();
            $new->addData([
                'customer_id' => $customerId,
                'updated_at' => $updatedAt,
            ]);
            $new->save();
        }

        $this->setCurrent([]);
    }

    private function setCurrent(array $current): void
    {
        $this->current = $current;
    }
}
