<?php

namespace Violeta\CronModule\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class UserUpdate extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'user_updates';
    protected $_cacheTag = 'user_updates';
    protected $_eventPrefix = 'user_updates';

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    protected function _construct()
    {
        $this->_init('Violeta\CronModule\Model\ResourceModel\UserUpdate');
    }
}
