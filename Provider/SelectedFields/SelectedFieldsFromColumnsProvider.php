<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
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
    protected function getState(GridConfiguration $gridConfiguration, ParameterBag $gridParameters): array
    {
        $state = parent::getState($gridConfiguration, $gridParameters);

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
    protected function getConfiguration(GridConfiguration $gridConfiguration): array
    {
        return (array)$gridConfiguration->offsetGet(Configuration::COLUMNS_KEY);
    }
}
