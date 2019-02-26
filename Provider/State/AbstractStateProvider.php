<?php

namespace Pintushi\Bundle\GridBundle\Provider\State;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;
use Pintushi\Bundle\GridBundle\Entity\AbstractGridView;
use Pintushi\Bundle\GridBundle\Entity\Manager\GridViewManager;
use Pintushi\Bundle\GridBundle\Extension\GridViews\GridViewsExtension;
use Pintushi\Bundle\GridBundle\Extension\GridViews\View;
use Pintushi\Bundle\GridBundle\Extension\GridViews\ViewInterface;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * Contains base methods for datagrid state providers which are trying to fetch data from grid views.
 */
abstract class AbstractStateProvider implements DatagridStateProviderInterface
{
    /** @var TokenAccessorInterface */
    private $tokenAccessor;

    /** @var GridViewManager */
    private $gridViewManager;

    /** @var array */
    private $defaultGridView = [];

    /**
     * @param GridViewManager $gridViewManager
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function __construct(GridViewManager $gridViewManager, TokenAccessorInterface $tokenAccessor)
    {
        $this->gridViewManager = $gridViewManager;
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * @param DatagridConfiguration $datagridConfiguration
     * @param ParameterBag $datagridParameters
     *
     * @return ViewInterface|null
     */
    protected function getActualGridView(
        DatagridConfiguration $datagridConfiguration,
        ParameterBag $datagridParameters
    ): ?ViewInterface {
        if ($this->gridViewsDisabled($datagridParameters)) {
            return null;
        }

        $gridName = $datagridConfiguration->getName();

        return $this->getCurrentGridView($datagridParameters, $gridName) ?: $this->getDefaultGridView($gridName);
    }

    /**
     * Gets id for current grid view
     *
     * @param ParameterBag $datagridParameters
     *
     * @return int|string|null
     */
    private function getCurrentGridViewId(ParameterBag $datagridParameters)
    {
        $additionalParameters = $datagridParameters->get(ParameterBag::ADDITIONAL_PARAMETERS, []);

        return $additionalParameters[GridViewsExtension::VIEWS_PARAM_KEY] ?? null;
    }

    /**
     * @param ParameterBag $datagridParameters
     * @param $gridName
     *
     * @return View|null
     */
    private function getCurrentGridView(ParameterBag $datagridParameters, $gridName)
    {
        $currentGridViewId = $this->getCurrentGridViewId($datagridParameters);
        if ($currentGridViewId !== null) {
            $gridView = $this->gridViewManager->getView($currentGridViewId, 1, $gridName);
        }

        return $gridView ?? null;
    }

    /**
     * Gets defined as default grid view for current logged user.
     *
     * @param string $gridName
     *
     * @return AbstractGridView|null
     */
    private function getDefaultGridView(string $gridName)
    {
        if (!array_key_exists($gridName, $this->defaultGridView)) {
            $currentUser = $this->tokenAccessor->getUser();
            if (null === $currentUser) {
                return null;
            }
            $this->defaultGridView[$gridName] = $this->gridViewManager->getDefaultView($currentUser, $gridName);
        }

        return $this->defaultGridView[$gridName];
    }

    /**
     * @param ParameterBag $datagridParameters
     *
     * @return bool
     */
    private function gridViewsDisabled(ParameterBag $datagridParameters): bool
    {
        $parameters = $datagridParameters->get(GridViewsExtension::GRID_VIEW_ROOT_PARAM, []);

        return !empty($parameters[GridViewsExtension::DISABLED_PARAM]);
    }
}
