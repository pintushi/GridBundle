<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

interface MassActionHandlerInterface
{
    /**
     * Handle mass action
     *
     * @param MassActionHandlerArgs $args
     *
     * @return MassActionResponseInterface
     */
    public function handle(MassActionHandlerArgs $args);
}
