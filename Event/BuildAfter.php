<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildBefore
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event dispatched after datagrid builder finish building datasource for datagrid
 */
class BuildAfter extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.datagrid.build.after';

    /** @var DatagridInterface */
    protected $datagrid;

    public function __construct(DatagridInterface $datagrid)
    {
        $this->datagrid = $datagrid;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }
}
