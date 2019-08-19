<?php


namespace Violeta\CronModule\Writer;


class JsonWriter implements CustomerChangesWriterInterface
{
    public function write(string $outputPath, array $data): void
    {
        file_put_contents($outputPath, json_encode($data));
    }

    public function getExtension(): string
    {
        return 'json';
    }
}
