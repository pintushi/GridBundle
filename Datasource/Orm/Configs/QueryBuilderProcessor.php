<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm\Configs;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Exception\DatasourceException;

class QueryBuilderProcessor
{
    /** @var ManagerRegistry */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function processQuery(array $config)
    {
        if (array_key_exists('query_builder', $config)) {
            $qb = $config['query_builder']; // "@service.name->getGridQb"
            if ($qb instanceof QueryBuilder) {
                return $qb;
            } else {
                throw new DatasourceException(
                    sprintf(
                        '%s configured with service must return an instance of Doctrine\ORM\QueryBuilder, %s given',
                        get_class($this),
                        is_object($qb) ? get_class($qb) : gettype($qb)
                    )
                );
            }
        } else {
            $entity = $config['entity'];
            $repository = $this->registry->getRepository($entity);

            $qb = null;
            if(array_key_exists('repository_method', $config)) {
                $method = $config['repository_method'];
                if (method_exists($repository, $method)) {
                    $qb = $repository->$method();
                } else {
                    throw new DatasourceException(sprintf('%s has no method %s', get_class($repository), $method));
                }
            }else {
                $qb = $repository->createQueryBuilder('o');
            }

            if ($qb instanceof QueryBuilder) {
                return $qb;
            } else {
                throw new DatasourceException(
                    sprintf(
                        '%s::%s() must return an instance of Doctrine\ORM\QueryBuilder, %s given',
                        get_class($repository),
                        $method,
                        is_object($qb) ? get_class($qb) : gettype($qb)
                    )
                );
            }
        }

        throw new DatasourceException(get_class($this).' expects to be configured with query or repository method');
    }
}
