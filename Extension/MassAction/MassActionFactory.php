<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Exception\RuntimeException;
use Pintushi\Bundle\GridBundle\Extension\Action\ActionFactory;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\MassActionInterface;

class MassActionFactory extends ActionFactory
{
    /**
     * Creates an action object.
     *
     * @param string $actionName
     * @param array  $actionConfig
     *
     * @return MassActionInterface
     *
     * @throws RuntimeException if the requested action has invalid configuration
     */
    public function createAction($actionName, array $actionConfig)
    {
        $action = parent::createAction($actionName, $actionConfig);
        if (!$action instanceof MassActionInterface) {
            throw new RuntimeException(
                sprintf(
                    'An action should be an instance of "%s", got "%s".',
                    MassActionInterface::class,
                    get_class($action)
                )
            );
        }

        return $action;
    }
}
