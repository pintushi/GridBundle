<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\IterableResult;
use Pintushi\Bundle\GridBundle\Datasource\Orm\IterableResultInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmDatasource;
use Pintushi\Bundle\GridBundle\Exception\LogicException;
use Pintushi\Bundle\GridBundle\Extension\Action\ActionConfiguration;
use Pintushi\Bundle\GridBundle\Extension\MassAction\DTO\SelectedItems;
use Pintushi\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Component\DoctrineUtils\ORM\QueryBuilderUtil;

/**
 * Creates IterableResultInterace for orm datasouce to pass to mass action handler through params.
 */
class IterableResultFactory implements IterableResultFactoryInterface
{
    /**
     * @var AclHelper
     */
    private $aclHelper;

    /**
     * @param AclHelper $aclHelper
     */
    public function __construct(AclHelper $aclHelper)
    {
        $this->aclHelper = $aclHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(DatasourceInterface $dataSource): bool
    {
        return $dataSource instanceof OrmDatasource;
    }

    /**
     * {@inheritdoc}
     */
    public function createIterableResult(
        DatasourceInterface $dataSource,
        ActionConfiguration $actionConfiguration,
        DatagridConfiguration $gridConfiguration,
        SelectedItems $selectedItems
    ): IterableResultInterface {
        /** @var OrmDatasource $dataSource */
        if (!$this->isApplicable($dataSource)) {
            throw new LogicException(
                sprintf('Expecting "%s" datasource type, "%s" given', OrmDatasource::class, get_class($dataSource))
            );
        }

        /** @var QueryBuilder $qb */
        $qb = $dataSource->getQueryBuilder();

        //prepare query builder
        $qb->setMaxResults(null);
        $qb->setFirstResult(null);

        $identifierField = $this->getIdentifierField($actionConfiguration);
        $objectIdentifier = $this->getObjectIdentifier($actionConfiguration);

        if ($selectedItems->getValues()) {
            $valueWhereCondition =
                $selectedItems->isInset()
                    ? $qb->expr()->in($identifierField, ':values')
                    : $qb->expr()->notIn($identifierField, ':values');
            $qb->andWhere($valueWhereCondition);
            $qb->setParameter('values', $selectedItems->getValues());
        }

        if ($objectIdentifier) {
            $qb->addSelect($objectIdentifier);
        }

        if (!$gridConfiguration->isDatasourceSkipAclApply()) {
            $qb = $this->aclHelper->apply($qb);
        }

        return $this->getIterableResult($qb);
    }

    /**
     * @param ActionConfiguration $actionConfiguration
     * @return string
     */
    private function getIdentifierField(ActionConfiguration $actionConfiguration)
    {
        $identifier = $actionConfiguration->offsetGetOr('data_identifier');
        if (!$identifier) {
            throw new LogicException('Mass action must define identifier name');
        }
        QueryBuilderUtil::checkField($identifier);

        return $identifier;
    }

    /**
     * @param ActionConfiguration $actionConfiguration
     * @return null|string
     */
    private function getObjectIdentifier(ActionConfiguration $actionConfiguration)
    {
        $identifier = $actionConfiguration->offsetGetOr('object_identifier');
        QueryBuilderUtil::checkIdentifier($identifier);

        return $identifier;
    }

    /**
     * @param Query|QueryBuilder $qb
     * @return IterableResult
     */
    protected function getIterableResult($qb): IterableResult
    {
        return new IterableResult($qb);
    }
}
