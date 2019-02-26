<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class PreBuild
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event dispatched at start of datagrid builder
 * Listeners could apply validation of config and provide changes of config
 */
class PreBuild extends Event implements GridConfigurationEventInterface
{
    const NAME = 'pintushi_grid.datagrid.build.pre';

    /** @var DatagridConfiguration */
    protected $config;

    /** @var ParameterBag */
    protected $parameters;

    public function __construct(DatagridConfiguration $config, ParameterBag $parameters)
    {
        $this->config     = $config;
        $this->parameters = $parameters;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }
}
