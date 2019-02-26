<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Widget\MassEdit;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Widget\WindowMassAction;
use Symfony\Component\HttpFoundation\Request;

class MassEditAction extends WindowMassAction
{
    /** @var array */
    protected $requiredOptions = ['handler', 'route', 'data_identifier'];

    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_type'])) {
            $options['frontend_type'] = 'edit-mass';
        }

        return parent::setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAllowedRequestTypes()
    {
        return [Request::METHOD_POST];
    }
}
