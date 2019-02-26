<?php

namespace Pintushi\Bundle\GridBundle\Extension;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Datagrid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

abstract class AbstractExtension implements ExtensionVisitorInterface
{
    /**
     * @var ParameterBag
     */
    protected $parameters;

    /**
     * Full list of datagrid modes
     * @see \Pintushi\Bundle\GridBundle\Provider\DatagridModeProvider
     *
     * @var array of modes that are not supported by this extension
     */
    protected $excludedModes = [];

    /**
     * {@inheritDoc}
     */
    public function isApplicable(DatagridConfiguration $config)
    {
        return $this->isExtensionSupportedDatagridModes();
    }

    /**
     * {@inheritDoc}
     */
    public function processConfigs(DatagridConfiguration $config)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitDatasource(DatagridConfiguration $config, DatasourceInterface $datasource)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataObject $data)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(DatagridConfiguration $config, ResultsObject $result)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        // default priority if not overridden by child
        return 0;
    }

    /**
     * Validate configuration
     *
     * @param ConfigurationInterface      $configuration
     * @param                             $config
     *
     * @return array
     */
    protected function validateConfiguration(ConfigurationInterface $configuration, $config)
    {
        $processor = new Processor();
        return $processor->processConfiguration(
            $configuration,
            $config
        );
    }

    /**
     * Getter for parameters
     *
     * @return ParameterBag
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set instance of parameters used for current grid
     *
     * @param ParameterBag $parameters
     */
    public function setParameters(ParameterBag $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Checking that extension is supported all datagrid modes.
     *
     * @return bool
     */
    private function isExtensionSupportedDatagridModes()
    {
        $datagridModes = $this->getParameters()->get(ParameterBag::DATAGRID_MODES_PARAMETER, []);
        if ([] === $datagridModes || [] === $this->excludedModes) {
            return true;
        }

        return empty(array_intersect($this->excludedModes, $datagridModes));
    }
}
