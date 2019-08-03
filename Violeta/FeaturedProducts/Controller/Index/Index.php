<?php


namespace Violeta\FeaturedProducts\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Psr\Log\LoggerInterface;
use Violeta\FeaturedProducts\Block\Featured;
use Violeta\FeaturedProducts\Helper\Data;


class Index extends Action
{
    protected $resultPageFactory;
    protected $logger;
    protected $productCollection;
    protected $helperData;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        Data $helperData

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->productCollection = $collectionFactory->create();
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->helperData->getGeneralConfig('enable')) {
            $resultPage = $this->resultPageFactory->create();
            $this->productCollection->addAttributeToFilter('featured_product', true);
            $this->productCollection->addFieldToSelect('*');
            $this->productCollection
                ->setPageSize($this->helperData->getGeneralConfig('products_limit'))
                ->setCurPage(1)
                ->load();
            $resultPage->getConfig()
                ->getTitle()
                ->set($this->helperData->getGeneralConfig('display_text'));
            /** @var Featured $list */
            $list = $resultPage->getLayout()->getBlock('featured');
            $list->setProductCollection($this->productCollection);
            $this->logger->info('hello from logger');
            return $resultPage;
        }
        return $this->_redirect('/');
    }
}
