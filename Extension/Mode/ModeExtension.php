<?php

namespace Pintushi\Bundle\GridBundle\Extension\Mode;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;

class ModeExtension extends AbstractExtension
{
    const MODE_OPTION_PATH = '[options][mode]';

    const MODE_SERVER = 'server';
    const MODE_CLIENT = 'client';

    /**
     * {@inheritdoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        return
            parent::isApplicable($config)
            && $this->getMode($config) !== self::MODE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        $data->offsetSetByPath('mode', $this->getMode($config));
    }

    /**
     * @param GridConfiguration $config
     * @return string|null
     */
    protected function getMode(GridConfiguration $config)
    {
        return $config->offsetGetByPath(self::MODE_OPTION_PATH, self::MODE_SERVER);
    }
}
