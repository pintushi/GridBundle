<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Ajax;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Symfony\Component\HttpFoundation\Request;

class DeleteMassAction extends AjaxMassAction
{
    /** @var array */
    protected $requiredOptions = ['handler', 'entity_name', 'data_identifier'];

    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['handler'])) {
            $options['handler'] = 'pintushi_grid.extension.mass_action.handler.delete';
        }

        if (empty($options['frontend_type'])) {
            $options['frontend_type'] = 'delete-mass';
        }

        return parent::setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedRequestTypes()
    {
        return [Request::METHOD_POST, Request::METHOD_DELETE];
    }
}
