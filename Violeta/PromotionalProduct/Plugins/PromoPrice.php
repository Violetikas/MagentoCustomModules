<?php

namespace Violeta\PromotionalProduct\Plugins;

use Magento\Catalog\Model\Product;

class PromoPrice
{
    public function afterGetPrice(Product $product, $price)
    {
        if (!$product->getData('promotional')) {
            return $price;
        }
        return floatval($product->getData('promotional_price'));
    }
}
