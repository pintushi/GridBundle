<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResultBefore
 * @package Pintushi\Bundle\GridBundle\Event
 *
 * This event is dispatched before grid builder starts to build result
 */
class OrmResultBefore extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.orm_datasource.result.before';

    /**
     * @var GridInterface
     */
    protected $grid;

    /**
     * @var AbstractQuery
     */
    protected $query;

    /**
     * @param GridInterface   $grid
     * @param AbstractQuery $query
     */
    public function __construct(GridInterface $grid, AbstractQuery $query)
    {
        $this->grid = $grid;
        $this->query    = $query;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return AbstractQuery|Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}
