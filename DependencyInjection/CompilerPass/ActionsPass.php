<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection\CompilerPass;

use Oro\Component\DependencyInjection\Compiler\TaggedServicesCompilerPassTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Pintushi\Bundle\GridBundle\Extension\Action\Actions\ActionInterface;

class ActionsPass implements CompilerPassInterface
{
    use TaggedServicesCompilerPassTrait;

    const ACTION_FACTORY_SERVICE_ID = 'pintushi_grid.extension.action.factory';
    const ACTION_TAG_NAME           = 'pintushi_grid.extension.action.type';

    const ACTION_EXTENSION_SERVICE_ID = 'pintushi_grid.extension.action';
    const ACTION_PROVIDER_TAG         = 'pintushi_grid.extension.action.provider';

    const MASS_ACTION_FACTORY_SERVICE_ID = 'pintushi_grid.extension.mass_action.factory';
    const MASS_ACTION_TAG_NAME           = 'pintushi_grid.extension.mass_action.type';

    const ITERABLE_RESULT_FACTORY_REGISTRY_SERVICE_ID =
        'pintushi_grid.extension.mass_action.iterable_result_factory_registry';

    const ITERABLE_RESULT_FACTORY_TAG_NAME = 'pintushi_grid.extension.mass_action.iterable_result_factory';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerActions(
            $container,
            self::ACTION_FACTORY_SERVICE_ID,
            self::ACTION_TAG_NAME
        );
        $this->registerTaggedServices(
            $container,
            self::ACTION_EXTENSION_SERVICE_ID,
            self::ACTION_PROVIDER_TAG,
            'addActionProvider'
        );

        $this->registerTaggedServices(
            $container,
            self::ITERABLE_RESULT_FACTORY_REGISTRY_SERVICE_ID,
            self::ITERABLE_RESULT_FACTORY_TAG_NAME,
            'addFactory'
        );

        $this->registerActions(
            $container,
            self::MASS_ACTION_FACTORY_SERVICE_ID,
            self::MASS_ACTION_TAG_NAME
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $actionFactoryServiceId
     * @param string           $actionTagName
     */
    protected function registerActions(ContainerBuilder $container, $actionFactoryServiceId, $actionTagName)
    {
        $container
            ->registerForAutoconfiguration(ActionInterface::class)
            ->setPublic(true)
        ;

        $actionFactoryDef = $container->getDefinition($actionFactoryServiceId);
        $actions = $container->findTaggedServiceIds($actionTagName);
        foreach ($actions as $serviceId => $tags) {
            $actionDef = $container->getDefinition($serviceId);
            if ($actionDef->isShared()) {
                throw new RuntimeException(sprintf('The service "%s" should not be shared.', $serviceId));
            }
            foreach ($tags as $tag) {
                $actionFactoryDef->addMethodCall('registerAction', [$tag['type'], $serviceId]);
            }
        }
    }
}
