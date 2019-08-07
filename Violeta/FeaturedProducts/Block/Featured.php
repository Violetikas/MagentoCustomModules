<?php

namespace Violeta\FeaturedProducts\Block;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Catalog\Block\Product\ListProduct;

class Featured extends ListProduct
{
    public function getLoadedProductCollection()
    {
        return $this->_productCollection;
    }

    public function setProductCollection(AbstractCollection $collection)
    {
        $this->_productCollection = $collection;
    }
}
