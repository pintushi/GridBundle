<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\IterableResultInterface;
use Pintushi\Bundle\GridBundle\Exception\LogicException;
use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\DTO\SelectedItems;

/**
 * Registry for IterableResultFactory services.
 */
class IterableResultFactoryRegistry
{
    /**
     * @var IterableResultFactory[]
     */
    private $factories = [];

    /**
     * @param DatasourceInterface $dataSource
     * @param ActionConfiguration $actionConfiguration
     * @param DatagridConfiguration $gridConfiguration
     * @param SelectedItems $selectedItems
     * @return IterableResultInterface
     * @throws LogicException
     */
    public function createIterableResult(
        DatasourceInterface $dataSource,
        ActionConfiguration $actionConfiguration,
        DatagridConfiguration $gridConfiguration,
        SelectedItems $selectedItems
    ): IterableResultInterface {
        foreach ($this->factories as $factory) {
            if ($factory->isApplicable($dataSource)) {
                return $factory
                    ->createIterableResult($dataSource, $actionConfiguration, $gridConfiguration, $selectedItems);
            }
        }

        throw new LogicException(
            sprintf('No IterableResultFactory found for "%s" datasource type', get_class($dataSource))
        );
    }

    /**
     * @param IterableResultFactoryInterface $factory
     */
    public function addFactory(IterableResultFactoryInterface $factory)
    {
        $this->factories[] = $factory;
    }
}
