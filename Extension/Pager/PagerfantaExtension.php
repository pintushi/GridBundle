<?php

namespace Pintushi\Bundle\GridBundle\Extension\Pager;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\DatasourceInterface;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmDatasource;
use Pintushi\Bundle\GridBundle\Extension\Mode\ModeExtension;
use Pintushi\Bundle\GridBundle\Extension\Toolbar\ToolbarExtension;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Pagerfanta\Pagerfanta;
use Hateoas\Configuration\Route;

/**
 * Responsibility of this extension is to apply pagination on query for ORM datasource
 */
class PagerfantaExtension extends AbstractExtension
{
     /**
     * Pager parameters
     */
    const PAGER_ROOT_PARAM = '_pager';
    const PAGE_PARAM       = '_page';
    const PER_PAGE_PARAM   = '_per_page';
    const DISABLED_PARAM   = '_disabled';

    const MINIFIED_PAGE_PARAM     = 'i';
    const MINIFIED_PER_PAGE_PARAM = 'p';

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        $defaultPage = 1;
        $defaultPerPage = $config->offsetGetByPath(ToolbarExtension::PAGER_DEFAULT_PER_PAGE_OPTION_PATH, 10);

        $initialState = [
            'currentPage' => $defaultPage,
            'pageSize' => $defaultPerPage
        ];
        $state = [
            'currentPage' => $this->getOr(self::PAGE_PARAM, $defaultPage),
            'pageSize' => $this->getOr(self::PER_PAGE_PARAM, $defaultPerPage)
        ];

        $data->offsetAddToArray('initialState', $initialState);
        $data->offsetAddToArray('state', $state);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        // Pager should proceed closest to end of accepting chain
        return -240;
    }

    /**
     * @param ParameterBag $parameters
     */
    public function setParameters(ParameterBag $parameters)
    {
        if ($parameters->has(ParameterBag::MINIFIED_PARAMETERS)) {
            $minifiedParameters = $parameters->get(ParameterBag::MINIFIED_PARAMETERS);
            $pager = [];

            if (array_key_exists(self::MINIFIED_PAGE_PARAM, $minifiedParameters)) {
                $pager[self::PAGE_PARAM] = $minifiedParameters[self::MINIFIED_PAGE_PARAM];
            }
            if (array_key_exists(self::MINIFIED_PER_PAGE_PARAM, $minifiedParameters)) {
                $pager[self::PER_PAGE_PARAM] = $minifiedParameters[self::MINIFIED_PER_PAGE_PARAM];
            }

            $parameters->set(self::PAGER_ROOT_PARAM, $pager);
        }

        parent::setParameters($parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        return
            parent::isApplicable($config)
            && !$this->getOr(self::DISABLED_PARAM, false)
            && !$config->offsetGetByPath(ToolbarExtension::TOOLBAR_PAGINATION_HIDE_OPTION_PATH, false);
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(GridConfiguration $config, ResultsObject $result)
    {
        /** @var Pagerfanta */
        $paginator = $result->getData();

        $onePage = $config->offsetGetByPath(ToolbarExtension::PAGER_ONE_PAGE_OPTION_PATH, false);
        $mode = $config->offsetGetByPath(ModeExtension::MODE_OPTION_PATH);
        $perPageLimit = $config->offsetGetByPath(ToolbarExtension::PAGER_DEFAULT_PER_PAGE_OPTION_PATH);
        $defaultPerPage = $config->offsetGetByPath(ToolbarExtension::PAGER_DEFAULT_PER_PAGE_OPTION_PATH, 10);
        $perPageCount = $this->getOr(self::PER_PAGE_PARAM, $defaultPerPage);

        if ((!$perPageLimit && $onePage) || $mode === ModeExtension::MODE_CLIENT) {
            // no restrictions applied
            $paginator->setCurrentPage(0);
            $paginator->setMaxPerPage(0);
        } elseif ($onePage && $perPageLimit) {
            // one page with limit
            $paginator->setCurrentPage(0);
            $paginator->setMaxPerPage($perPageCount);
        } else {
            $paginator->setCurrentPage($this->getOr(self::PAGE_PARAM, 1));
            $paginator->setMaxPerPage($perPageCount);
        }
    }

        /**
     * Get param or return specified default value
     *
     * @param string $paramName
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getOr($paramName, $default = null)
    {
        $pagerParameters = $this->getParameters()->get(self::PAGER_ROOT_PARAM, []);

        return isset($pagerParameters[$paramName]) ? $pagerParameters[$paramName] : $default;
    }
}
