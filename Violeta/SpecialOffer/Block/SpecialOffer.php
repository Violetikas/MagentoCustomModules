<?php


namespace Violeta\SpecialOffer\Block;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Violeta\SpecialOffer\Helper\Data;

class SpecialOffer extends Template
{
    private $blockRepository;
    const POP_BLOCK_IDENTIFIER = 'specialoffer';
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Template\Context $context,
        BlockRepositoryInterface $blockRepo,
        Data $helper,
        array $data = []
    ) {
        $this->blockRepository = $blockRepo;
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    public function getBlockContent()
    {
        try {
            $block = $this->blockRepository->getById(self::POP_BLOCK_IDENTIFIER);
            $content = $block->getContent();
        } catch (LocalizedException $e) {
            $content = false;
        }
        return $content;
    }

    public function getConfigArray(): array
    {
        return [
            'enable' => $this->helper->getGeneralConfig('enable'),
            'title' => $this->helper->getGeneralConfig('popup_title'),
            'delay' => $this->helper->getGeneralConfig('popup_delay')
        ];
    }
}
