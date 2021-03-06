<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ColumnsPass implements CompilerPassInterface
{
    const FORMATTER_EXTENSION_ID = 'pintushi_grid.extension.columns';
    const TAG_NAME               = 'pintushi_grid.extension.columns.column';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * Find and add available properties to collumn extension
         */
        $extension = $container->getDefinition(self::FORMATTER_EXTENSION_ID);
        if ($extension) {
            $properties = $container->findTaggedServiceIds(self::TAG_NAME);
            foreach ($properties as $serviceId => $tags) {
                if ($container->hasDefinition($serviceId)) {
                    $container->getDefinition($serviceId)->setPublic(false);
                }
                $tagAttrs = reset($tags);
                $extension->addMethodCall('registerColumn', [$tagAttrs['type'], new Reference($serviceId)]);
            }
        }
    }
}
