<?php

namespace Violeta\CronModule\Test\Unit;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Violeta\CronModule\Customer\CustomerChangeTracker;
use PHPUnit\Framework\TestCase;
use Violeta\CronModule\Model\ResourceModel\UserUpdate\CollectionFactory as UserUpdateCollectionFactory;
use Violeta\CronModule\Model\ResourceModel\UserUpdateFactory as ResourceModelFactory;
use Violeta\CronModule\Model\UserUpdateFactory;

class CustomerChangeTrackerTest extends TestCase
{
    private $customerCollectionFactory;
    private $previousCustomer;
    private $userUpdateFactory;
    private $resourceModelFactory;

    protected function setUp()
    {
        $this->customerCollectionFactory = $this->createMock(CollectionFactory::class);
        $this->previousCustomer = $this->createMock(UserUpdateCollectionFactory::class);
        $this->userUpdateFactory = $this->createMock(UserUpdateFactory::class);
        $this->resourceModelFactory = $this->createMock(ResourceModelFactory::class);
        parent::setUp();
    }

    public function testFindDeletedCustomers()
    {
        $changeTracker = new CustomerChangeTracker(
            $this->customerCollectionFactory,
            $this->previousCustomer,
            $this->userUpdateFactory,
            $this->resourceModelFactory
        );

        $currentCustomerData = [
            1 => "2019-08-20 07:24:58",
            20 => "2019-08-21 07:31:49",
            21 => "2019-08-21 07:34:59",
        ];

        $previous = [
            1 => "2019-08-20 07:24:58",
            20 => "2019-08-21 07:31:49",
            21 => "2019-08-21 07:34:59",
            22 => "2019-08-21 08:56:38",
        ];

        $deletedCustomers = $changeTracker->findDeletedCustomers($currentCustomerData, $previous);
        $this->assertEquals([
            0 => [
                'action' => 'deleted',
                'customer_id' => 22,
            ],
        ], $deletedCustomers);
    }
}
