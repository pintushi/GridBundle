<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Process columns metadata for frontend.
 */
class ColumnsExtension extends AbstractExtension
{
    /** @var ColumnInterface[] */
    protected $columns = [];

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        if (!parent::isApplicable($config)) {
            return false;
        }

        $columns    = $config->offsetGetOr(Configuration::COLUMNS_KEY, []);
        $applicable = $columns;
        $this->processConfigs($config);

        return $applicable;
    }


    /**
     * Validate configs nad fill default values
     *
     * @param GridConfiguration $config
     */
    public function processConfigs(GridConfiguration $config)
    {
        $columns    = $config->offsetGetOr(Configuration::COLUMNS_KEY, []);

        // validate extension configuration and normalize by setting default values
        $columnsNormalized    = $this->validateConfigurationByType($columns, Configuration::COLUMNS_KEY);

        // replace config values by normalized, extra keys passed directly
        $config->offsetSet(Configuration::COLUMNS_KEY, array_replace_recursive($columns, $columnsNormalized));
    }

    /**
     * {@inheritdoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        // get only columns here because columns will be represented on frontend
        $columns = $config->offsetGetOr(Configuration::COLUMNS_KEY, []);

        $columnsMetadata = [];
        foreach ($columns as $name => $fieldConfig) {
            $fieldConfig = ColumnConfiguration::createNamed($name, $fieldConfig);
            $metadata    = $this->getColumnObject($fieldConfig)->getMetadata();

            // translate label on backend
            $metadata['label']    = $metadata[ColumnInterface::TRANSLATABLE_KEY]
                ? $this->translator->trans($metadata['label'])
                : $metadata['label'];
            $columnsMetadata[] = $metadata;
        }

        $data->offsetAddToArray('columns', $columnsMetadata);
    }

    /**
     * Add column to array of available columns, usually called by DIC
     *
     * @param string            $name
     * @param ColumnInterface $column
     */
    public function registerColumn($name, ColumnInterface $column)
    {
        $this->columns[$name] = $column;
    }

    /**
     * Returns prepared column object
     *
     * @param ColumnConfiguration $config
     *
     * @return ColumnInterface
     */
    protected function getColumnObject(ColumnConfiguration $config)
    {
        $column = $this->columns[$config->offsetGet(Configuration::TYPE_KEY)]->init($config);

        return $column;
    }

     /**
     * Validates specified type configuration
     *
     * @param array  $config
     * @param string $type
     *
     * @return array
     */
    protected function validateConfigurationByType($config, $type)
    {
        $registeredTypes = array_keys($this->columns);
        $configuration   = new Configuration($registeredTypes, $type);

        return parent::validateConfiguration($configuration, [$type => $config]);
    }
}
