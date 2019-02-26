<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;

interface GridEventInterface
{
    /**
     * Getter for datagrid
     *
     * @return DatagridInterface
     */
    public function getDatagrid();
}
