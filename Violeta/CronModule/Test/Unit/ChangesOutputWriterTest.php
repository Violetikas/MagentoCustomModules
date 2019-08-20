<?php

namespace Violeta\CronModule\Test\Unit;

use PHPUnit\Framework\TestCase;
use Violeta\CronModule\Helper\ConfigHelper;
use Violeta\CronModule\Output\ChangesOutputWriter;
use Violeta\CronModule\Writer\JsonWriter;
use Violeta\CronModule\Writer\WriterFactory;

class ChangesOutputWriterTest extends TestCase
{
    /**
     * @var bool|string
     */
    private $filePath;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $helper;
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $writerFactory;

    protected function setUp()
    {
        $this->helper = $this->createMock(ConfigHelper::class);
        $this->helper->method('getConfigValue')->willReturn('json');
        $this->writerFactory = $this->createMock(WriterFactory::class);
        $this->writerFactory->method('create')->willReturn(JsonWriter::class);
        parent::setUp();
        $this->filePath = tempnam(sys_get_temp_dir(), 'changesoutputwritertest');
    }

    protected function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
        $this->filePath = null;
    }

    public function testWriteChanges()
    {
        $changesOutputWriter = new ChangesOutputWriter();
        $changes =
            [
                '{
                    "action" : "added",
                    "customer_id" : 18,
                    "data" :
                        {"entity_id" : "18",
   "website_id" : "1",
   "email" : "test@test.com",
   "group_id" : "1",
   "increment_id" : null,
   "store_id" : "1",
   "created_at" : "2019-08-20 09:37:20",
   "updated_at" : "2019-08-20 09:37:21",
   "is_active" : "1",
   "disable_auto_group_change" : "0",
   "created_in" : "Default Store View",
   "prefix" : null,
   "firstname" : "test",
   "middlename" : null,
   "lastname" : "test",
   "suffix" : null,
   "dob" : null,
   "password_hash" : null,
   "rp_token" : "I0T6MqRFv236zXkpGhmuEIfHXetvQPoQ",
   "rp_token_created_at" : "2019-08-20 09:37:21",
   "default_billing" : "0",
   "default_shipping" : "0",
   "taxvat" : null,
   "confirmation" : null,
   "gender" : "0",
   "failures_num" : "0",
   "first_failure" : null,
   "lock_expires" : null}}',
            ];
        ;
    }
}
