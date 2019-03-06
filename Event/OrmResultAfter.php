<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Doctrine\ORM\AbstractQuery;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Symfony\Component\EventDispatcher\Event;
use Pagerfanta\Pagerfanta;

class OrmResultAfter extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.orm_datasource.result.after';

    /**
     * @var GridInterface
     */
    protected $grid;

    /**
     * @var Pagerfanta
     */
    protected $data;

    /**
     * @var AbstractQuery
     */
    protected $query;

    /**
     * @param GridInterface $grid
     * @param array             $data
     * @param AbstractQuery     $query
     */
    public function __construct(
        GridInterface $grid,
        Pagerfanta $data,
        AbstractQuery $query = null
    ) {
        $this->grid = $grid;
        $this->data  = $data;
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
     * @return Pagerfanta
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(Pagerfanta $data)
    {
        $this->data = $data;
    }

    /**
     * @return AbstractQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
