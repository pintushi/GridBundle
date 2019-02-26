<?php

namespace Pintushi\Bundle\GridBundle\Extension\Feature;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmDatasource;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Oro\Bundle\FeatureToggleBundle\Checker\FeatureChecker;
use Oro\Component\DoctrineUtils\ORM\QueryBuilderUtil;

class FeatureExtension extends AbstractExtension
{
    /** @var FeatureChecker */
    protected $featureChecker;

    /**
     * @param FeatureChecker $featureChecker
     */
    public function __construct(FeatureChecker $featureChecker)
    {
        $this->featureChecker = $featureChecker;
    }

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
     * {@inheritdoc}
     */
    public function processConfigs(DatagridConfiguration $config)
    {
        $config->offsetSetByPath(
            '[features]',
            $this->validateConfiguration(
                new Configuration(),
                ['features' => $config->offsetGetByPath('[features]')]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visitDatasource(DatagridConfiguration $config, DatasourceInterface $datasource)
    {
        /** @var OrmDatasource $datasource */

        $dataName = $config->offsetGetByPath('[features][entity_class_name_path]');
        if (!$dataName) {
            return;
        }

        $qb = $datasource->getQueryBuilder();

        $excludedEntities = $this->featureChecker->getDisabledResourcesByType('entities');
        if (!$excludedEntities) {
            return;
        }

        $excludedEntitiesParam = QueryBuilderUtil::generateParameterName('excluded_entities');
        $qb
            ->andWhere($qb->expr()->notIn($dataName, sprintf(':%s', $excludedEntitiesParam)))
            ->setParameter($excludedEntitiesParam, $excludedEntities);
    }
}
