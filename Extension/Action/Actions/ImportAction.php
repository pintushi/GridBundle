<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action\Actions;

class ImportAction extends AbstractImportExportAction
{
    /**
     * @var array
     */
    protected $requiredOptions = [
        'importProcessor',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return self::TYPE_IMPORT;
    }
}
