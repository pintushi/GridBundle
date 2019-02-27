<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PintushiGridExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('extensions.yml');
        $loader->load('data_sources.yml');
        $loader->load('formatters.yml');
        $loader->load('actions.yml');
        $loader->load('mass_actions.yml');
        $loader->load('datagrid_state.yml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.yml');
        }

        if ($container->getParameter('kernel.environment') === 'test') {
            // $loader->load('services_test.yml');
        }

        $container->prependExtensionConfig($this->getAlias(), array_intersect_key($config, array_flip(['settings'])));
    }
}
