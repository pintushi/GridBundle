<?php

namespace Pintushi\Bundle\GridBundle\Extension;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Component\PhpUtils\ArrayUtil;

trait UnsupportedGridPrefixesTrait
{
    /** @var string */
    protected $unsupportedGridPrefixes = [];

    /**
     * @param string $prefix
     */
    public function addUnsupportedGridPrefix($prefix)
    {
        $this->unsupportedGridPrefixes[] = $prefix;
    }

    /**
     * Checks if configuration is for supported grid prefix
     *
     * @param GridConfiguration $config
     *
     * @return bool
     */
    protected function isUnsupportedGridPrefix(GridConfiguration $config)
    {
        $gridName = $config->getName();

        return ArrayUtil::some(
            function ($prefix) use ($gridName) {
                return strpos($gridName, $prefix) === 0;
            },
            $this->unsupportedGridPrefixes
        );
    }
}
