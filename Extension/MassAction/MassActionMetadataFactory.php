<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionMetadataFactory;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\MassActionInterface;

class MassActionMetadataFactory
{
    /** @var ActionMetadataFactory */
    private $actionMetadataFactory;

    /**
     * @param ActionMetadataFactory $actionMetadataFactory
     */
    public function __construct(ActionMetadataFactory $actionMetadataFactory)
    {
        $this->actionMetadataFactory = $actionMetadataFactory;
    }

    /**
     * Creates metadata for the given action.
     *
     * @param MassActionInterface $action
     *
     * @return array
     */
    public function createActionMetadata(MassActionInterface $action)
    {
        return $this->actionMetadataFactory->createActionMetadata($action);
    }
}
