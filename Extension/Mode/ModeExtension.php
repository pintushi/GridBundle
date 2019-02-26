<?php

namespace Pintushi\Bundle\GridBundle\Extension\Mode;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;

class ModeExtension extends AbstractExtension
{
    const MODE_OPTION_PATH = '[options][mode]';

    const MODE_SERVER = 'server';
    const MODE_CLIENT = 'client';

    /**
     * {@inheritdoc}
     */
    public function isApplicable(DatagridConfiguration $config)
    {
        return
            parent::isApplicable($config)
            && $this->getMode($config) !== self::MODE_SERVER;
    }

    /**
     * {@inheritdoc}
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataObject $data)
    {
        $data->offsetSetByPath('mode', $this->getMode($config));
    }

    /**
     * @param DatagridConfiguration $config
     * @return string|null
     */
    protected function getMode(DatagridConfiguration $config)
    {
        return $config->offsetGetByPath(self::MODE_OPTION_PATH, self::MODE_SERVER);
    }
}
