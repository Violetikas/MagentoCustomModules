<?php

namespace Violeta\CustomShippingModule\Logger;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class CustomShippingLogger
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $writer = new Stream(BP . '/var/log/custom_shipping_module.log');
        $this->logger = $logger;
        $this->logger->addWriter($writer);
    }

    public function log(string $message, $extra = [])
    {
        $this->logger->info($message, $extra);
    }
}
