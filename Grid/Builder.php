<?php

namespace Pintushi\Bundle\GridBundle\Grid;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Event\BuildAfter;
use Pintushi\Bundle\GridBundle\Event\BuildBefore;
use Pintushi\Bundle\GridBundle\Event\PreBuild;
use Pintushi\Bundle\GridBundle\Exception\RuntimeException;
use Pintushi\Bundle\GridBundle\Extension\Acceptor;
use Pintushi\Bundle\GridBundle\Extension\ExtensionVisitorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Builder
{
    /** @var string */
    protected $baseGridClass;

    /** @var string */
    protected $acceptorClass;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var DatasourceInterface[] */
    protected $dataSources = [];

    /** @var ExtensionVisitorInterface[] */
    protected $extensions = [];

    /**
     * @param                          $baseGridClass
     * @param                          $acceptorClass
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct($baseGridClass, $acceptorClass, EventDispatcherInterface $eventDispatcher)
    {
        $this->baseGridClass = $baseGridClass;
        $this->acceptorClass     = $acceptorClass;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * Create, configure and build grid
     *
     * @param GridConfiguration $config
     * @param ParameterBag          $parameters
     * @param array                 $additionalParameters
     *
     * @return GridInterface
     */
    public function build(GridConfiguration $config, ParameterBag $parameters, array $additionalParameters = [])
    {
        /**
         * @TODO: should be refactored in BAP-6849
         */
        $minified = $parameters->get(ParameterBag::MINIFIED_PARAMETERS);
        if (is_array($minified) && array_key_exists('g', $minified) && is_array($minified['g'])) {
            $parameters->add(array_merge($minified['g'], $additionalParameters));
        }

        /**
         * @TODO: should be refactored in BAP-6826
         */
        $event = new PreBuild($config, $parameters);
        $this->eventDispatcher->dispatch(PreBuild::NAME, $event);

        $class = $config->offsetGetByPath(GridConfiguration::BASE_DATAGRID_CLASS_PATH, $this->baseGridClass);
        $name  = $config->getName();

        /** @var GridInterface $grid */
        $grid = new $class($name, $config, $parameters);
        $grid->setScope($config->offsetGetOr('scope'));

        $event = new BuildBefore($grid, $config);
        $this->eventDispatcher->dispatch(BuildBefore::NAME, $event);

        $acceptor = $this->createAcceptor($config, $parameters);
        $grid->setAcceptor($acceptor);

        $acceptor->processConfiguration();
        $this->buildDataSource($grid, $config);

        $event = new BuildAfter($grid);
        $this->eventDispatcher->dispatch(BuildAfter::NAME, $event);

        return $grid;
    }

    /**
     * Register datasource type
     * Automatically registered services tagged by pintushi_grid.datasource tag
     *
     * @param string              $type
     * @param DatasourceInterface $dataSource
     *
     * @return $this
     */
    public function registerDatasource($type, DatasourceInterface $dataSource)
    {
        $this->dataSources[$type] = $dataSource;

        return $this;
    }

    /**
     * Register extension
     * Automatically registered services tagged by pintushi_grid.extension tag
     *
     * @param ExtensionVisitorInterface $extension
     *
     * @return $this
     */
    public function registerExtension(ExtensionVisitorInterface $extension)
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @param GridConfiguration $config
     * @param ParameterBag          $parameters
     *
     * @return Acceptor
     */
    protected function createAcceptor(GridConfiguration $config, ParameterBag $parameters)
    {
        /** @var Acceptor $acceptor */
        $acceptor = new $this->acceptorClass();
        $acceptor->setConfig($config);

        foreach ($this->extensions as $extension) {
            /**
             * ATTENTION: extension object should be cloned cause it can contain some state
             */
            $extension = clone $extension;
            $extension->setParameters($parameters);

            if ($extension->isApplicable($config)) {
                $acceptor->addExtension($extension);
            }
        }
        $acceptor->sortExtensionsByPriority();

        return $acceptor;
    }

    /**
     * Try to find datasource adapter and process it
     * Datasource object should be self-acceptable to grid
     *
     * @param GridInterface     $grid
     * @param GridConfiguration $config
     *
     * @throws RuntimeException
     */
    protected function buildDataSource(GridInterface $grid, GridConfiguration $config)
    {
        $sourceType = $config->offsetGetByPath(GridConfiguration::DATASOURCE_TYPE_PATH, false);
        if (!$sourceType) {
            throw new RuntimeException('Grid source does not configured');
        }

        if (!isset($this->dataSources[$sourceType])) {
            throw new RuntimeException(sprintf('Grid source "%s" does not exist', $sourceType));
        }

        $this->dataSources[$sourceType]->process(
            $grid,
            $config->offsetGetByPath(GridConfiguration::DATASOURCE_PATH, [])
        );
    }
}
