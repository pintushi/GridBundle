<?php

namespace Pintushi\Bundle\GridBundle\Tools;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
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
     * @param GridConfiguration $config
     *
     * @return string|null
     * @deprecated since 2.0. Use config->getOrmQuery()->getRootEntity(entityClassResolver, true) instead
     */
    public function getEntity(GridConfiguration $config)
    {
        $entityClassName = $config->offsetGetOr('extended_entity_name');
        if ($entityClassName) {
            return $entityClassName;
        }

        return $config->getOrmQuery()->getRootEntity($this->entityClassResolver);
    }

    /**
     * @param GridConfiguration $config
     *
     * @return null
     * @deprecated since 2.0. Use config->getOrmQuery()->getRootAlias() instead
     */
    public function getEntityRootAlias(GridConfiguration $config)
    {
        return $config->getOrmQuery()->getRootAlias();
    }
}
