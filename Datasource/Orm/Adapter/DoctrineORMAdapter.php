<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm\Adapter;

use Pagerfanta\Adapter\DoctrineORMAdapter as PagerfantaDoctrineORMAdapter;
use Pintushi\Bundle\GridBundle\Datasource\Orm\ResultRecord;

class DoctrineORMAdapter extends PagerfantaDoctrineORMAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $records = [];
        foreach (parent::getSlice($offset, $length) as $record) {
            $records[] = new ResultRecord($record);
        }

        return new \ArrayIterator($records);
    }
}
