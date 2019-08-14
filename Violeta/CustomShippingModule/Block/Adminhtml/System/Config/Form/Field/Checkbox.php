<?php

namespace Violeta\CustomShippingModule\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class Checkbox extends AbstractBlock
{
    protected function _toHtml()
    {
        $elId = $this->getInputId();
        $elName = $this->getInputName();
        $colName = $this->getColumnName();
        $column = $this->getColumn();

        return '<input type="checkbox" value="1" id="' . $elId . '"' .
            ' name="' . $elName . '" <%- ' . $colName . ' %> ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') .
            ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }


}