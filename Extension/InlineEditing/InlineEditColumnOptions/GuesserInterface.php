<?php

namespace Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions;

/**
 * Interface GuesserInterface
 * @package Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions
 */
interface GuesserInterface
{
    /**
     * @param string $columnName
     * @param string $entityName
     * @param array $column
     * @param bool $isEnabledInline
     *
     * @return array
     */
    public function guessColumnOptions($columnName, $entityName, $column, $isEnabledInline = false);
}
