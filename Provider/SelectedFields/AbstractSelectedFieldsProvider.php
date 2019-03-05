<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Pintushi\Bundle\GridBundle\Provider\State\DatagridStateProviderInterface;

/**
 * Abstract implementation of provider that must return an array of field names which must be present in select
 * statement of datasource query used by some datagrid component, e.g. columns, sorters.
 */
abstract class AbstractSelectedFieldsProvider implements SelectedFieldsProviderInterface
{
    /** @var DatagridStateProviderInterface */
    protected $datagridStateProvider;

    /**
     * @param DatagridStateProviderInterface $datagridStateProvider
     */
    public function __construct(DatagridStateProviderInterface $datagridStateProvider)
    {
        $this->datagridStateProvider = $datagridStateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedFields(
        DatagridConfiguration $datagridConfiguration,
        ParameterBag $datagridParameters
    ): array {
        $configuration = $this->getConfiguration($datagridConfiguration);
        $state = $this->getState($datagridConfiguration, $datagridParameters);

        return array_map(function (string $name) use ($configuration) {
            return $configuration[$name]['property_path'] ?? $name;
        }, array_keys($state));
    }

    /**
     * Returns state of component.
     *
     * @param DatagridConfiguration $datagridConfiguration
     * @param ParameterBag $datagridParameters
     *
     * @return array
     */
    protected function getState(DatagridConfiguration $datagridConfiguration, ParameterBag $datagridParameters)
    {
        return (array)$this->datagridStateProvider->getState($datagridConfiguration, $datagridParameters);
    }

    /**
     * Must return the configuration of component (e.g. columns or sorters)
     *
     * @param DatagridConfiguration $datagridConfiguration
     *
     * @return array
     */
    abstract protected function getConfiguration(DatagridConfiguration $datagridConfiguration);
}
