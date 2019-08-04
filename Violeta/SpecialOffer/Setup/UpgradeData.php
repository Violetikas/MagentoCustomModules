<?php


namespace Violeta\SpecialOffer\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Psr\Log\LoggerInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $blockFactory;

    public function __construct(
        LoggerInterface $logger,
        BlockFactory $blockFactory
    ) {
        $this->blockFactory = $blockFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        try {
            $staticBlockInfo = [
                'title' => 'specialoffer',
                'identifier' => 'specialoffer',
                'stores' => ['0'],
                'is_active' => true,
                'content' => 'Special Offer PopUp'
            ];
            $this->blockFactory
                ->create()
                ->setData($staticBlockInfo)
                ->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $setup->endSetup();
    }
}
