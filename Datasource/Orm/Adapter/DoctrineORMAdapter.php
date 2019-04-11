<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm\Adapter;

use Pagerfanta\Adapter\DoctrineORMAdapter as PagerfantaDoctrineORMAdapter;
use Pintushi\Bundle\GridBundle\Datasource\PagerfantaAdapterTrait;

class DoctrineORMAdapter extends PagerfantaDoctrineORMAdapter
{
    use PagerfantaAdapterTrait;
}
