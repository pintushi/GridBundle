<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Grid\GridInterface;

interface GridEventInterface
{
    /**
     * Getter for grid
     *
     * @return GridInterface
     */
    public function getGrid();
}
