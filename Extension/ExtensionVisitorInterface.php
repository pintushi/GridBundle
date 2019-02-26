<?php

namespace Pintushi\Bundle\GridBundle\Extension;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Datagrid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;

interface ExtensionVisitorInterface
{
    /**
     * Checks if extensions should be applied to grid
     *
     * @param DatagridConfiguration $config
     *
     * @return bool
     */
    public function isApplicable(DatagridConfiguration $config);

    /**
     * Process configuration object
     * Validation and passing default values goes here
     *
     * @param DatagridConfiguration $config
     *
     * @return void
     */
    public function processConfigs(DatagridConfiguration $config);

    /**
     * Apply changes provided by applied extensions on datasource
     *
     * @param DatagridConfiguration $config
     * @param DatasourceInterface   $datasource
     *
     * @return mixed
     */
    public function visitDatasource(DatagridConfiguration $config, DatasourceInterface $datasource);

    /**
     * Apply changes provided by applied extensions on result data
     *
     * @param DatagridConfiguration $config
     * @param ResultsObject         $result
     *
     * @return mixed
     */
    public function visitResult(DatagridConfiguration $config, ResultsObject $result);

    /**
     * Apply changes provided by applied extensions on metadata
     *
     * @param DatagridConfiguration $config
     * @param MetadataObject        $data
     *
     * @return mixed
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataObject $data);

    /**
     * Set instance of parameters used for current grid
     *
     * @param ParameterBag $parameters
     */
    public function setParameters(ParameterBag $parameters);

    /**
     * Returns priority needed for applying
     * Format from -255 to 255
     *
     * @return int
     */
    public function getPriority();
}
