<?php

namespace Violeta\CronModule\Cron;

use Violeta\CronModule\Customer\CustomerChangeTracker;
use Violeta\CronModule\Logger\CustomCronLogger;
use Violeta\CronModule\Output\ChangesOutputWriter;

class CustomerUpdateCronTask
{
    protected $logger;
    private $customerChangeTracker;
    private $outputWiter;

    public function __construct(
        CustomerChangeTracker $customerChangeTracker,
        CustomCronLogger $logger,
        ChangesOutputWriter $outputWiter
    ) {
        $this->logger = $logger;
        $this->customerChangeTracker = $customerChangeTracker;
        $this->outputWiter = $outputWiter;
    }

    public function execute(): void
    {
        $this->logger->log('Looking for changes since last time.');
        $changes = $this->customerChangeTracker->getChangesSinceLastTime();
        $outputFile = $this->outputWiter->writeChanges($changes);
        $this->logger->log(sprintf('%d changes written to file %s', count($changes), $outputFile));
        $this->customerChangeTracker->rememberCurrentState();
    }
}
