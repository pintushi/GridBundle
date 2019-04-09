<?php

namespace Pintushi\Bundle\GridBundle\Grid\Common;

use Doctrine\ORM\EntityRepository;
use Pintushi\Bundle\GridBundle\Grid\Builder;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmDatasource;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmQueryConfiguration;
use Pintushi\Bundle\GridBundle\Exception\LogicException;
use Pintushi\Bundle\GridBundle\Provider\SystemAwareResolver;
use Oro\Component\Config\Common\ConfigObject;

/**
 * This class represents read & parsed grid configuration.
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GridConfiguration extends ConfigObject
{
    const COLUMN_PATH = '[columns][%s]';
    const SORTER_PATH = '[sorters][columns][%s]';
    const FILTER_PATH = '[filters][columns][%s]';
    const DATASOURCE_PATH = '[source]';
    const HINTS_PATH      = '[source][hints]';
    const NAME_KEY           = 'name';
    const VALUE_KEY          = 'value';
    const DATASOURCE_TYPE_PATH = '[source][type]';
    const BASE_DATAGRID_CLASS_PATH  = '[options][base_grid_class]';

    const ENTITY_CLASS = 'ENTITY_CLASS';

    // Use this option as workaround for http://www.doctrine-project.org/jira/browse/DDC-2794
    const DATASOURCE_SKIP_COUNT_WALKER_PATH = '[options][skip_count_walker]';

    /**
     * This option refers to ACL resource that will be checked before grid is loaded.
     */
    const ACL_RESOURCE_PATH = '[acl_resource]';

    /**
     * This option makes possible to skip apply of ACL adjustment to source query of grid.
     */
    const DATASOURCE_SKIP_ACL_APPLY_PATH = '[source][skip_acl_apply]';

    /**
     * This option sets what ACL permission will be applied to datasource if value is DATASOURCE_SKIP_ACL_APPLY_PATH
     * is set to false. Default value of this setting is VIEW.
     */
    const DATASOURCE_ACL_APPLY_PERMISSION_PATH = '[source][acl_apply_permission]';

    /**
     * A grid parameters to datasource parameters binding.
     */
    const DATASOURCE_BIND_PARAMETERS_PATH = '[source][bind_parameters]';


    /** @var object|null */
    private $query;

    /**
     * Gets an instance of OrmQueryConfiguration that can be used to configure ORM query.
     *
     * @return OrmQueryConfiguration
     */
    public function getOrmQuery()
    {
        if (null === $this->query) {
            $datasourceType = $this->getDatasourceType();
            if (!$datasourceType || OrmDatasource::TYPE === $datasourceType) {
                $this->query = new OrmQueryConfiguration($this);
            }
        }
        if (!$this->query instanceof OrmQueryConfiguration) {
            throw new LogicException(
                sprintf(
                    'The expected data grid source type is "%s". Actual source type is "%s".',
                    OrmDatasource::TYPE,
                    $this->getDatasourceType()
                )
            );
        }

        return $this->query;
    }

    /**
     * Indicates whether the grid is based on ORM query.
     *
     * @return bool
     */
    public function isOrmDatasource()
    {
        return OrmDatasource::TYPE === $this->getDatasourceType();
    }

    /**
     * @return string
     */
    public function getDatasourceType()
    {
        return $this->offsetGetByPath(self::DATASOURCE_TYPE_PATH);
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setDatasourceType($type)
    {
        $this->offsetSetByPath(self::DATASOURCE_TYPE_PATH, $type);

        return $this;
    }

    /**
     * Gets the class name of extended entity the query is related with.
     *
     * @return string|null
     */
    public function getEntityClass()
    {
        return $this->offsetGetOr(self::ENTITY_CLASS);
    }

    /**
     * Sets or unsets the class name of extended entity the query is related with.
     *
     * @param string|null $className
     *
     * @return self
     */
    public function setEntityClass($className)
    {
        if ($className) {
            $this->offsetSet(self::ENTITY_CLASS, $className);
        } else {
            $this->offsetUnset(self::ENTITY_CLASS);
        }

        return $this;
    }

    /**
     * Get value of "acl_resource" option from grid configuration.
     *
     * @return string|null
     */
    public function getAclResource()
    {
        return $this->offsetGetByPath(self::ACL_RESOURCE_PATH, false);
    }

    /**
     * Check if ACL apply to source query of grid should be skipped
     *
     * @return bool
     */
    public function isDatasourceSkipAclApply()
    {
       return $this->offsetGetByPath(self::DATASOURCE_SKIP_ACL_APPLY_PATH, false);
    }

    /**
     * Gets ACL permission which should be applied to datasource if isDatasourceSkipAclApply() returns false.
     *
     * @return string
     */
    public function getDatasourceAclApplyPermission()
    {
        return $this->offsetGetByPath(self::DATASOURCE_ACL_APPLY_PERMISSION_PATH, 'VIEW');
    }

    /**
     * Sets ACL permission which should be applied to datasource if isDatasourceSkipAclApply() returns false.
     *
     * @param string $value
     * @return string
     */
    public function setDatasourceAclApplyPermission($value)
    {
        return $this->offsetSetByPath(self::DATASOURCE_ACL_APPLY_PERMISSION_PATH, $value);
    }

    /**
     * @param string $name
     * @param string $label
     *
     * @return self
     */
    public function updateLabel($name, $label)
    {
        if (empty($name)) {
            throw new \BadMethodCallException('GridConfiguration::updateLabel: name should not be empty');
        }

        $this->offsetSetByPath(sprintf(self::COLUMN_PATH.'[label]', $name), $label);

        return $this;
    }

    /**
     * @param string      $name       column name
     * @param array       $definition definition array as in grids.yml
     * @param null|string $select     select part for the column
     * @param array       $sorter     sorter definition
     * @param array       $filter     filter definition
     *
     * @return self
     */
    public function addColumn($name, array $definition, $select = null, array $sorter = [], array $filter = [])
    {
        if (empty($name)) {
            throw new \BadMethodCallException('GridConfiguration::addColumn: name should not be empty');
        }

        $this->offsetSetByPath(
            sprintf(self::COLUMN_PATH, $name),
            $definition
        );

        if (!empty($sorter)) {
            $this->addSorter($name, $sorter);
        }

        if (!empty($filter)) {
            $this->addFilter($name, $filter);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array  $definition
     *
     * @return self
     */
    public function addFilter($name, array $definition)
    {
        $this->offsetSetByPath(
            sprintf(self::FILTER_PATH, $name),
            $definition
        );

        return $this;
    }

    /**
     * @param string $name
     * @param array  $definition
     *
     * @return self
     */
    public function addSorter($name, array $definition)
    {
        $this->offsetSetByPath(
            sprintf(self::SORTER_PATH, $name),
            $definition
        );

        return $this;
    }

    /**
     * Remove column definition
     * should remove sorters as well and optionally filters
     *
     * @param string $name         column name from grid definition
     * @param bool   $removeFilter whether remove filter or not, true by default
     *
     * @return self
     */
    public function removeColumn($name, $removeFilter = true)
    {
        $this->offsetUnsetByPath(
            sprintf(self::COLUMN_PATH, $name)
        );

        $this->removeSorter($name);
        if ($removeFilter) {
            $this->removeFilter($name);
        }

        return $this;
    }

    /**
     * @param string $name column name
     */
    public function removeSorter($name)
    {
        $this->offsetUnsetByPath(
            sprintf(self::SORTER_PATH, $name)
        );
    }

    /**
     * Remove filter definition
     *
     * @param string $name column name
     *
     * @return self
     */
    public function removeFilter($name)
    {
        $this->offsetUnsetByPath(
            sprintf(self::FILTER_PATH, $name)
        );

        return $this;
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return self
     */
    public function addMassAction($name, array $options)
    {
        $this->offsetSetByPath(
            sprintf('[mass_actions][%s]', $name),
            $options
        );

        return $this;
    }

    /**
     * @param string $gridName
     * @return bool
     */
    public function isGridExtendedFrom($gridName)
    {
        $parentGrids = $this->offsetGetOr(SystemAwareResolver::KEY_EXTENDED_FROM, []);
        foreach ($parentGrids as $parentGridName) {
            if ($parentGridName === $gridName) {
                return true;
            }
        }
        return false;
    }

     /**
     * Gets the query hints.
     *
     * @return array
     */
    public function getHints()
    {
        return $this->offsetGetByPath(self::HINTS_PATH, []);
    }

    /**
     * Sets the query hints.
     *
     * @param array $hints
     *
     * @return self
     */
    public function setHints(array $hints)
    {
        $this->offsetSetByPath(self::HINTS_PATH, $hints);

        return $this;
    }

    /**
     * Removes the query hints.
     *
     * @return self
     */
    public function resetHints()
    {
        $this->offsetUnsetByPath(self::HINTS_PATH);

        return $this;
    }

    /**
     * Adds a hint to the query.
     *
     * @param string $name  The name of a hint.
     * @param mixed  $value The value of a hint.
     *
     * @return self
     */
    public function addHint($name, $value = null)
    {
        if ($value) {
            $this->offsetAddToArrayByPath(
                self::HINTS_PATH,
                [[self::NAME_KEY => $name, self::VALUE_KEY => $value]]
            );
        } else {
            $this->offsetAddToArrayByPath(self::HINTS_PATH, [$name]);
        }

        return $this;
    }
}
