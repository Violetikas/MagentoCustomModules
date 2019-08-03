<?php


namespace Violeta\SpecialOffer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Cms\Api\BlockRepositoryInterface;

class SpecialOffer extends Template
{
    private $blockRepository;
    const POP_BLOCK_IDENTIFIER = 'specialoffer';

    public function __construct(
        Template\Context $context,
        BlockRepositoryInterface $blockRepository,
        array $data = []
    ) {
        $this->blockRepository = $blockRepository;
        parent::__construct($context, $data);
    }

    public function getContent()
    {
        $block = $this->blockRepository->getById(self::POP_BLOCK_IDENTIFIER);
        $content = $block->getContent();
        return $content;
    }

}