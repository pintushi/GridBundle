<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\Ajax;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\AbstractMassAction;
use Symfony\Component\HttpFoundation\Request;

class AjaxMassAction extends AbstractMassAction
{
    /** @var array */
    protected $requiredOptions = ['handler'];

    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_handle'])) {
            $options['frontend_handle'] = 'ajax';
        }

        if (empty($options['route'])) {
            $options['route'] = 'pintushi_grid_mass_action';
        }

        if (empty($options['route_parameters'])) {
            $options['route_parameters'] = [];
        }

        if (!isset($options['confirmation'])) {
            $options['confirmation'] = true;
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

    /**
     * {@inheritdoc}
     */
    protected function getRequestType()
    {
        return Request::METHOD_POST;
    }
}
