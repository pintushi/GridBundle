<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Widget;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\AbstractMassAction;

class WidgetMassAction extends AbstractMassAction
{
    /** @var array */
    protected $requiredOptions = ['route', 'frontend_type'];

    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_options'])) {
            $options['frontend_options'] = [];
        }
        if (empty($options['route_parameters'])) {
            $options['route_parameters'] = [];
        }

        return parent::setOptions($options);
    }
}
