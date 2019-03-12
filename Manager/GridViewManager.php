<?php

namespace Pintushi\Bundle\GridBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectRepository;
use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Manager;
use Pintushi\Bundle\GridBundle\Entity\AbstractGridViewUser;
use Pintushi\Bundle\GridBundle\Entity\GridView;
use Pintushi\Bundle\GridBundle\Entity\GridViewUser;
use Pintushi\Bundle\GridBundle\Repository\GridViewRepository;
use Pintushi\Bundle\GridBundle\Repository\GridViewUserRepository;
use Pintushi\Bundle\GridBundle\Extension\GridViews\GridViewsExtension;
use Pintushi\Bundle\GridBundle\Extension\GridViews\View;
use Pintushi\Bundle\GridBundle\Extension\GridViews\ViewInterface;
use Pintushi\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Pintushi\Bundle\UserBundle\Entity\AbstractUser;
use Pintushi\Bundle\UserBundle\Entity\UserInterface;
use Pintushi\Component\PhpUtils\ArrayUtil;

class GridViewManager
{
    const DEFAULT_VIEW_KEY = 'default_view';
    const SYSTEM_VIEWS_KEY = 'system_views';
    const ALL_VIEWS_KEY    = 'all_views';

    /** @var AclHelper */
    protected $aclHelper;

    /** @var Registry */
    protected $registry;

    /** @var  Manager */
    protected $gridManager;

    /** @var array */
    protected $cacheData = [];

    /** @var  array */
    protected $appearanceTypes;

    /** @var array */
    protected $gridConfigurations = [];

    /** @var string */
    protected $gridViewClassName;

    /** @var string */
    protected $gridViewUserClassName;

    /**
     * @param AclHelper $aclHelper
     * @param Registry $registry
     * @param Manager $gridManager
     * @param RestrictionManager $restrictionManager
     */
    public function __construct(
        AclHelper $aclHelper,
        Registry $registry,
        Manager $gridManager
    ) {
        $this->aclHelper = $aclHelper;
        $this->registry = $registry;
        $this->gridManager = $gridManager;
        $this->gridViewClassName = GridView::class;
        $this->gridViewUserClassName = GridViewUser::class;
    }

    /**
     * @param string $gridViewClassName
     */
    public function setGridViewClassName($gridViewClassName)
    {
        $this->gridViewClassName = $gridViewClassName;
    }

    /**
     * @param string $gridViewUserClassName
     */
    public function setGridViewUserClassName($gridViewUserClassName)
    {
        $this->gridViewUserClassName = $gridViewUserClassName;
    }

    /**
     * @param AbstractUser $user
     * @param ViewInterface $gridView
     * @param bool $default
     */
    public function setDefaultGridView(AbstractUser $user, ViewInterface $gridView, $default = true)
    {
        if ($default) {
            $isGridViewDefault = $this->isViewDefault($gridView, $user);
            // Checks if default grid view changed
            if (!$isGridViewDefault) {
                /** @var GridViewRepository $repository */
                $gridName = $gridView->getGridName();
                $om = $this->registry->getManagerForClass($this->gridViewUserClassName);
                $repository = $om->getRepository($this->gridViewUserClassName);
                $userViews = $repository->findDefaultGridViews($this->aclHelper, $user, $gridName, false);
                foreach ($userViews as $userView) {
                    $om->remove($userView);
                }

                $userView = $this->createGridViewUser();
                $userView->setAlias($gridView->getName());
                $userView->setUser($user);
                $userView->setGridName($gridName);
                if (is_a($gridView, $this->gridViewClassName, true)) {
                    $userView->setGridView($gridView);
                }
                $om->persist($userView);
            }
        }
    }

    /**
     * @param ViewInterface $view
     * @param AbstractUser $user
     * @return bool
     */
    protected function isViewDefault(ViewInterface $view, AbstractUser $user)
    {
        /** @var GridViewUserRepository $repository */
        $repository = $this->getRepository($this->gridViewUserClassName);

        return (bool) $repository->findByGridViewAndUser($view, $user);
    }

    /**
     * @param $id
     * @param $gridName
     * @return null|View
     */
    public function getSystemView($id, $gridName)
    {
        $gridViews = $this->getSystemViews($gridName);
        foreach ($gridViews as $gridView) {
            if ($gridView->getName() == $id) {
                return $gridView;
            }
        }

        return null;
    }

