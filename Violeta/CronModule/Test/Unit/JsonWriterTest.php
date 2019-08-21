<?php


namespace Violeta\CronModule\Test\Unit;

use PHPUnit\Framework\TestCase;
use Violeta\CronModule\Writer\JsonWriter;

class JsonWriterTest extends TestCase
{
    private $filePath;

    protected function setUp()
    {
        parent::setUp();
        $this->filePath = tempnam(sys_get_temp_dir(), 'jsonwritertest');
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
        $writer = new JsonWriter();
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
            ['action' => 'added', 'customer_id' => 11, 'data' => $customer],
        ]);
        $contents = $this->readJson($this->filePath);
        $this->assertEquals(
            '[{"action":"added","customer_id":11,
            "data":{"entity_id":"11",
            "website_id":"1",
            "email":"whatever1@gmail.com",
            "group_id":"1","increment_id":null,
            "store_id":"2",
            "created_at":"2019-08-18 13:18:47",
            "updated_at":"2019-08-18 13:18:48",
            "is_active":"1",
            "disable_auto_group_change":"0",
            "created_in":"Second store view",
            "prefix":null,
            "firstname":"Petras",
            "middlename":null,
            "lastname":"Pirmasis",
            "suffix":null,
            "dob":null,
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
            "lock_expires":null}}]',
            $contents
        );
    }

    private function readJson(string $filePath)
    {
        $json = file_get_contents($filePath);
        return $json;
    }
}
