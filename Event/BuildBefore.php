<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildBefore
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event dispatched before grid builder starts build grid
 * Listeners could apply validation of config and provide changes of config
 */
class BuildBefore extends Event implements GridEventInterface, GridConfigurationEventInterface
{
    const NAME = 'pintushi_grid.grid.build.before';

    /** @var GridInterface */
    protected $grid;

    /** @var GridConfiguration */
    protected $config;

    public function __construct(GridInterface $grid, GridConfiguration $config)
    {
        $this->grid   = $grid;
        $this->config     = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->config;
    }
}