    /**
     * Get all system views by gridName
     *
     * @param $gridName
     * @return array
     */
    public function getSystemViews($gridName)
    {
        $cacheKey = sprintf('%s.%s', self::SYSTEM_VIEWS_KEY, $gridName);
        if (!isset($this->cacheData[$cacheKey])) {
            $config = $this->getConfigurationForGrid($gridName);
            $list = $config->offsetGetOr(GridViewsExtension::VIEWS_LIST_KEY, false);
            $gridViews[] = new View(GridViewsExtension::DEFAULT_VIEW_ID);
            if ($list) {
                $list = $this->applyAppearanceRestrictions($list->getList()->getValues(), $gridName);
                $gridViews = array_merge($gridViews, $list);
            }
            $this->cacheData[$cacheKey] = $gridViews;
        }

        return $this->cacheData[$cacheKey];
    }

    /**
     * @param AbstractUser|null  $user
     * @param string $gridName
     *
     * @return array
     */
    public function getAllGridViews(AbstractUser $user = null, $gridName = null)
    {
        $cacheKey = sprintf('%s.%s.%s', self::ALL_VIEWS_KEY, $user ? $user->getUsername() : (string) $user, $gridName);
        if (!isset($this->cacheData[$cacheKey])) {
            $systemViews = $this->getSystemViews($gridName);
            $gridViews = [];
            if ($user instanceof UserInterface) {
                /** @var GridViewRepository $repository */
                $repository = $this->getRepository($this->gridViewClassName);

                $gridViews = $repository->findGridViews($this->aclHelper, $user, $gridName);
                $gridViews = $this->applyAppearanceRestrictions($gridViews, $gridName);
            }
            $this->cacheData[$cacheKey] = [
                'system' => $systemViews,
                'user' => $gridViews
            ];
        }

        return $this->cacheData[$cacheKey];
    }

    /**
     * Get default view from all views (user made, system, etc)
     *
     * @param AbstractUser $user
     * @param string $gridName
     *
     * @return mixed
     */
    public function getDefaultView(AbstractUser $user, $gridName)
    {
        $cacheKey = sprintf('%s.%s.%s', self::DEFAULT_VIEW_KEY, $user->getUsername(), $gridName);
        if (!array_key_exists($cacheKey, $this->cacheData)) {
            /** @var GridViewUserRepository $gridViewRepository */
            $gridViewRepository = $this->getRepository($this->gridViewUserClassName);
            $default = $gridViewRepository->findDefaultGridView($this->aclHelper, $user, $gridName);
            $systemViews = $this->getSystemViews($gridName);
            if (!$default) {
                $defaultView = ArrayUtil::find(
                    function ($systemView) {
                        return $systemView->isDefault();
                    },
                    $systemViews
                );
            } elseif ($default->getGridView()) {
                $defaultView = $this->getRepository($this->gridViewClassName)->find($default->getGridView());
            } else {
                $defaultView = ArrayUtil::find(
                    function ($systemView) use ($default) {
                        return $default->getAlias() == $systemView->getName();
                    },
                    $systemViews
                );
            }
            /**
             * If default view is restricted, fallback to the default system view
             */
            if ($defaultView && !$this->applyAppearanceRestrictions([$defaultView], $gridName)) {
                $defaultView = ArrayUtil::find(
                    function ($systemView) {
                        return $systemView->getName() === GridViewsExtension::DEFAULT_VIEW_ID;
                    },
                    $systemViews
                );
            }
            $this->cacheData[$cacheKey] = $defaultView;
        }

        return $this->cacheData[$cacheKey];
    }

    /**
     * @param $id
     * @param $default (if $default=0 all views will unset as default and __all__ view will be set
     * @param $gridName
     * @return null|View
     */
    public function getView($id, $default, $gridName)
    {
        if (!$default) {
            $gridView = new View(GridViewsExtension::DEFAULT_VIEW_ID);
        } else {
            $gridView = $this->getSystemView($id, $gridName);
            if (!$gridView) {
                $gridView = $this->getRepository($this->gridViewClassName)->find($id);
            }
        }

        if ($gridView instanceof ViewInterface) {
            $gridView->setGridName($gridName);
        }

        return $gridView;
    }

    /**
     * @param string $gridName
     * @return GridConfiguration
     */
    protected function getConfigurationForGrid($gridName)
    {
        if (!isset($this->gridConfigurations[$gridName])) {
            $this->gridConfigurations[$gridName] = $this->gridManager->getConfigurationForGrid($gridName);
        }

        return $this->gridConfigurations[$gridName];
    }

    /**
     * @param ViewInterface[] $views
     * @param string $gridName
     * @return ViewInterface[]
     */
    protected function applyAppearanceRestrictions($views, $gridName)
    {
        $config = $this->getConfigurationForGrid($gridName);


        return $views;
    }

    /**
     * @return AbstractGridViewUser
     */
    private function createGridViewUser()
    {
        return new $this->gridViewUserClassName();
    }

    /**
     * @param string $className
     * @return ObjectRepository
     */
    private function getRepository($className)
    {
        return $this->registry->getManagerForClass($className)->getRepository($className);
    }
}
