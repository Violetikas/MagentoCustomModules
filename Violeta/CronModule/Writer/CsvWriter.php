<?php


namespace Violeta\CronModule\Writer;


class CsvWriter implements CustomerChangesWriterInterface
{
    private const CUSTOMER_FIELDS = [
        'updated_at',
        'firstname',
        'middlename',
        'lastname',
        'default_billing',
        'default_shipping',
    ];

    public function write(string $outputPath, array $data): void
    {
        $fileHandle = fopen($outputPath, 'w');
        fputcsv($fileHandle, array_merge(['action', 'customer_id'], self::CUSTOMER_FIELDS));
        foreach ($data as $row) {
            fputcsv(
                $fileHandle,
                array_merge([
                    $row['action'],
                    $row['customer_id'],
                ], $this->createArray($row['data']))
            );
        }
        fclose($fileHandle);
    }

    public function getExtension(): string
    {
        return 'csv';
    }

    private function createArray($customer): array
    {
        $dataArray = [];
        foreach (self::CUSTOMER_FIELDS as $field) {
            $dataArray[$field] = isset($customer[$field]) ? $customer[$field] : '';
        }
        return $dataArray;
    }
}
