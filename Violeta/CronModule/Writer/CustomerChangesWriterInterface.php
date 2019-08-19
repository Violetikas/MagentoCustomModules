<?php


namespace Violeta\CronModule\Writer;


interface CustomerChangesWriterInterface
{
    public function write(string $outputPath, array $data): void;

    public function getExtension(): string;
}
