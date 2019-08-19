<?php


namespace Violeta\CronModule\Writer;


class CsvWriter implements CustomerChangesWriterInterface
{
    public function write(string $outputPath, array $data): void
    {
        $fileHandle = fopen($outputPath, 'w');
        fputcsv($fileHandle, ['action', 'customer_id', 'data']);
        foreach ($data as $row) {
            fputcsv(
                $fileHandle,
                [$row['action'], $row['customer_id'], isset($row['data']) ? json_encode($row['data']) : '']
            );
        }
        fclose($fileHandle);
    }

    public function getExtension(): string
    {
        return 'csv';
    }
}
