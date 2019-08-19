<?php


namespace Violeta\CronModule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ConfigHelper extends AbstractHelper
{
    const XML_PATH = 'customer_changes/';

    public function getConfigValue($code, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . $code,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
