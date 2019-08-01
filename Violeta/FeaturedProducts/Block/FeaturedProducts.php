<?php
namespace Violeta\FeaturedProducts\Block;



use Magento\Framework\View\Element\Template;

class FeaturedProducts extends Template
{

    public function doWhatever(){
        echo 'hello Violeta';
    }
    /**
     * @return string
     */
    public function getHelloWorldTxt()
    {
        return 'Hello world!';
    }


}