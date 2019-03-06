<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuildBefore
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event dispatched after grid builder finish building datasource for grid
 */
class BuildAfter extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.grid.build.after';

    /** @var GridInterface */
    protected $grid;

    public function __construct(GridInterface $grid)
    {
        $this->grid = $grid;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrid()
    {
        return $this->grid;
    }
}
