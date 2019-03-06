<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;

/**
 * Describes providers that must return an array of field names which must be present in select statement of
 * datasource query according to grid configuration and parameters.
 */
interface SelectedFieldsProviderInterface
{
    /**
     * @param GridConfiguration $gridConfiguration
     * @param ParameterBag $gridParameters
     *
     * @return array
     */
    public function getSelectedFields(
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    ): array;
}
