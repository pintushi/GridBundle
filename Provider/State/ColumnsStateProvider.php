<?php

namespace Pintushi\Bundle\GridBundle\Provider\State;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Entity\Manager\GridViewManager;
use Pintushi\Bundle\GridBundle\Extension\Columns\ColumnStateExtension;
use Pintushi\Bundle\GridBundle\Extension\Columns\Configuration;
use Pintushi\Bundle\GridBundle\Tools\GridParametersHelper;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * Provides request- and user-specific grid state for columns component.
 * Tries to fetch state from grid parameters, then fallbacks to state from current grid view, then from default
 * grid view, then to grid columns configuration.
 * State is respresented by an array with column names as key and array with the following keys as values:
 * - renderable: whether a column must be displayed on frontend
 * - order: column order (weight)
 */
class ColumnsStateProvider extends AbstractStateProvider
{
    public const RENDER_FIELD_NAME = 'renderable';
    public const ORDER_FIELD_NAME = 'order';

    /** @var GridParametersHelper */
    private $gridParametersHelper;

    /**
     * @param GridViewManager $gridViewManager
     * @param TokenAccessorInterface $tokenAccessor
     * @param GridParametersHelper $gridParametersHelper
     */
    public function __construct(
        GridViewManager $gridViewManager,
        TokenAccessorInterface $tokenAccessor,
        GridParametersHelper $gridParametersHelper
    ) {
        parent::__construct($gridViewManager, $tokenAccessor);
        $this->gridParametersHelper = $gridParametersHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(GridConfiguration $gridConfiguration, ParameterBag $gridParameters): array
    {
        // Fetch state from grid parameters.
        $state = $this->getFromParameters($gridParameters);

        // Try to fetch state from grid view.
        if (!$state) {
            $gridView = $this->getActualGridView($gridConfiguration, $gridParameters);
            if ($gridView) {
                $state = $gridView->getColumnsData();
            }
        }

        return $this->sanitizeState($state, $this->getColumnsConfig($gridConfiguration));
    }

    /**
     * {@inheritdoc}
     */
    public function getStateFromParameters(
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    ): array {
        // Fetch state from grid parameters.
        $state = $this->getFromParameters($gridParameters);

        return $this->sanitizeState($state, $this->getColumnsConfig($gridConfiguration));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultState(GridConfiguration $gridConfiguration): array
    {
        return $this->sanitizeState([], $this->getColumnsConfig($gridConfiguration));
    }

    /**
     * @param array $state
     * @param array $columnsConfig
     *
     * @return array
     */
    private function sanitizeState(array $state, array $columnsConfig): array
    {
        $state = array_intersect_key($state, $columnsConfig);

        $columnsData = array_replace_recursive($columnsConfig, $state);

        return $this->fillRenderableAndWeight($columnsData);
    }

    /**
     * {@inheritdoc}
     */
    private function getFromParameters(ParameterBag $gridParameters): array
    {
        $rawColumnsState = $this->getRawColumnsState($gridParameters);
        if (!$rawColumnsState) {
            return [];
        }

        if (\is_array($rawColumnsState)) {
            $columnsState = $this->getFromNonMinifiedState($rawColumnsState);
        } else {
            $columnsState = $this->getFromMinifiedState($rawColumnsState);
        }

        return $columnsState;
    }

    /**
     * @param ParameterBag $gridParameters
     *
     * @return array|string
     */
    private function getRawColumnsState(ParameterBag $gridParameters)
    {
        $rawColumnsState = $this->gridParametersHelper
            ->getFromParameters($gridParameters, ColumnStateExtension::COLUMNS_PARAM);

        // Try to fetch from minified parameters if any.
        if (!$rawColumnsState) {
            $rawColumnsState = $this->gridParametersHelper
                ->getFromMinifiedParameters($gridParameters, ColumnStateExtension::MINIFIED_COLUMNS_PARAM);
        }

        return $rawColumnsState;
    }

    /**
     * @param array $rawColumnsState
     *
     * @return array
     */
    private function getFromNonMinifiedState(array $rawColumnsState): array
    {
        return array_filter(array_map(function ($columnData) {
            $state = [];
            if (isset($columnData[self::RENDER_FIELD_NAME])) {
                $state[self::RENDER_FIELD_NAME] = $columnData[self::RENDER_FIELD_NAME];
            }
            if (isset($columnData[self::ORDER_FIELD_NAME])) {
                $state[self::ORDER_FIELD_NAME] = $columnData[self::ORDER_FIELD_NAME];
            }

            return $state;
        }, $rawColumnsState));
    }

    /**
     * @param string $rawColumnsState
     *
     * @return array
     */
    private function getFromMinifiedState(string $rawColumnsState): array
    {
        if (!$rawColumnsState) {
            return [];
        }

        $columnsState = [];
        foreach (explode('.', $rawColumnsState) as $key => $columnState) {
            // Last char is flag indicating column state, rest part is a column name.
            $columnsState[substr($columnState, 0, -1)] = [
                self::ORDER_FIELD_NAME => (int)$key,
                self::RENDER_FIELD_NAME => substr($columnState, -1) === '1',
            ];
        }

        return $columnsState;
    }

    /**
     * @param GridConfiguration $gridConfiguration
     *
     * @return array
     */
    private function getColumnsConfig(GridConfiguration $gridConfiguration): array
    {
        return (array)$gridConfiguration->offsetGet(Configuration::COLUMNS_KEY);
    }

    /**
     * @param array $columnsData
     *
     * @return array
     */
    private function fillRenderableAndWeight(array $columnsData): array
    {
        $weight = 0;
        $explicitWeights = array_column($columnsData, self::ORDER_FIELD_NAME);

        return array_map(
            function (array $columnData) use (&$weight, &$explicitWeights) {
                // Fill "render" option, default is true.
                $columnState[self::RENDER_FIELD_NAME] = $columnData[self::RENDER_FIELD_NAME] ?? true;
                $columnState[self::RENDER_FIELD_NAME] = filter_var(
                    $columnState[self::RENDER_FIELD_NAME],
                    FILTER_VALIDATE_BOOLEAN
                );

                // Get "order" option if any.
                $columnState[self::ORDER_FIELD_NAME] = filter_var(
                    $columnData[self::ORDER_FIELD_NAME] ?? null,
                    FILTER_VALIDATE_INT
                );

                // Fill "order" option if it contains false.
                if ($columnState[self::ORDER_FIELD_NAME] === false) {
                    $columnState[self::ORDER_FIELD_NAME] = $weight;
                    $explicitWeights[] = $weight;
                    while (\in_array($weight, $explicitWeights, true)) {
                        $weight++;
                    }
                }

                return $columnState;
            },
            $columnsData
        );
    }
}
