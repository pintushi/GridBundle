<?php

namespace Pintushi\Bundle\GridBundle\Provider\State;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Manager\GridViewManager;
use Pintushi\Bundle\GridBundle\Extension\Columns\ColumnInterface;
use Pintushi\Bundle\GridBundle\Extension\Sorter\AbstractSorterExtension;
use Pintushi\Bundle\GridBundle\Extension\Sorter\Configuration as SorterConfiguration;
use Pintushi\Bundle\GridBundle\Tools\GridParametersHelper;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * Provides request- and user-specific grid state for sorters component.
 * Tries to fetch state from grid parameters, then fallbacks to state from current grid view, then from default
 * grid view, then to grid columns configuration.
 * State is respresented by an array with column names as key and order direction as value.
 */
class SortersStateProvider extends AbstractStateProvider
{
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
                $state = $gridView->getSortersData();
            }
        }

        // Fallback to default sorters.
        if (!$state) {
            $state = $this->getDefaultSorters($gridConfiguration);
        }

        return $this->sanitizeState($state, $this->getSortersConfig($gridConfiguration));
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

        // Fallback to default sorters.
        if (!$state) {
            $state = $this->getDefaultSorters($gridConfiguration);
        }

        return $this->sanitizeState($state, $this->getSortersConfig($gridConfiguration));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultState(GridConfiguration $gridConfiguration): array
    {
        $state = $this->getDefaultSorters($gridConfiguration);

        return $this->sanitizeState($state, $this->getSortersConfig($gridConfiguration));
    }

    /**
     * @param array $state
     * @param array $sortersConfig
     *
     * @return array
     */
    private function sanitizeState(array $state, array $sortersConfig): array
    {
        // Remove sorters which are not in grid configuration.
        $state = array_intersect_key($state, $sortersConfig);

        array_walk($state, [$this, 'normalizeDirection']);

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    private function getFromParameters(ParameterBag $gridParameters): array
    {
        $sortersState = $this->gridParametersHelper
            ->getFromParameters($gridParameters, AbstractSorterExtension::SORTERS_ROOT_PARAM);

        // Try to fetch from minified parameters if any.
        if (!$sortersState) {
            $sortersState = $this->gridParametersHelper
                ->getFromMinifiedParameters($gridParameters, AbstractSorterExtension::MINIFIED_SORTERS_PARAM);
        }

        return (array)$sortersState;
    }

    /**
     * @param GridConfiguration $gridConfiguration
     *
     * @return array
     */
    private function getSortersConfig(GridConfiguration $gridConfiguration): array
    {
        return array_filter(
            $gridConfiguration->offsetGetByPath(SorterConfiguration::COLUMNS_PATH, []),
            function (array $sorterDefinition) {
                return empty($sorterDefinition[ColumnInterface::DISABLED_KEY]);
            }
        );
    }

    /**
     * @param string|int|bool $direction
     */
    private function normalizeDirection(&$direction): void
    {
        switch ($direction) {
            case AbstractSorterExtension::DIRECTION_ASC:
            case AbstractSorterExtension::DIRECTION_DESC:
                break;
            case 1:
            case false:
                $direction = AbstractSorterExtension::DIRECTION_DESC;
                break;
            case -1:
            default:
                $direction = AbstractSorterExtension::DIRECTION_ASC;
        }
    }

    /**
     * @param GridConfiguration $gridConfiguration
     *
     * @return array
     */
    private function getDefaultSorters(GridConfiguration $gridConfiguration): array
    {
        return $gridConfiguration->offsetGetByPath(SorterConfiguration::DISABLE_DEFAULT_SORTING_PATH, false)
            ? []
            : $gridConfiguration->offsetGetByPath(SorterConfiguration::DEFAULT_SORTERS_PATH, []);
    }
}
