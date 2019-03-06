<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Provider\State\GridStateProviderInterface;

/**
 * Abstract implementation of provider that must return an array of field names which must be present in select
 * statement of datasource query used by some grid component, e.g. columns, sorters.
 */
abstract class AbstractSelectedFieldsProvider implements SelectedFieldsProviderInterface
{
    /** @var GridStateProviderInterface */
    protected $gridStateProvider;

    /**
     * @param GridStateProviderInterface $gridStateProvider
     */
    public function __construct(GridStateProviderInterface $gridStateProvider)
    {
        $this->gridStateProvider = $gridStateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedFields(
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    ): array {
        $configuration = $this->getConfiguration($gridConfiguration);
        $state = $this->getState($gridConfiguration, $gridParameters);

        return array_map(function (string $name) use ($configuration) {
            return $configuration[$name]['property_path'] ?? $name;
        }, array_keys($state));
    }

    /**
     * Returns state of component.
     *
     * @param GridConfiguration $gridConfiguration
     * @param ParameterBag $gridParameters
     *
     * @return array
     */
    protected function getState(GridConfiguration $gridConfiguration, ParameterBag $gridParameters)
    {
        return (array)$this->gridStateProvider->getState($gridConfiguration, $gridParameters);
    }

    /**
     * Must return the configuration of component (e.g. columns or sorters)
     *
     * @param GridConfiguration $gridConfiguration
     *
     * @return array
     */
    abstract protected function getConfiguration(GridConfiguration $gridConfiguration);
}
