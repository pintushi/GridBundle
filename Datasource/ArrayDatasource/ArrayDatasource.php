<?php

namespace Pintushi\Bundle\GridBundle\Datasource\ArrayDatasource;

use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\ResultRecord;

/**
 * This datasource allows to create datagrids from plain PHP arrays
 * To use it, you need to configure datagrid with datasource type: array
 * And then set source array via grid listener
 */
class ArrayDatasource implements DatasourceInterface
{
    const TYPE = 'array';

    /**
     * @var array
     */
    protected $arraySource = [];

    /** {@inheritdoc} */
    public function process(DatagridInterface $grid, array $config)
    {
        $grid->setDatasource(clone $this);
    }

    /** {@inheritdoc} */
    public function getResults()
    {
        $rows = [];
        foreach ($this->arraySource as $result) {
            $rows[] = new ResultRecord($result);
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function getArraySource()
    {
        return $this->arraySource;
    }

    /**
     * @param array $source
     */
    public function setArraySource(array $source)
    {
        $this->arraySource = $source;
    }
}
