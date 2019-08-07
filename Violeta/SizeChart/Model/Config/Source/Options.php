<?php


namespace Violeta\SizeChart\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Ddl\Table;

class Options extends AbstractSource
{
    public function getAllOptions()
    {
        $objectManager = ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('cms_block');
        $sql = $connection->select()->from(
            ["tn" => $tableName]
        );
        if (!$this->_options) {
            $result = $connection->fetchAll($sql);
            $this->_options = [
                [
                    'value' => '',
                    'label' => '',
                ]
            ];
            $this->_options = [['label' => 'Select Options', 'value' => '']];
            foreach ($result as $s) {
                $this->_options[] = ['value' => $s['identifier'], 'label' => $s['title']];
            }
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
