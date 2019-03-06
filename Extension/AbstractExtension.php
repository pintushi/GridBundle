<?php

namespace Pintushi\Bundle\GridBundle\Extension;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
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
     * Full list of grid modes
     * @see \Pintushi\Bundle\GridBundle\Provider\GridModeProvider
     *
     * @var array of modes that are not supported by this extension
     */
    protected $excludedModes = [];

    /**
     * {@inheritDoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        return $this->isExtensionSupportedGridModes();
    }

    /**
     * {@inheritDoc}
     */
    public function processConfigs(GridConfiguration $config)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitDatasource(GridConfiguration $config, DatasourceInterface $datasource)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(GridConfiguration $config, ResultsObject $result)
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
     * Checking that extension is supported all grid modes.
     *
     * @return bool
     */
    private function isExtensionSupportedGridModes()
    {
        $gridModes = $this->getParameters()->get(ParameterBag::DATAGRID_MODES_PARAMETER, []);
        if ([] === $gridModes || [] === $this->excludedModes) {
            return true;
        }

        return empty(array_intersect($this->excludedModes, $gridModes));
    }
}
