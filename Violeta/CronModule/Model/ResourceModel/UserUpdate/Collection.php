<?php

namespace Violeta\CronModule\Model\ResourceModel\UserUpdate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'user_updates_collection';
    protected $_eventObject = 'user_updates_collection';

    protected function _construct()
    {
        $this->_init(
            'Violeta\CronModule\Model\UserUpdate',
            'Violeta\CronModule\Model\ResourceModel\UserUpdate'
        );
    }
}
