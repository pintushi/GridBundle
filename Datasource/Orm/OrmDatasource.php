<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\Configs\QueryBuilderProcessor;
use Pintushi\Bundle\GridBundle\Datasource\ParameterBinderAwareInterface;
use Pintushi\Bundle\GridBundle\Datasource\ParameterBinderInterface;
use Pintushi\Bundle\GridBundle\Event\OrmResultAfter;
use Pintushi\Bundle\GridBundle\Event\OrmResultBefore;
use Pintushi\Bundle\GridBundle\Event\OrmResultBeforeQuery;
use Pintushi\Bundle\GridBundle\Exception\BadMethodCallException;
use Oro\Component\DoctrineUtils\ORM\QueryHintResolver;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Allows to create grids from ORM queries.
 */
class OrmDatasource implements DatasourceInterface, ParameterBinderAwareInterface
{
    const TYPE = 'orm';

    /** @var QueryBuilder */
    protected $qb;

    /** @var array|null */
    protected $queryHints;

    /** @var QueryBuilderProcessor */
    protected $queryBuilderProcessor;

    /** @var GridInterface */
    protected $grid;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ParameterBinderInterface */
    protected $parameterBinder;

    /** @var QueryHintResolver */
    protected $queryHintResolver;

    /**
     * @param QueryBuilderProcessor $processor
     * @param EventDispatcherInterface $eventDispatcher
     * @param ParameterBinderInterface $parameterBinder
     * @param QueryHintResolver        $queryHintResolver
     */
    public function __construct(
        QueryBuilderProcessor $processor,
        EventDispatcherInterface $eventDispatcher,
        ParameterBinderInterface $parameterBinder,
        QueryHintResolver $queryHintResolver
    ) {
        $this->queryBuilderProcessor = $processor;
        $this->eventDispatcher = $eventDispatcher;
        $this->parameterBinder = $parameterBinder;
        $this->queryHintResolver = $queryHintResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function process(GridInterface $grid, array $config)
    {
        $this->grid = $grid;
        $this->processConfigs($config);
        $grid->setDatasource(clone $this);
    }

    /**
     * You must avoid to make changes of QueryBuilder here
     * because query was already used as is in grid extensions for example "PaginatorExtension"
     *
     * @return Pagerfanta
     */
    public function getData()
    {
        $this->eventDispatcher->dispatch(
            OrmResultBeforeQuery::NAME,
            new OrmResultBeforeQuery($this->grid, $this->qb)
        );

        $query = $this->qb->getQuery();
        $this->queryHintResolver->resolveHints($query, $this->queryHints ?? []);

        $this->eventDispatcher->dispatch(
            OrmResultBefore::NAME,
            new OrmResultBefore($this->grid, $query)
        );

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, false, false));
        $paginator->setNormalizeOutOfRangePages(true);

        $event = new OrmResultAfter($this->grid, $paginator, $query);
        $this->eventDispatcher->dispatch(OrmResultAfter::NAME, $event);

        return $paginator;
    }

    /**
     * Gets grid this datasource belongs to.
     *
     * @return GridInterface
     */
    public function getGrid(): GridInterface
    {
        return $this->grid;
    }

    /**
     * Returns query builder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * Set QueryBuilder
     *
     * @param QueryBuilder $qb
     *
     * @return $this
     */
    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->qb = $qb;

        return $this;
    }

    /**
     * {@inheritdoc}
     *  @deprecated since 2.0.
     */
    public function getParameterBinder()
    {
        return $this->parameterBinder;
    }

    /**
     * {@inheritdoc}
     */
    public function bindParameters(array $datasourceToGridParameters, $append = true)
    {
        if (!$this->grid) {
            throw new BadMethodCallException('Method is not allowed when datasource is not processed.');
        }

        return $this->parameterBinder->bindParameters($this->grid, $datasourceToGridParameters, $append);
    }

    public function __clone()
    {
        $this->qb = clone $this->qb;
    }

    /**
     * @param array $config
     */
    protected function processConfigs(array $config)
    {
        $this->qb = $this->queryBuilderProcessor->processQuery($config);

        $this->queryHints = $config['hints'] ?? [];
    }
}
