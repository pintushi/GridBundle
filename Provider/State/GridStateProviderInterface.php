<?php

namespace Pintushi\Bundle\GridBundle\Provider\State;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;

/**
 * Describes a provider which has to return an array representing state of some grid component.
 * A state is represented by an array which contains request- and user-specific data about current grid component
 * settings (state), e.g. for columns it can contain information for each column about whether it is renderable
 * (visible) and its order (weight). Initially, due to specifics of grid frontend implementation, a grid state
 * has been introduced for usage in frontend - to adjust grid view according to user preferences, e.g. show only
 * specific columns in specific order. Later a grid state has been started used on backend, e.g. for sorters and
 * adjustment of datasource queries.
 */
interface GridStateProviderInterface
{
    /**
     * Returns state based on parameters, grid view and configuration.
     *
     * @param GridConfiguration $gridConfiguration
     * @param ParameterBag $gridParameters
     *
     * @return array State of grid component, e.g. columns` state, sorters state
     */
    public function getState(GridConfiguration $gridConfiguration, ParameterBag $gridParameters);

    /**
     * Returns state based on parameters and configuration.
     *
     * @param GridConfiguration $gridConfiguration
     * @param ParameterBag $gridParameters
     *
     * @return array State of grid component, e.g. columns` state, sorters state
     */
    public function getStateFromParameters(
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    );

    /**
     * Returns default state based on configuration only.
     *
     * @param GridConfiguration $gridConfiguration
     *
     * @return array State of grid component, e.g. columns` state, sorters state
     */
    public function getDefaultState(GridConfiguration $gridConfiguration);
}
