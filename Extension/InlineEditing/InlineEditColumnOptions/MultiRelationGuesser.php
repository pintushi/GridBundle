<?php

namespace Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions;

use Pintushi\Bundle\GridBundle\Extension\Formatter\Property\PropertyInterface;
use Pintushi\Bundle\GridBundle\Extension\InlineEditing\Configuration;

/**
 * Class MultiRelationGuesser
 * @package Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions
 */
class MultiRelationGuesser extends RelationGuesser
{
    /** Frontend type */
    const MULTI_RELATION = 'multi-relation';

    const DEFAULT_EDITOR_VIEW = 'oroform/js/app/views/editor/multi-relation-editor-view';
    const DEFAULT_API_ACCESSOR_CLASS = 'oroui/js/tools/search-api-accessor';

    /**
     * {@inheritdoc}
     */
    public function guessColumnOptions($columnName, $entityName, $column, $isEnabledInline = false)
    {
        $result = [];
 
        if (array_key_exists(PropertyInterface::FRONTEND_TYPE_KEY, $column)
            && $column[PropertyInterface::FRONTEND_TYPE_KEY] === self::MULTI_RELATION) {
            $isConfiguredInlineEdit = array_key_exists(Configuration::BASE_CONFIG_KEY, $column);
            $result = $this->guessEditorView($column, $isConfiguredInlineEdit, $result);
            $result = $this->guessApiAccessorClass($column, $isConfiguredInlineEdit, $result);
        }

        return $result;
    }
}
