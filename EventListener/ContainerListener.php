<?php

namespace Pintushi\Bundle\GridBundle\EventListener;

use Pintushi\Bundle\GridBundle\Provider\ConfigurationProvider;
use Oro\Component\Config\Dumper\ConfigMetadataDumperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ContainerListener
{
    /** @var ConfigMetadataDumperInterface */
    private $dumper;

    /** @var ConfigurationProvider */
    private $configurationProvider;

    /**
     * @param ConfigMetadataDumperInterface $dumper
     * @param ContainerInterface            $container
     */
    public function __construct(ConfigMetadataDumperInterface $dumper, ConfigurationProvider $configurationProvider)
    {
        $this->dumper = $dumper;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * Executes event on kernel request
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest() && !$this->dumper->isFresh()) {
            $container = new ContainerBuilder();
            $this->configurationProvider->loadConfiguration($container);
            $this->dumper->dump($container);
        }
    }
}
