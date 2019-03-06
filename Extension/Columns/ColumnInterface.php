<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

interface ColumnInterface
{
    const TYPE_DATE         = 'date';
    const TYPE_DATETIME     = 'datetime';
    const TYPE_TIME         = 'time';
    const TYPE_DECIMAL      = 'decimal';
    const TYPE_INTEGER      = 'integer';
    const TYPE_PERCENT      = 'percent';
    const TYPE_CURRENCY     = 'currency';
    const TYPE_SELECT       = 'select';
    const TYPE_MULTI_SELECT = 'multi-select';
    const TYPE_STRING       = 'string';
    const TYPE_HTML         = 'html';
    const TYPE_BOOLEAN      = 'boolean';
    const TYPE_ARRAY        = 'array';
    const TYPE_SIMPLE_ARRAY = 'simple_array';


    const METADATA_NAME_KEY = 'name';
    const METADATA_TYPE_KEY = 'type';

    const TRANSLATABLE_KEY  = 'translatable';
    const DISABLED_KEY      = 'disabled';
    const TYPE_KEY          = 'type';
    const NAME_KEY          = 'name';
    const DATA_PATH_KEY     = 'path';
}
