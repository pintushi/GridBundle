<?php

namespace Pintushi\Bundle\GridBundle\Cache;

use Pintushi\Bundle\GridBundle\Provider\ConfigurationProvider;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class GridConfigurationCacheWarmer implements CacheWarmerInterface
{
    /** @var ConfigurationProvider */
    protected $configurationProvider;

    /**
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $this->configurationProvider->loadConfiguration();
    }
}
