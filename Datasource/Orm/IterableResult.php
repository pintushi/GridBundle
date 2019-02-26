<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Pintushi\Bundle\GridBundle\ORM\Query\BufferedIdentityQueryResultIterator;
use Pintushi\Bundle\GridBundle\Datasource\ResultRecord;

/**
 * Iterates query result with elements of ResultRecord type
 */
class IterableResult extends BufferedIdentityQueryResultIterator implements IterableResultInterface
{
    /**
     * {@inheritDoc}
     */
    public function next()
    {
        parent::next();

        $this->current = parent::current();
        if (null !== $this->current) {
            $this->current = new ResultRecord($this->current);
        }
    }
}
