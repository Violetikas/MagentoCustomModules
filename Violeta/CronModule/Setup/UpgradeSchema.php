<?php

namespace Violeta\CronModule\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->createTable($installer);
        }
    }

    protected function createTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable('user_updates')
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false]
            )
            ->addIndex('customer', ['customer_id'], ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]);
        $installer->getConnection()->createTable($table);
    }
}
