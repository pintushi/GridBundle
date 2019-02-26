<?php

namespace Pintushi\Bundle\GridBundle;

use Pintushi\Bundle\GridBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Adds compiler passes required by PintushiGridBundle.
 */
class PintushiGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CompilerPass\ConfigurationPass());
        $container->addCompilerPass(new CompilerPass\FormattersPass());
        $container->addCompilerPass(new CompilerPass\ActionsPass());
        $container->addCompilerPass(new CompilerPass\GuessPass());
        $container->addCompilerPass(new CompilerPass\InlineEditColumnOptionsGuesserPass());
        $container->addCompilerPass(new CompilerPass\SetDatagridEventListenersLazyPass());
        $container->addCompilerPass(new CompilerPass\BoardProcessorsPass());
        $container->addCompilerPass(new CompilerPass\SelectedFieldsProvidersPass());
    }
}
