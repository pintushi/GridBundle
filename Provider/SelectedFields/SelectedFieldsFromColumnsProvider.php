<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Pintushi\Bundle\GridBundle\Extension\Formatter\Configuration;
use Pintushi\Bundle\GridBundle\Provider\State\ColumnsStateProvider;

/**
 * Returns array of field names (used in renderable columns) which must be present in select statement of datasource
 * query.
 */
class SelectedFieldsFromColumnsProvider extends AbstractSelectedFieldsProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getState(DatagridConfiguration $datagridConfiguration, ParameterBag $datagridParameters): array
    {
        $state = parent::getState($datagridConfiguration, $datagridParameters);

        return array_filter(
            $state,
            function (array $columnState) {
                return $columnState[ColumnsStateProvider::RENDER_FIELD_NAME];
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(DatagridConfiguration $datagridConfiguration): array
    {
        return (array)$datagridConfiguration->offsetGet(Configuration::COLUMNS_KEY);
    }
}
