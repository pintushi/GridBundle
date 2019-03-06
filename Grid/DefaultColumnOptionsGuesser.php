<?php

namespace Pintushi\Bundle\GridBundle\Grid;

use Pintushi\Bundle\GridBundle\Grid\Guess\ColumnGuess;
use Pintushi\Bundle\GridBundle\Extension\Columns\ColumnInterface as Column;

class DefaultColumnOptionsGuesser extends AbstractColumnOptionsGuesser
{
    /** @var array */
    protected $typeMap = [
        'integer'      => Column::TYPE_INTEGER,
        'smallint'     => Column::TYPE_INTEGER,
        'bigint'       => Column::TYPE_INTEGER,
        'decimal'      => Column::TYPE_DECIMAL,
        'float'        => Column::TYPE_DECIMAL,
        'boolean'      => Column::TYPE_BOOLEAN,
        'date'         => Column::TYPE_DATE,
        'datetime'     => Column::TYPE_DATETIME,
        'time'         => Column::TYPE_TIME,
        'money'        => Column::TYPE_CURRENCY,
        'percent'      => Column::TYPE_PERCENT,
        'simple_array' => Column::TYPE_SIMPLE_ARRAY,
        'array'        => Column::TYPE_ARRAY,
        'json_array'   => Column::TYPE_ARRAY,
    ];

    /**
     * {@inheritdoc}
     */
    public function guessFormatter($class, $column, $type)
    {
        $options = [
            'type' => $this->getType($type),
        ];

        return new ColumnGuess($options, ColumnGuess::LOW_CONFIDENCE);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    protected function getType($type)
    {
        if (isset($this->typeMap[$type])) {
            return $this->typeMap[$type];
        }

        return Column::TYPE_STRING;
    }
}
