<?php

namespace Pintushi\Bundle\GridBundle\Provider;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;

/**
 * Provides an interface for classes responsible to load grid configuration
 */
interface ConfigurationProviderInterface
{
    /**
     * Checks if this provider can be used to load configuration of a grid with the given name
     *
     * @param string $gridName The name of a grid
     *
     * @return bool
     */
    public function isApplicable($gridName);

    /**
     * Returns prepared config for requested grid
     *
     * @param string $gridName The name of a grid
     *
     * @return GridConfiguration
     * @throws \RuntimeException in case when grid configuration not found
     */
    public function getConfiguration($gridName);
}
