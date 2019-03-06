<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEFAULT_TYPE   = 'string';

    const TYPE_KEY       = 'type';
    const COLUMNS_KEY    = 'columns';

    /** @var array */
    protected $types;

    /**
     * @param        $types
     * @param string $root
     */
    public function __construct($types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder->root(self::COLUMNS_KEY)
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->ignoreExtraKeys()
                ->children()
                    ->scalarNode(self::TYPE_KEY)
                        ->defaultValue(self::DEFAULT_TYPE)
                        ->validate()
                        ->ifNotInArray($this->types)
                            ->thenInvalid('Invalid property type "%s"')
                        ->end()
                    ->end()
                    // if "property_path" is not specified a field name is used
                    ->scalarNode(ColumnInterface::DATA_PATH_KEY)->end()
                    // just validate type if node exist
                    ->scalarNode('label')->end()
                    ->booleanNode('editable')->defaultFalse()->end()
                    ->booleanNode(ColumnInterface::TRANSLATABLE_KEY)->defaultTrue()->end()
                    ->booleanNode('renderable')->end()
                    ->scalarNode('order')->end()
                    ->booleanNode('required')->end()
                ->end()
            ->end();

        return $builder;
    }
}
