<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Doctrine\ORM\AbstractQuery;
use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Pintushi\Bundle\GridBundle\Datasource\ResultRecordInterface;
use Symfony\Component\EventDispatcher\Event;

class OrmResultAfter extends Event implements GridEventInterface
{
    const NAME = 'pintushi_grid.orm_datasource.result.after';

    /**
     * @var DatagridInterface
     */
    protected $datagrid;

    /**
     * @var ResultRecordInterface[]
     */
    protected $records;

    /**
     * @var AbstractQuery
     */
    protected $query;

    /**
     * @param DatagridInterface $datagrid
     * @param array             $records
     * @param AbstractQuery     $query
     */
    public function __construct(
        DatagridInterface $datagrid,
        array $records = [],
        AbstractQuery $query = null
    ) {
        $this->datagrid = $datagrid;
        $this->records  = $records;
        $this->query    = $query;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * @return ResultRecordInterface[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param array $records
     */
    public function setRecords(array $records)
    {
        $this->records = $records;
    }

    /**
     * @return AbstractQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
