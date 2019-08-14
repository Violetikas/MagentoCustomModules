<?php
namespace Violeta\CustomShippingModule\Model;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
class ApiOrder extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'api_orders_ids';
    protected $_cacheTag = 'api_orders_ids';
    protected $_eventPrefix = 'api_orders_ids';
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
        $this->_init('Violeta\CustomShippingModule\Model\ResourceModel\ApiOrder');
    }
}
