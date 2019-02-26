<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Widget;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;

class WindowMassAction extends WidgetMassAction
{
    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_handle'])) {
            $options['frontend_handle'] = 'dialog';
        }

        return parent::setOptions($options);
    }
}
