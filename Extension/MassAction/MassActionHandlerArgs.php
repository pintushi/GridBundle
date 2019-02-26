<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\IterableResultInterface;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\MassActionInterface;

class MassActionHandlerArgs
{
    /** @var MassActionInterface */
    protected $massAction;

    /** @var \Iterator */
    protected $results;

    /** @var array */
    protected $data;

    /** @var DatagridInterface|null */
    protected $datagrid;

    /**
     * @param MassActionInterface     $massAction
     * @param DatagridInterface       $datagrid
     * @param IterableResultInterface $results
     * @param array                   $data
     */
    public function __construct(
        MassActionInterface $massAction,
        DatagridInterface $datagrid,
        IterableResultInterface $results,
        array $data = []
    ) {
        $this->massAction = $massAction;
        $this->results    = $results;
        $this->data       = $data;
        $this->datagrid   = $datagrid;
    }

    /**
     * @return MassActionInterface
     */
    public function getMassAction()
    {
        return $this->massAction;
    }

    /**
     * @return IterableResultInterface
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }
}
