<?php

namespace Pintushi\Bundle\GridBundle\Datasource;

use Pintushi\Bundle\GridBundle\Grid\GridInterface;

/**
 * Binds parameters of grid to it's datasource
 */
interface ParameterBinderInterface
{
    /**
     * Binds grid parameters to datasource.
     *
     * Example of usage:
     * <code>
     *  // get parameter "name" from grid parameter bag and add it to datasource
     *  $queryParameterBinder->bindParameters($grid, ['name']);
     *
     *  // get parameter "id" from grid parameter bag and add it to datasource as parameter "client_id"
     *  $queryParameterBinder->bindParameters($grid, ['client_id' => 'id']);
     *
     *  // get parameter "email" from grid parameter bag and add it to datasource, all other existing
     *  // parameters will be cleared
     *  $queryParameterBinder->bindParameters($grid, ['email'], false);
     * </code>
     *
     *
     * @param GridInterface $grid
     * @param array $datasourceToGridParameters
     * @param bool $append
     */
    public function bindParameters(
        GridInterface $grid,
        array $datasourceToGridParameters,
        $append = true
    );
}
