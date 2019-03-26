<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const ACTIONS_KEY = 'actions';

    const ACL_KEY = 'acl_resource';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root(self::ACTIONS_KEY)
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('label')->end()
                        ->scalarNode('icon')->end()
                        ->scalarNode(self::ACL_KEY)->end()
                        ->booleanNode('rowAction')->defaultValue(false)->end()
                        ->arrayNode('link')
                            ->children()
                                ->scalarNode('route')->end()
                                ->arrayNode('params')
                                    ->performNoDeepMerging()
                                    ->variablePrototype()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $builder;
    }
}
