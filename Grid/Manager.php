<?php

namespace Pintushi\Bundle\GridBundle\Grid;

use Pintushi\Bundle\GridBundle\Exception\InvalidArgumentException;
use Pintushi\Bundle\GridBundle\Provider\ConfigurationProviderInterface;

/**
 * Class Manager
 *
 * @package Pintushi\Bundle\GridBundle\Grid
 *
 * Responsibility of this class is to store raw config data, prepare configs for grid builder.
 * Public interface returns grid object prepared by builder using config
 */
class Manager implements ManagerInterface
{
    /**
     * This flag may be used by callers of this class and extensions to decide are they required for current request
     */
    const REQUIRE_ALL_EXTENSIONS = 'require_all_extensions';

    /** @var Builder */
    protected $gridBuilder;

    /** @var ConfigurationProviderInterface */
    protected $configurationProvider;

    /** @var RequestParameterBagFactory */
    protected $parametersFactory;

    /** @var NameStrategyInterface */
    protected $nameStrategy;

    /**
     * Constructor
     *
     * @param ConfigurationProviderInterface $configurationProvider
     * @param Builder                        $builder
     * @param RequestParameterBagFactory     $parametersFactory
     * @param NameStrategyInterface          $nameStrategy
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        Builder $builder,
        RequestParameterBagFactory $parametersFactory,
        NameStrategyInterface $nameStrategy
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->gridBuilder       = $builder;
        $this->parametersFactory     = $parametersFactory;
        $this->nameStrategy          = $nameStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function getGrid($name, $parameters = null, array $additionalParameters = [])
    {
        if (null === $parameters) {
            $parameters = new ParameterBag();
        } elseif (is_array($parameters)) {
            $parameters = new ParameterBag($parameters);
        } elseif (!$parameters instanceof ParameterBag) {
            throw new InvalidArgumentException('$parameters must be an array or instance of ParameterBag.');
        }

        $configuration = $this->getConfigurationForGrid($name);

        $grid = $this->gridBuilder->build($configuration, $parameters, $additionalParameters);

        return $grid;
    }

    /**
     * Used to generate unique id for grid on page
     *
     * @param string $name
     *
     * @return string
     */
    public function getGridUniqueName($name)
    {
        return $this->nameStrategy->getGridUniqueName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getGridByRequestParams($name, array $additionalParameters = [])
    {
        $gridScope = $this->nameStrategy->parseGridScope($name);
        if (!$gridScope) {
            // In case if grid has scope in config we should use it to get grid parameters properly
            $configuration = $this->getConfigurationForGrid($name);
            $scope = $configuration->offsetGetOr('scope');
            if ($scope) {
                $name = $this->nameStrategy->buildGridFullName($name, $scope);
            }
        }

        $uniqueName = $this->getGridUniqueName($name);
        $parameters = $this->parametersFactory->createParameters($uniqueName);

        /**
         * In case of custom relation - $gridScope will be present.
         * So we need to check for additional parameters (pager, sorter, etc.) by gridName (without scope).
         * E.g. 'uniqueName' can be like 'entity-relation-grid:OroAcme_Bundle_AcmeBundle_Entity_AcmeEntit-relOneToMany'
         *  so parameters by 'uniqueName' will contain 'class_name', 'field_name', 'id'
         *  and parameters by 'gridName' (entity-relation-grid) will contain '_pager', '_sort_by', etc.
         * In such cases we'll merge them together, otherwise pagination and sorters will not work.
         */
        if ($gridScope) {
            $gridName   = $this->nameStrategy->parseGridName($name);
            $additional = $this->parametersFactory->createParameters($gridName)->all();
            if ($additional) {
                $additionalParameters = array_merge($additionalParameters, $additional);
            }
        }

        $parameters->add($additionalParameters);

        return $this->getGrid($name, $parameters, $additionalParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurationForGrid($name)
    {
        $gridName = $this->nameStrategy->parseGridName($name);
        $result = $this->configurationProvider->getConfiguration($gridName);

        $gridScope = $this->nameStrategy->parseGridScope($name);
        if ($gridScope) {
            $result->offsetSet('scope', $gridScope);
        }

        return $result;
    }
}
