<?php

namespace Pintushi\Bundle\GridBundle\Datagrid;

class AbstractColumnOptionsGuesser implements ColumnOptionsGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guessFormatter($class, $property, $type)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessSorter($class, $property, $type)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function guessFilter($class, $property, $type)
    {
        return null;
    }
}
