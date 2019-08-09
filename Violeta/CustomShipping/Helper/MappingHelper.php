<?php

namespace Violeta\CustomShipping\Helper;

use Violeta\CustomShipping\Helper\Data;

class MappingHelper
{
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function formatName(string $name, string $keyPrefix): string
    {
        $table = json_decode($this->helper->getConfigValue($keyPrefix . '_mapping'), true);
        foreach ($table as $id => $data) {
            if ($data['default_name'] === $name) {
                return $data['custom_name'];
            }
        }
        $name = strtolower(str_replace('_', ' ', $name));
        $words = [];
        preg_match_all('/[a-z0-9]+/', $name, $words);
        if (!empty($words)) {
            $words = $words[0];
            foreach ($words as $word) {
                $name = str_replace(
                    $word,
                    strtoupper(substr($word, 0, 1)) . substr($word, 1),
                    $name
                );
            }
        }
        return $name;
    }
}
