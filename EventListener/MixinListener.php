<?php

namespace Pintushi\Bundle\GridBundle\EventListener;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Event\PreBuild;
use Pintushi\Bundle\GridBundle\Tools\MixinConfigurationHelper;

class MixinListener
{
    const MIXINS = 'mixins';

    const GRID_MIXIN = 'grid-mixin';

    /** @var MixinConfigurationHelper */
    protected $mixinConfigurationHelper;

    /** @var array */
    protected $appliedFor = [];

    /** @param MixinConfigurationHelper $mixinConfigurationHelper */
    public function __construct(MixinConfigurationHelper $mixinConfigurationHelper)
    {
        $this->mixinConfigurationHelper = $mixinConfigurationHelper;
    }

    /**
     * @param GridConfiguration $config
     * @param ParameterBag          $parameters
     *
     * @return bool
     */
    public function isApplicable(GridConfiguration $config, ParameterBag $parameters)
    {
        return $parameters->get(self::GRID_MIXIN, false) || $config->offsetGetOr(self::MIXINS, false);
    }

    /**
     * @param PreBuild $event
     */
    public function onPreBuild(PreBuild $event)
    {
        $parameters = $event->getParameters();
        $config     = $event->getConfig();
        if (!$this->isApplicable($config, $parameters)) {
            return;
        }

        $gridName = $config->getName();
        $mixins   = $this->getMixins($config, $parameters);
        foreach ($mixins as $mixin) {
            if (empty($this->appliedFor[$gridName . $mixin])) {
                $this->mixinConfigurationHelper->extendConfiguration($config, $mixin);
                $this->appliedFor[$gridName . $mixin] = true;
            }
        }
    }

    /**
     * @param GridConfiguration $config
     * @param ParameterBag          $parameters
     *
     * @return string[]
     */
    protected function getMixins(GridConfiguration $config, ParameterBag $parameters)
    {
        $mixins = (array)$config->offsetGetOr(self::MIXINS, []);

        $mixins = array_merge($mixins, (array)$parameters->get(self::GRID_MIXIN, []));

        return $mixins;
    }
}
