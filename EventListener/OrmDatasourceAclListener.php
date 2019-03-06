<?php

namespace Pintushi\Bundle\GridBundle\EventListener;

use Pintushi\Bundle\GridBundle\Event\OrmResultBefore;
use Pintushi\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class OrmDatasourceAclListener
{
    const EDIT_SCOPE = 'edit';

    /** @var AclHelper */
    protected $aclHelper;

    /**
     * @param AclHelper $aclHelper
     */
    public function __construct(AclHelper $aclHelper)
    {
        $this->aclHelper = $aclHelper;
    }

    /**
     * @param OrmResultBefore $event
     */
    public function onResultBefore(OrmResultBefore $event)
    {
        $dataGrid = $event->getGrid();
        $config = $dataGrid->getConfig();
        if (!$config->isDatasourceSkipAclApply()) {
            $permission = $this->getPermission($dataGrid->getScope());
            $this->aclHelper->apply($event->getQuery(), $permission);
        }
    }

    /**
     * @param string|null $scope
     *
     * @return string
     */
    protected function getPermission($scope)
    {
        if (self::EDIT_SCOPE === $scope) {
            return 'EDIT';
        }

        return 'VIEW';
    }
}
