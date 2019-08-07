<?php


namespace Violeta\PromotionalProduct\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Data;


class Promotional extends Template
{
    /**
     * @var Data
     */
    private $config;
    /**
     * @var Data
     */
    private $helper;
    protected $product;

    public function __construct(
        Template\Context $context,
        Data $helper,
        \Violeta\PromotionalProduct\Helper\Data $config,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->config = $config;
        $this->helper = $helper;
    }

    public function isProductPromotional()
    {
        if ($this->config->getGeneralConfig('enable')) {
            if (is_null($this->product)) {
                $this->product = $this->helper->getProduct();
            }
            $promo = $this->product->getData('promotional');
            return $promo;
        }
        return false;
    }

    public function getPromoPrice()
    {
        if (is_null($this->product)) {
            $this->product = $this->helper->getProduct();
        }
        if (empty($this->product)) {
            return '';
        }
        $price = $this->product->getData('promotional_price');
        return $price;
    }


    public function getTimeleft()
    {
        $endDate = new \DateTime($this->config->getGeneralConfig('end_date'));
        $today = new \DateTime('now');
        $difference = $today->diff($endDate);
        if ($difference) {
            return $this->getTimeleftString($difference);
        }
        return false;
    }

    public function getLabelBg()
    {
        return $this->config->getGeneralConfig('background');
    }

    public function getTextColor()
    {
        return $this->config->getGeneralConfig('text_color');
    }

    public function getLabelText()
    {
        return trim($this->config->getGeneralConfig('label_name'));
    }

    private function getTimeleftString($difference)
    {
        $days = $difference->format('%d');
        $hours = $difference->format('%h');
        $mins = $difference->format('%i');
        $secs = $difference->format('%s');

        $timeleft = '';

        if ($days) {
            $timeleft .= $days . ' days ';
        }

        if ($hours) {
            $timeleft .= $hours . ' hours ';
        }

        if ($mins) {
            $timeleft .= $mins . ' minutes ';
        }

        if ($secs) {
            $timeleft .= $secs . ' seconds ';
        }

        return $timeleft . 'left until promotion ends.';
    }
}
