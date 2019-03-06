<?php

namespace Pintushi\Bundle\GridBundle\EventListener;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\Orm\ParameterBinder;
use Pintushi\Bundle\GridBundle\Datasource\ParameterBinderAwareInterface;
use Pintushi\Bundle\GridBundle\Event\BuildAfter;

/**
 * Binds grid parameters to datasource from datasource option "bind_parameters".
 *
 * @see ParameterBinder
 */
class DatasourceBindParametersListener
{
    /**
     * Binds grid parameters to datasource query on event.
     *
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        $grid = $event->getGrid();

        $datasource = $grid->getDatasource();
        if (!$datasource instanceof ParameterBinderAwareInterface) {
            return;
        }

        $parameters = $grid->getConfig()
            ->offsetGetByPath(GridConfiguration::DATASOURCE_BIND_PARAMETERS_PATH, []);
        if (!$parameters || !is_array($parameters)) {
            return;
        }

        $datasource->bindParameters($parameters);
    }
}
