<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Pintushi\Bundle\GridBundle\ORM\Query\ResultIterator\IdentifierWithoutOrderByIterationStrategy;

/**
 * The aim of this class is provide an iterator which can be used for delete records.
 * This iterator is always iterates through the first page of a buffer. So, it allows you to
 * iterate through records to be deleted and delete them one by one.
 */
class DeletionIterableResult extends IterableResult
{
    /**
     * {@inheritdoc}
     */
    protected function getIterationStrategy()
    {
        if (null === $this->iterationStrategy) {
            $this->iterationStrategy = new IdentifierWithoutOrderByIterationStrategy();
        }

        return $this->iterationStrategy;
    }
}
