<?php

namespace Violeta\CustomShipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH = 'carriers/customshipping/';

    public function getConfigValue($code, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH . $code,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
