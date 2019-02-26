<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Symfony\Component\EventDispatcher\Event;

class OrmResultBeforeQuery extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.orm_datasource.result.before_query';

    /** @var DatagridInterface */
    protected $datagrid;

    /** @var QueryBuilder */
    protected $qb;

    /**
     * @param DatagridInterface $datagrid
     * @param QueryBuilder $qb
     */
    public function __construct(DatagridInterface $datagrid, QueryBuilder $qb)
    {
        $this->datagrid = $datagrid;
        $this->qb = $qb;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }
}
