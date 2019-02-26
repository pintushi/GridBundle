<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action\Actions;

class ExportAction extends AbstractImportExportAction
{
    /**
     * @var array
     */
    protected $requiredOptions = [
        'exportProcessor',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return self::TYPE_EXPORT;
    }
}
