<?php

namespace Pintushi\Bundle\GridBundle\Datasource;

use Pintushi\Bundle\GridBundle\Datasource\ResultRecord;

trait PagerfantaAdapterTrait
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
