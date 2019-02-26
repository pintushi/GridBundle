<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\IterableResultInterface;
use Pintushi\Bundle\GridBundle\Exception\LogicException;
use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\DTO\SelectedItems;

/**
 * Interface for factory to create ResultIteratorInterface object for provided Datasource.
 */
interface IterableResultFactoryInterface
{
    /**
     * @param DatasourceInterface $dataSource
     * @return bool
     */
    public function isApplicable(DatasourceInterface $dataSource): bool;

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
    ): IterableResultInterface;
}
