<?php

namespace Violeta\CronModule\Logger;

use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class CustomCronLogger
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $writer = new Stream(BP . '/var/log/cron_module.log');
        $this->logger = $logger;
        $this->logger->addWriter($writer);
    }

    public function log(string $message, $extra = [])
    {
        $this->logger->info($message, $extra);
    }
}
