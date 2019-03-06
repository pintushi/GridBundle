<?php

namespace Pintushi\Bundle\GridBundle\Datasource;

use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Pagerfanta\Pagerfanta;

interface DatasourceInterface
{
    /**
     * Add source to grid
     *
     * @param GridInterface $grid
     * @param array             $config
     */
    public function process(GridInterface $grid, array $config);

    /**
     * Returns data extracted via datasource
     *
     * @return Pagerfanta
     */
    public function getData();
}
