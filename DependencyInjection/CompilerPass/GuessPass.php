<?php

namespace Pintushi\Bundle\GridBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GuessPass implements CompilerPassInterface
{
    const COLUMN_OPTIONS_GUESSER_ID       = 'pintushi_grid.grid.guesser';
    const COLUMN_OPTIONS_GUESSER_TAG_NAME = 'pintushi_grid.column_options_guesser';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::COLUMN_OPTIONS_GUESSER_ID)) {
            $serviceDef = $container->getDefinition(self::COLUMN_OPTIONS_GUESSER_ID);
            $guessers = array_keys($container->findTaggedServiceIds(self::COLUMN_OPTIONS_GUESSER_TAG_NAME));
            $serviceDef->replaceArgument(1, $guessers);
        }
    }
}
