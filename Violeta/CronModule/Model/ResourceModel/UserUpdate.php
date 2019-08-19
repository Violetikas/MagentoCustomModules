<?php


namespace Violeta\CronModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class UserUpdate extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('user_updates', 'entity_id');
    }
}
