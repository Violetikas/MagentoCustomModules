<?php

namespace Violeta\CronModule\Cron;

use Violeta\CronModule\Customer\CustomerChangeTracker;
use Violeta\CronModule\Logger\CustomCronLogger;
use Violeta\CronModule\Output\ChangesOutputWriter;

class UserUpdateCronTask
{
    protected $logger;
    private $tracker;
    private $output;

    public function __construct(
        CustomerChangeTracker $tracker,
        CustomCronLogger $logger,
        ChangesOutputWriter $output
    ) {
        $this->logger = $logger;
        $this->tracker = $tracker;
        $this->output = $output;
    }

    public function execute(): void
    {
        $this->logger->log('Looking for changes since last time.');
        $changes = $this->tracker->getChangesSinceLastTime();
        $outputFile = $this->output->writeChanges($changes);
        $this->logger->log(sprintf('%d changes written to file %s', count($changes), $outputFile));
        $this->tracker->remember();
    }
}
