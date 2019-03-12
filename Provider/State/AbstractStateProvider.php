<?php

namespace Pintushi\Bundle\GridBundle\Provider\State;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;
use Pintushi\Bundle\GridBundle\Entity\AbstractGridView;
use Pintushi\Bundle\GridBundle\Manager\GridViewManager;
use Pintushi\Bundle\GridBundle\Extension\GridViews\GridViewsExtension;
use Pintushi\Bundle\GridBundle\Extension\GridViews\View;
use Pintushi\Bundle\GridBundle\Extension\GridViews\ViewInterface;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

/**
 * Contains base methods for grid state providers which are trying to fetch data from grid views.
 */
abstract class AbstractStateProvider implements GridStateProviderInterface
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
     * @param GridConfiguration $gridConfiguration
     * @param ParameterBag $gridParameters
     *
     * @return ViewInterface|null
     */
    protected function getActualGridView(
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    ): ?ViewInterface {
        if ($this->gridViewsDisabled($gridParameters)) {
            return null;
        }

        $gridName = $gridConfiguration->getName();

        return $this->getCurrentGridView($gridParameters, $gridName) ?: $this->getDefaultGridView($gridName);
    }

    /**
     * Gets id for current grid view
     *
     * @param ParameterBag $gridParameters
     *
     * @return int|string|null
     */
    private function getCurrentGridViewId(ParameterBag $gridParameters)
    {
        $additionalParameters = $gridParameters->get(ParameterBag::ADDITIONAL_PARAMETERS, []);

        return $additionalParameters[GridViewsExtension::VIEWS_PARAM_KEY] ?? null;
    }

    /**
     * @param ParameterBag $gridParameters
     * @param $gridName
     *
     * @return View|null
     */
    private function getCurrentGridView(ParameterBag $gridParameters, $gridName)
    {
        $currentGridViewId = $this->getCurrentGridViewId($gridParameters);
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
     * @param ParameterBag $gridParameters
     *
     * @return bool
     */
    private function gridViewsDisabled(ParameterBag $gridParameters): bool
    {
        $parameters = $gridParameters->get(GridViewsExtension::GRID_VIEW_ROOT_PARAM, []);

        return !empty($parameters[GridViewsExtension::DISABLED_PARAM]);
    }
}
