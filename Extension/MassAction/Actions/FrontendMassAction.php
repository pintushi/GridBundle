<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;

class FrontendMassAction extends AbstractMassAction
{
    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_type'])) {
            $options['frontend_type'] = 'frontend-mass';
        }

        return parent::setOptions($options);
    }
}
