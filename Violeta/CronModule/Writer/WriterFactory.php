<?php

namespace Violeta\CronModule\Writer;

class WriterFactory
{
    public function selectFileExtension(string $type): CustomerChangesWriterInterface
    {
        if ($type === 'json') {
            return new JsonWriter();
        } elseif ($type === 'csv') {
            return new CsvWriter();
        } else {
            throw new \Exception('Unknown writer type: ' . $type);
        }
    }
}
