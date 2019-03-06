<?php

namespace Pintushi\Bundle\GridBundle\Grid\Common;

use Oro\Component\Config\Common\ConfigObject;
use Pagerfanta\Pagerfanta;
use Hateoas\Configuration\Route;

class ResultsObject extends ConfigObject
{
    /**
     * Path to total records parameter
     */
    const ROUTE_PATH = '[route]';

    /**
     * Path tp results data
     */
    const DATA_PATH = '[data]';

    /**
     * @return Route
     */
    public function getRoute()
    {
        return (int)$this->offsetGetByPath(self::ROUTE_PATH, null);
    }

    /**
     *
     * @param  Route $route
     * @return $this
     */
    public function setRoute(Route $route)
    {
        return $this->offsetSetByPath(self::ROUTE_PATH, $route);
    }

    /**
     * Gets data rows from results object
     *
     * @return Pagerfanta
     */
    public function getData()
    {
        return $this->offsetGetByPath(self::DATA_PATH, null);
    }

    /**
     * Gets data rows from results object
     *
     * @param Pagerfanta $paginator
     * @return $this
     */
    public function setData(Pagerfanta $paginator)
    {
        return $this->offsetSetByPath(self::DATA_PATH, $paginator);
    }
}
