<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildBefore
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event dispatched before datagrid builder starts build datagrid
 * Listeners could apply validation of config and provide changes of config
 */
class BuildBefore extends Event implements GridEventInterface, GridConfigurationEventInterface
{
    const NAME = 'pintushi_grid.datagrid.build.before';

    /** @var DatagridInterface */
    protected $datagrid;

    /** @var DatagridConfiguration */
    protected $config;

    public function __construct(DatagridInterface $datagrid, DatagridConfiguration $config)
    {
        $this->datagrid   = $datagrid;
        $this->config     = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->config;
    }
}
