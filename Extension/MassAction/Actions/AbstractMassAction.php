<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction\Actions;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\Action\Actions\AbstractAction;
use Pintushi\Bundle\GridBundle\Extension\MassAction\MassActionExtension;
use Symfony\Component\HttpFoundation\Request;

class AbstractMassAction extends AbstractAction implements MassActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (empty($options['frontend_type'])) {
            $options['frontend_type'] = 'mass';
        }

        if (!empty($options['icon'])) {
            $options['launcherOptions'] = [
                'iconClassName' => 'fa-' . $options['icon']
            ];
            unset($options['icon']);
        }

        if (empty($options[MassActionExtension::ALLOWED_REQUEST_TYPES])) {
            $options[MassActionExtension::ALLOWED_REQUEST_TYPES] = $this->getAllowedRequestTypes();
        }

        if (empty($options['requestType'])) {
            $options['requestType'] = $this->getRequestType();
        }

        return parent::setOptions($options);
    }

    /**
     * @return array
     */
    protected function getAllowedRequestTypes()
    {
        return [Request::METHOD_GET];
    }

    /**
     * @return string
     */
    protected function getRequestType()
    {
        return Request::METHOD_GET;
    }
}
