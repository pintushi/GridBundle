<?php

namespace Pintushi\Bundle\GridBundle\Datagrid;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Datagrid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Extension\Acceptor;

interface DatagridInterface
{
    /**
     * Returns datagrid name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns datagrid scope
     *
     * @return string
     */
    public function getScope();

    /**
     * Set scope of datagrid
     *
     * @param string $scope
     *
     * @return $this
     */
    public function setScope($scope);

    /**
     * Set grid datasource
     *
     * @param DatasourceInterface $source
     *
     * @return $this
     */
    public function setDatasource(DatasourceInterface $source);

    /**
     * Returns datasource object
     *
     * @return DatasourceInterface
     */
    public function getDatasource();

    /**
     * Returns datasource object accepted by extensions
     *
     * @return DatasourceInterface
     */
    public function getAcceptedDatasource();

    /**
     * Apply accepted extensions to datasource object
     *
     * @return $this
     */
    public function acceptDatasource();

    /**
     * Getter for acceptor object
     *
     * @return Acceptor
     */
    public function getAcceptor();

    /**
     * Setter for acceptor object
     *
     * @param Acceptor $acceptor
     *
     * @return $this
     */
    public function setAcceptor(Acceptor $acceptor);

    /**
     * Converts datasource into the result array
     * return ResultsObject
     *    'data' => converted source
     *    ....   => some additional info added by extensions
     * )
     *
     * @return ResultsObject
     */
    public function getData();

    /**
     * Retrieve metadata from all extensions
     * Metadata needed to create view layer (can be lazy - so additional request is required)
     *
     * @return MetadataObject
     */
    public function getMetadata();

    /**
     * Returns the same as getMetadata with resolved lazy stuff
     *
     * @return MetadataObject
     */
    public function getResolvedMetadata();

    /**
     * Returns parameters
     *
     * @return ParameterBag
     */
    public function getParameters();

    /**
     * Get config object
     *
     * @return DatagridConfiguration
     */
    public function getConfig();
}
