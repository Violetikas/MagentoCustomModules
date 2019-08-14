<?php

namespace Violeta\CronModule\Cron;

use \Psr\Log\LoggerInterface;

class UserUpdateCronTask
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info('!!!!!!!!!!!!!!!!!!!!!!!CRON WORKS!!!!!!!!!!!!!!!!!!!');
    }
}
