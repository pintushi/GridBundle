<?php

namespace Pintushi\Bundle\GridBundle\Extension;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;

interface ExtensionVisitorInterface
{
    /**
     * Checks if extensions should be applied to grid
     *
     * @param GridConfiguration $config
     *
     * @return bool
     */
    public function isApplicable(GridConfiguration $config);

    /**
     * Process configuration object
     * Validation and passing default values goes here
     *
     * @param GridConfiguration $config
     *
     * @return void
     */
    public function processConfigs(GridConfiguration $config);

    /**
     * Apply changes provided by applied extensions on datasource
     *
     * @param GridConfiguration $config
     * @param DatasourceInterface   $datasource
     *
     * @return mixed
     */
    public function visitDatasource(GridConfiguration $config, DatasourceInterface $datasource);

    /**
     * Apply changes provided by applied extensions on result data
     *
     * @param GridConfiguration $config
     * @param ResultsObject         $result
     *
     * @return mixed
     */
    public function visitResult(GridConfiguration $config, ResultsObject $result);

    /**
     * Apply changes provided by applied extensions on metadata
     *
     * @param GridConfiguration $config
     * @param MetadataObject        $data
     *
     * @return mixed
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data);

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
