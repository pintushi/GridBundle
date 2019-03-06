<?php

namespace Pintushi\Bundle\GridBundle\Datasource\ArrayDatasource;

use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * This datasource allows to create grids from plain PHP arrays
 * To use it, you need to configure grid with datasource type: array
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
    public function process(GridInterface $grid, array $config)
    {
        $grid->setDatasource(clone $this);
    }

    /** {@inheritdoc} */
    public function getData()
    {
        return new Pagerfanta(new ArrayAdapter($this->arraySource));
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
