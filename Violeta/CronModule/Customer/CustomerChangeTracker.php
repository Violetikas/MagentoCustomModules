<?php


namespace Violeta\CronModule\Customer;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Violeta\CronModule\Model\UserUpdateFactory;

class CustomerChangeTracker
{
    /** @var CollectionFactory */
    private $customer;
    /** @var UserUpdateFactory */
    private $previousCustomer;
    /**
     * @var array
     */
    private $current = [];

    /**
     * CustomerChangeTracker constructor.
     * @param CollectionFactory $customer
     * @param UserUpdateFactory $previousCustomer
     */
    public function __construct(CollectionFactory $customer, UserUpdateFactory $previousCustomer)
    {
        $this->customer = $customer;
        $this->previousCustomer = $previousCustomer;
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
        foreach ($this->previousCustomer->create()->getCollection() as $customer) {
            $previous[$customer->getData('customer_id')] = $customer->getData('updated_at');
        }

        $createdIds = array_keys(array_diff_key($current, $previous));
        $deletedIds = array_keys(array_diff_key($previous, $current));
        $updatedIds = [];
        foreach ($current as $customerId => $updatedAt) {
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
        foreach ($this->previousCustomer->create()->getCollection() as $item) {
            $item->delete();
        }

        foreach ($this->current as $customerId => $updatedAt) {
            $new = $this->previousCustomer->create();
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
