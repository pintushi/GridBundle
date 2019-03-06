<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;

interface GridConfigurationEventInterface
{
    /**
     * Getter for grid configuration
     *
     * @return GridConfiguration
     */
    public function getConfig();
}
