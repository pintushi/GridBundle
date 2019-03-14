<?php

namespace Pintushi\Bundle\GridBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Pintushi\Bundle\GridBundle\Manager\GridViewManager;
use Pintushi\Bundle\GridBundle\Entity\AbstractGridView;

class GridViewController extends Controller
{
    /** @var GridViewManager */
    protected $gridViewManager;

    public function __construct(
        GridViewManager $gridViewManager
    ) {
        $this->gridViewManager = $gridViewManager;
    }

    public function handle(AbstractGridView $data, FormInterface $form, Request $request)
    {
        $this->checkCreateSharedAccess($request);

        $default = $form->get('isDefault')->getData();

        $this->setDefaultGridView($data, $default);

        $this->fixFilters($data);

        return $data;
    }

     /**
     * @param AbstractGridView $gridView
     * @param bool $default
     */
    protected function setDefaultGridView(AbstractGridView $gridView, $default)
    {
        $this->gridViewManager->setDefaultGridView($this->getUser(), $gridView, $default);
    }

    /**
     * @todo Remove once https://github.com/symfony/symfony/issues/5906 is fixed.
     *       After removing this method PLEASE CHECK saving filters in grid view
     *       look in CollectionFiltersManager._onChangeFilterSelect()
     *       Added fix for dictionary filters also.
     *
     * @param AbstractGridView $gridView
     */
    protected function fixFilters(AbstractGridView $gridView)
    {
        $filters = $gridView->getFiltersData();
        foreach ($filters as $name => $filter) {
            if (is_array($filter) && array_key_exists('type', $filter) && $filter['type'] == null) {
                $filters[$name]['type'] = '';
            }
            if (is_array($filter) && is_array($filter['value'])) {
                foreach ($filter['value'] as $k => $value) {
                    if (is_array($value)) {
                        $filters[$name]['value'][$k] = $value['value'];
                    }
                }
            }
        }

        $gridView->setFiltersData($filters);
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedException
     */
    protected function checkCreateSharedAccess(Request $request)
    {
        if ($request->request->get('type') !== AbstractGridView::TYPE_PUBLIC) {
            return;
        }

        if ($this->isGridViewPublishGranted()) {
            return;
        }

        throw new AccessDeniedException();
    }

    /**
     * @return bool
     */
    protected function isGridViewPublishGranted()
    {
        return $this->isGranted('pintushi_grid_gridview_publish');
    }
}
