<?php

namespace Violeta\CustomShipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory;

class NameMapping extends AbstractFieldArray
{
    protected $elementFactory;

    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->addColumn('default_name', ['label' => __('Default carrier name'), 'renderer' => false]);
        $this->addColumn('custom_name', ['label' => __('New carrier name'), 'renderer' => false]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

}
