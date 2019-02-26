<?php

namespace Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions;

use Pintushi\Bundle\GridBundle\Extension\InlineEditing\Configuration;
use Pintushi\Bundle\EntityBundle\ORM\DoctrineHelper;

/**
 * Class TextGuesser
 * @package Pintushi\Bundle\GridBundle\Extension\InlineEditing\InlineEditColumnOptions
 */
class TextGuesser implements GuesserInterface
{
    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function guessColumnOptions($columnName, $entityName, $column, $isEnabledInline = false)
    {
        $entityManager = $this->doctrineHelper->getEntityManager($entityName);
        $metadata = $entityManager->getClassMetadata($entityName);

        $result = [];
        if ($isEnabledInline && $metadata->hasField($columnName) && !$metadata->hasAssociation($columnName)) {
            $result[Configuration::BASE_CONFIG_KEY] = [Configuration::CONFIG_ENABLE_KEY => true];
        }

        return $result;
    }
}
