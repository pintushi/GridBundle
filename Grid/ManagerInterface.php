<?php

namespace Pintushi\Bundle\GridBundle\Grid;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Exception\RuntimeException;

interface ManagerInterface
{
    /**
     * Returns prepared grid object for further operations.
     *
     * @param string $name Unique name of grid, optionally with scope ("grid-name:grid-scope")
     * @param ParameterBag|array|null $parameters
     * @param array $additionalParameters
     * @return GridInterface
     */
    public function getGrid($name, $parameters = null, array $additionalParameters = []);

    /**
     * Returns prepared grid object for further operations based on parameters from request.
     *
     * @param string $name Unique name of grid, optionally with scope ("grid-name:grid-scope")
     * @param array $additionalParameters Additional params that will be merged with request params to use in grid
     * @return GridInterface
     */
    public function getGridByRequestParams($name, array $additionalParameters = []);

    /**
     * Returns prepared config for requested grid
     *
     * @param string $name Unique name of grid, optionally with scope ("grid-name:grid-scope")
     * @return GridConfiguration
     * @throws RuntimeException If grid configuration not found
     */
    public function getConfigurationForGrid($name);
}
