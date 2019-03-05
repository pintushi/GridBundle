<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Doctrine\ORM\Query;
use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;

/**
 * The default implementation of QueryExecutorInterface.
 */
class QueryExecutor implements QueryExecutorInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(DatagridInterface $datagrid, Query $query, $executeFunc = null)
    {
        if (null === $executeFunc) {
            return $query->getResult();
        }

        if (!is_callable($executeFunc)) {
            throw new \InvalidArgumentException(sprintf(
                'The $executeFunc must be callable or null, got "%s".',
                is_object($executeFunc) ? get_class($executeFunc) : gettype($executeFunc)
            ));
        }

        return $executeFunc($query);
    }
}
