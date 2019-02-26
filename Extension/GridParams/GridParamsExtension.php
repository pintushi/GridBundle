<?php

namespace Pintushi\Bundle\GridBundle\Extension\GridParams;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;

/**
 * @TODO: should be refactored in BAP-6849
 */
class GridParamsExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function isApplicable(DatagridConfiguration $config)
    {
        return
            parent::isApplicable($config)
            && $config->isOrmDatasource();
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataObject $data)
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
