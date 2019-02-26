<?php

namespace Pintushi\Bundle\GridBundle\Tools;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\EntityBundle\ORM\EntityClassResolver;

/**
 * @deprecated since 2.0. Use config->getOrmQuery()->getRootEntity() and config->getOrmQuery()->getRootAlias() instead
 */
class GridConfigurationHelper
{
    /** @var EntityClassResolver */
    protected $entityClassResolver;

    /**
     * @param EntityClassResolver $entityClassResolver
     */
    public function __construct(EntityClassResolver $entityClassResolver)
    {
        $this->entityClassResolver = $entityClassResolver;
    }

    /**
     * @param DatagridConfiguration $config
     *
     * @return string|null
     * @deprecated since 2.0. Use config->getOrmQuery()->getRootEntity(entityClassResolver, true) instead
     */
    public function getEntity(DatagridConfiguration $config)
    {
        $entityClassName = $config->offsetGetOr('extended_entity_name');
        if ($entityClassName) {
            return $entityClassName;
        }

        return $config->getOrmQuery()->getRootEntity($this->entityClassResolver);
    }

    /**
     * @param DatagridConfiguration $config
     *
     * @return null
     * @deprecated since 2.0. Use config->getOrmQuery()->getRootAlias() instead
     */
    public function getEntityRootAlias(DatagridConfiguration $config)
    {
        return $config->getOrmQuery()->getRootAlias();
    }
}
