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

    public function __construct(LoggerInterface $logger, BlockFactory $blockFactory)
    {
        $this->blockFactory = $blockFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        try {
            $staticBlockInfo = [
                'title' => 'Special offer popup block',
                'identifier' => 'special_offer_popup',
                'stores' => ['0'],
                'is_active' => true,
                'content' => 'This special offer popup'
            ];
            $this->blockFactory->create()->setData($staticBlockInfo)->save();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $setup->endSetup();
    }
}