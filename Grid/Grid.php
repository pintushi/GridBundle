<?php

namespace Pintushi\Bundle\GridBundle\Grid;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Extension\Acceptor;
use Pagerfanta\Pagerfanta;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Configuration\Route;

class Grid implements GridInterface
{
    /** @var DatasourceInterface */
    protected $datasource;

    /** @var string */
    protected $name;

    /** @var string */
    protected $scope;

    /** @var GridConfiguration */
    protected $config;

    /** @var ParameterBag */
    protected $parameters;

    /** @var Acceptor */
    protected $acceptor;

    /** @var ResultsObject */
    protected $cachedResult = null;

    /**
     * @param string                $name
     * @param GridConfiguration $config
     * @param ParameterBag          $parameters
     */
    public function __construct($name, GridConfiguration $config, ParameterBag $parameters)
    {
        $this->name       = $name;
        $this->config     = $config;
        $this->parameters = $parameters;

        $this->initialize();
    }

    /**
     * Performs an initialization of a data grid.
     * You can override this method to perform modifications of grid configuration
     * based on grid parameters.
     */
    public function initialize()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        if ($this->cachedResult !== null) {
            return $this->cachedResult;
        }
        /** @var ResultsObject*/
        $this->cachedResult = ResultsObject::create(['data' => $this->getAcceptedDatasource()->getData()]);

        $this->acceptor->acceptResult($this->cachedResult);

        return $this->cachedResult;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata()
    {
        $data = MetadataObject::createNamed($this->getName(), []);
        $this->acceptor->acceptMetadata($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getResolvedMetadata()
    {
        $data = MetadataObject::createNamed($this->getName(), [MetadataObject::LAZY_KEY => false]);
        $this->acceptor->acceptMetadata($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function setDatasource(DatasourceInterface $source)
    {
        $this->datasource = $source;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * {@inheritDoc}
     */
    public function getAcceptedDatasource()
    {
        $this->acceptDatasource();

        return $this->getDatasource();
    }

    /**
     * {@inheritDoc}
     */
    public function acceptDatasource()
    {
        $this->acceptor->acceptDatasource($this->getDatasource());

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAcceptor()
    {
        return $this->acceptor;
    }

    /**
     * {@inheritDoc}
     */
    public function setAcceptor(Acceptor $acceptor)
    {
        $this->acceptor = $acceptor;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return $this->config;
    }
}
