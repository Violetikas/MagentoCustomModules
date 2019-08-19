<?php

namespace Violeta\CronModule\Writer;

class WriterFactory
{
    public function create(string $type): CustomerChangesWriterInterface
    {
        if ($type === 'json') {
            return new JsonWriter();
        } elseif ($type === 'csv') {
            return new CsvWriter();
        } else {
            throw new \DomainException('Unknown writer type: ' . $type);
        }
    }
}
