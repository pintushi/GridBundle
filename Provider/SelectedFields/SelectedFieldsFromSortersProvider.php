<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Extension\Sorter\Configuration as SorterConfiguration;

/**
 * Returns an array of field names (required by applied sorters) which must be present in select statement of
 * datasource query.
 */
class SelectedFieldsFromSortersProvider extends AbstractSelectedFieldsProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getConfiguration(DatagridConfiguration $datagridConfiguration): array
    {
        return $datagridConfiguration->offsetGetByPath(SorterConfiguration::COLUMNS_PATH, []);
    }
}
