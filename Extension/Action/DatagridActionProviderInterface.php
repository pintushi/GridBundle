<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;

interface DatagridActionProviderInterface
{
    /**
     * @param DatagridConfiguration $configuration
     * @return boolean
     */
    public function hasActions(DatagridConfiguration $configuration);

    /**
     * Point to add additional configuration to datagrid config that will provide custom actions.
     * @param DatagridConfiguration $configuration
     */
    public function applyActions(DatagridConfiguration $configuration);
}
