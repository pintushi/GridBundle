<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\EventDispatcher\Event;

class OrmResultBeforeQuery extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.orm_datasource.result.before_query';

    /** @var GridInterface */
    protected $grid;

    /** @var QueryBuilder */
    protected $qb;

    /**
     * @param GridInterface $grid
     * @param QueryBuilder $qb
     */
    public function __construct(GridInterface $grid, QueryBuilder $qb)
    {
        $this->grid = $grid;
        $this->qb = $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }
}
