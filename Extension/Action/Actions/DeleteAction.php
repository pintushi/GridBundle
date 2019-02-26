<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action\Actions;

use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;

class DeleteAction extends AbstractAction
{
    /**
     * @var array
     */
    protected $requiredOptions = ['link'];

    /**
     * @param ActionConfiguration $options
     */
    public function setOptions(ActionConfiguration $options)
    {
        if (!isset($options['confirmation'])) {
            $options['confirmation'] = true;
        }

        parent::setOptions($options);
    }
}
