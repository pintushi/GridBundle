<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection;

use Pintushi\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('pintushi_grid');

        SettingsBuilder::append(
            $rootNode,
            [
                'default_per_page' => ['value' => 25],
                'full_screen_layout_enabled' => ['type' => 'boolean', 'value' => true],
            ]
        );

        return $treeBuilder;
    }
}
