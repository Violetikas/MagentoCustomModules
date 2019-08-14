<?php

namespace Violeta\CustomShippingModule\Setup;

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
            ->newTable('api_orders_ids')
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addColumn(
                'api_order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0]
            )
            ->addIndex(
                $installer->getIdxName(
                    'api_orders_ids',
                    ['order_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['order_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            );
        $installer->getConnection()->createTable($table);
    }
}
