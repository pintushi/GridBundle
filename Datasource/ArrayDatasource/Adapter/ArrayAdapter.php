<?php

namespace Pintushi\Bundle\GridBundle\Datasource\ArrayDatasource\Adapter;

use Pagerfanta\Adapter\ArrayAdapter as PagerfantaArrayAdapter;
use Pintushi\Bundle\GridBundle\Datasource\PagerfantaAdapterTrait;

class ArrayAdapter extends PagerfantaArrayAdapter
{
    use PagerfantaAdapterTrait;
}
