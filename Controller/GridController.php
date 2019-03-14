<?php

namespace Pintushi\Bundle\GridBundle\Controller;

use Pintushi\Bundle\GridBundle\Grid\Manager;
use Pintushi\Bundle\SecurityBundle\Annotation\AclAncestor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Provides the ability to control of the Grid
 */
class GridController extends Controller
{
    private $gridManager;

    public function __construct(Manager $manager)
    {
        $this->gridManager = $manager;
    }

    /**
     * @Route(
     *     "/{gridName}/filter-metadata",
     *     name="pintushi_grid_filter_metadata",
     *     options={
     *         "expose"=true
     *     }
     * )
     */
    public function filterMetadataAction(Request $request, $gridName)
    {
        $filterNames = $request->query->get('filterNames', []);

        $gridManager = $this->gridManager;
        $gridConfig  = $gridManager->getConfigurationForGrid($gridName);
        $acl         = $gridConfig->getAclResource();

        if ($acl && !$this->isGranted($acl)) {
            throw new AccessDeniedException('Access denied.');
        }

        $grid = $gridManager->getGridByRequestParams($gridName);
        $meta = $grid->getResolvedMetadata();


        $filterData = [];
        foreach ($meta['filters'] as $filter) {
            if (!in_array($filter['name'], $filterNames)) {
                continue;
            }

            $filterData[$filter['name']] = $filter;
        }

        return new JsonResponse($filterData);
    }


    /**
     * @Route(
     *     "/{gridName}/metadata",
     *     name="pintushi_grid_metadata",
     *     options={
     *         "expose"=true
     *     }
     * )
     */
    public function gridMetadataAction(Request $request, $gridName)
    {
        $gridManager = $this->gridManager;
        $gridConfig  = $gridManager->getConfigurationForGrid($gridName);
        $acl         = $gridConfig->getAclResource();

        if ($acl && !$this->isGranted($acl)) {
            throw new AccessDeniedException('Access denied.');
        }

        $grid = $gridManager->getGridByRequestParams($gridName);
        $meta = $grid->getResolvedMetadata();

        return new JsonResponse([
            'filters' => $meta['filters'],
            'columns' => $meta['columns'],
            'grid_views' => $meta['gridViews'],
        ]);
    }
}
