<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;

interface GridConfigurationEventInterface
{
    /**
     * Getter for datagrid configuration
     *
     * @return DatagridConfiguration
     */
    public function getConfig();
}
