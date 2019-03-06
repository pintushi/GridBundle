<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection\CompilerPass;

use Pintushi\Bundle\GridBundle\Event as GridEvent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Marks event listener services for all data grids as "lazy"
 * to prevent loading of services used by them on each request if Symfony Profiler is enabled.
 * The loading of all event listeners is triggered by Symfony's EventDataCollector.
 */
class SetGridEventListenersLazyPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('profiler')) {
            // the Symfony Profiler is disabled
            return;
        }

        $gridEvents = [
            GridEvent\PreBuild::NAME,
            GridEvent\BuildAfter::NAME,
            GridEvent\BuildBefore::NAME,
            GridEvent\OrmResultBefore::NAME,
            GridEvent\OrmResultAfter::NAME,
            GridEvent\GridViewsLoadEvent::EVENT_NAME
        ];

        $gridEventListeners = [];
        $eventListeners         = $container->findTaggedServiceIds('kernel.event_listener');
        foreach ($eventListeners as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['event'])
                    && 0 === strpos($tag['event'], 'pintushi_grid')
                    && !in_array($tag['event'], $gridEvents, true)
                ) {
                    $gridEventListeners[] = $serviceId;
                }
            }
        }
        $gridEventListeners = array_unique($gridEventListeners);
        foreach ($gridEventListeners as $serviceId) {
            if ($container->hasDefinition($serviceId)) {
                $serviceDef = $container->getDefinition($serviceId);
                if (!$serviceDef->isLazy()) {
                    $serviceDef->setLazy(true);
                }
            }
        }
    }
}
