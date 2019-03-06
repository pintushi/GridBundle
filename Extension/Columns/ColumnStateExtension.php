<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Pintushi\Bundle\GridBundle\Extension\GridViews\GridViewsExtension;
use Pintushi\Bundle\GridBundle\Provider\State\ColumnsStateProvider;
use Pintushi\Bundle\GridBundle\Provider\State\GridStateProviderInterface;

/**
 * Updates grid metadata object with:
 * - initial columns state - as per grid columns configuration;
 * - columns state - as per current state based on columns configuration, grid view settings and grid parameters;
 * - updates metadata columns with current `order` and `renderable` values.
 */
class ColumnStateExtension extends AbstractExtension
{
    public const MINIFIED_COLUMNS_PARAM = 'c';
    public const COLUMNS_PARAM = '_columns';

    /** @var GridStateProviderInterface|ColumnsStateProvider GridStateProviderInterface */
    private $columnsStateProvider;

    /**
     * @param GridStateProviderInterface $columnsStateProvider
     */
    public function __construct(GridStateProviderInterface $columnsStateProvider)
    {
        $this->columnsStateProvider = $columnsStateProvider;
    }

    /**
     * Should be applied after FormatterExtension.
     *
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(GridConfiguration $gridConfiguration)
    {
        if (!parent::isApplicable($gridConfiguration)) {
            return false;
        }

        $columnsConfig = $gridConfiguration->offsetGetOr(Configuration::COLUMNS_KEY, []);

        return count($columnsConfig) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function visitMetadata(GridConfiguration $gridConfiguration, MetadataObject $metadata)
    {
        $gridParameters = $this->getParameters();

        $columnsState = $this->columnsStateProvider->getState($gridConfiguration, $gridParameters);
        $this->setColumnsState($metadata, $columnsState);
        $this->updateMetadataColumns($metadata, $columnsState);

        $defaultColumnsState = $this->columnsStateProvider->getDefaultState($gridConfiguration);
        $this->setInitialColumnsState($metadata, $defaultColumnsState);
        $this->updateMetadataDefaultGridView($metadata, $defaultColumnsState);
    }

    /**
     * @param MetadataObject $metadata
     * @param array $columnsState
     */
    private function setInitialColumnsState(MetadataObject $metadata, array $columnsState): void
    {
        $metadata->offsetAddToArray('initialState', ['columns' => $columnsState]);
    }

    /**
     * @param MetadataObject $metadata
     * @param array $columnsState
     */
    private function updateMetadataDefaultGridView(MetadataObject $metadata, array $columnsState): void
    {
        $defaultGridViewKey = array_search(
            GridViewsExtension::DEFAULT_VIEW_ID,
            array_column($metadata->offsetGetByPath('[gridViews][views]', []), 'name'),
            false
        );

        if ($defaultGridViewKey !== false) {
            $metadata->offsetSetByPath(
                sprintf('[gridViews][views][%s][%s]', $defaultGridViewKey, 'columns'),
                $columnsState
            );
        }
    }

    /**
     * @param MetadataObject $metadata
     * @param array $columnsState
     */
    private function setColumnsState(MetadataObject $metadata, array $columnsState): void
    {
        $metadata->offsetAddToArray('state', ['columns' => $columnsState]);
    }

    /**
     * @param MetadataObject $metadata
     * @param array $columnsState
     */
    private function updateMetadataColumns(MetadataObject $metadata, array $columnsState): void
    {
        $columns = $metadata->offsetGetOr('columns', []);
        foreach ($columns as $index => $columnMetadata) {
            $columnName = $columnMetadata['name'] ?? null;
            if ($columnName === null) {
                continue;
            }

            $columnState = $columnsState[$columnName] ?? null;
            if ($columnState === null) {
                continue;
            }

            foreach ([ColumnsStateProvider::ORDER_FIELD_NAME, ColumnsStateProvider::RENDER_FIELD_NAME] as $configKey) {
                $metadata->offsetSetByPath(
                    sprintf('[%s][%s][%s]', 'columns', $index, $configKey),
                    $columnState[$configKey]
                );
            }
        }
    }
}
