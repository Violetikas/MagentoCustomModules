<?php


namespace Violeta\FeaturedProducts\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $_resultPageFactory;
    protected $logger;

    public function __construct(Context $context, PageFactory $resultPageFactory, LoggerInterface $logger)
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $this->logger->info('heloooooo from controller');
        return $resultPage;
    }
}