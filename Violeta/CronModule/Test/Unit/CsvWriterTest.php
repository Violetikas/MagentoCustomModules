<?php

namespace Violeta\CronModule\Writer;

use PHPUnit\Framework\TestCase;


class CsvWriterTest extends TestCase
{
    private $filePath;

    protected function setUp()
    {
        parent::setUp();
        $this->filePath = tempnam(sys_get_temp_dir(), 'csvwritertest');
    }

    protected function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
        $this->filePath = null;
    }

    public function testWrite()
    {
        $writer = new CsvWriter();
        $customer = json_decode(
            '{"entity_id":"11",
             "website_id":"1",
              "email":"whatever1@gmail.com",
               "group_id":"1",
                "increment_id":null,
                 "store_id":"2",
                  "created_at":"2019-08-18 13:18:47",
                   "updated_at":"2019-08-18 13:18:48",
                    "is_active":"1",
                     "disable_auto_group_change":"0",
                      "created_in":"Second store view",
                       "prefix":null, "firstname":"Petras",
                        "middlename":null,
                         "lastname":"Pirmasis",
                          "suffix":null, "dob":null,
                           "password_hash":"2d4788574f6b918a4d1fd9435f25621b0cae38559e85fdeb45856b3c9df7d6cb:kg2Jzguul7XIeXDI085fNLTK9B1MvqTB:1",
                            "rp_token":"mz5qlwATAuQ7id3LD5JXAaDOnwpB1ZGO",
                             "rp_token_created_at":"2019-08-18 13:18:48",
                              "default_billing":null,
                               "default_shipping":null,
                                "taxvat":null,
                                 "confirmation":null,
                                  "gender":null,
                                   "failures_num":"0",
                                    "first_failure":null,
                                     "lock_expires":null}',
            true
        );
        $writer->write($this->filePath, [
            ['action' => 'created', 'customer_id' => 11, 'data' => $customer],
            ['action' => 'deleted', 'customer_id' => 54],
        ]);
        $decoded = $this->readCsv($this->filePath);
        $this->assertCount(3, $decoded);
        $this->assertEquals([
            'action',
            'customer_id',
            'updated_at',
            'firstname',
            'middlename',
            'lastname',
            'default_billing',
            'default_shipping',
        ], $decoded[0]);
        $this->assertEquals([
            'created',
            '11',
            '2019-08-18 13:18:48',
            'Petras',
            '',
            'Pirmasis',
            '',
            '',
        ], $decoded[1]);
        $this->assertEquals(['deleted', '54', '', '', '', '', '', ''], $decoded[2]);
    }

    private function readCsv(string $filePath): array
    {
        $rows = [];
        $fileDescriptor = fopen($filePath, 'r');
        while (!feof($fileDescriptor)) {
            $row = fgetcsv($fileDescriptor);
            if ($row) {
                $rows[] = $row;
            }
        }
        fclose($fileDescriptor);
        return $rows;
    }
}
