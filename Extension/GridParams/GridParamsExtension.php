<?php

namespace Pintushi\Bundle\GridBundle\Extension\GridParams;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;

/**
 * @TODO: should be refactored in BAP-6849
 */
class GridParamsExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        return
            parent::isApplicable($config)
            && $config->isOrmDatasource();
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        $params = $this->getParameters()->all();
        $gridParams = array_filter(
            $params,
            function ($param) {
                return !is_array($param) && !is_null($param);
            }
        );

        $data->offsetAddToArray('gridParams', $gridParams);
    }
}
