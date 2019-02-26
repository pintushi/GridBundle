<?php

namespace Pintushi\Bundle\GridBundle\Extension\MassAction;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Datagrid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Datagrid\DatagridInterface;
use Pintushi\Bundle\GridBundle\Exception\RuntimeException;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Pintushi\Bundle\GridBundle\Extension\MassAction\Actions\MassActionInterface;
use Pintushi\Bundle\GridBundle\Provider\DatagridModeProvider;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MassActionExtension extends AbstractExtension
{
    const METADATA_ACTION_KEY = 'massActions';
    const ACTION_KEY          = 'mass_actions';
    const ALLOWED_REQUEST_TYPES   = 'allowedRequestTypes';
    const ALLOWED_REQUEST_METHODS = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'];

    /** @var MassActionFactory */
    protected $actionFactory;

    /** @var MassActionMetadataFactory */
    protected $actionMetadataFactory;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var CsrfTokenManagerInterface */
    protected $tokenManager;

    /** @var bool */
    protected $isMetadataVisited = false;

    /** {@inheritdoc} */
    protected $excludedModes = [
        DatagridModeProvider::DATAGRID_IMPORTEXPORT_MODE
    ];

    /**
     * @param MassActionFactory             $actionFactory
     * @param MassActionMetadataFactory     $actionMetadataFactory
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CsrfTokenManagerInterface     $tokenManager
     */
    public function __construct(
        MassActionFactory $actionFactory,
        MassActionMetadataFactory $actionMetadataFactory,
        AuthorizationCheckerInterface $authorizationChecker,
        CsrfTokenManagerInterface $tokenManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->actionMetadataFactory = $actionMetadataFactory;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenManager = $tokenManager;
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataObject $data)
    {
        $this->isMetadataVisited = true;
        $data->offsetAddToArray(self::METADATA_ACTION_KEY, $this->getActionsMetadata($config));
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(DatagridConfiguration $config, ResultsObject $result)
    {
        if (!$this->isMetadataVisited) {
            $result->offsetAddToArray(
                'metadata',
                [self::METADATA_ACTION_KEY => $this->getActionsMetadata($config)]
            );
        }
    }

    /**
     * Gets grid mass action by name.
     *
     * @param string            $name
     * @param DatagridInterface $datagrid
     *
     * @return MassActionInterface|null
     */
    public function getMassAction($name, DatagridInterface $datagrid)
    {
        $config = $datagrid->getAcceptor()->getConfig();
        if (!isset($config[self::ACTION_KEY][$name])) {
            return null;
        }

        return $this->createAction($name, $config[self::ACTION_KEY][$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        /**
         * should be applied before action extension
         * @see \Pintushi\Bundle\GridBundle\Extension\Action\ActionExtension::getPriority
         */
        return 205;
    }

    /**
     * @param DatagridConfiguration $config
     *
     * @return array
     */
    protected function getActionsMetadata(DatagridConfiguration $config)
    {
        $actionsMetadata = [];
        $actions = $config->offsetGetOr(self::ACTION_KEY, []);
        foreach ($actions as $actionName => $actionConfig) {
            $action = $this->createAction($actionName, $actionConfig);
            if (null !== $action) {
                $actionsMetadata[$action->getName()] = $this->createActionMetadata($action);
            }
        }

        return $actionsMetadata;
    }

    /**
     * @param string $actionName
     * @param array  $actionConfig
     *
     * @return MassActionInterface|null
     */
    protected function createAction($actionName, array $actionConfig)
    {
        $actionConfig['token'] = $this->tokenManager->getToken($actionName)->getValue();

        $action = $this->actionFactory->createAction($actionName, $actionConfig);
        $configuredTypes = $action->getOptions()->offsetGetByPath(self::ALLOWED_REQUEST_TYPES);

        if ($configuredTypes) {
            $foundTypes = array_intersect(array_map('strtoupper', $configuredTypes), self::ALLOWED_REQUEST_METHODS);
            if (count($foundTypes) !== count($configuredTypes)) {
                throw new RuntimeException(
                    sprintf(
                        'Action parameter "%s" contains wrong HTTP method. Given "%s", allowed: "%s".',
                        self::ALLOWED_REQUEST_TYPES,
                        implode(', ', $configuredTypes),
                        implode(', ', self::ALLOWED_REQUEST_METHODS)
                    )
                );
            }
        }

        $aclResource = $action->getAclResource();
        if ($aclResource && !$this->authorizationChecker->isGranted($aclResource)) {
            $action = null;
        }

        return $action;
    }

    /**
     * @param MassActionInterface $action
     *
     * @return array
     */
    protected function createActionMetadata(MassActionInterface $action)
    {
        return $this->actionMetadataFactory->createActionMetadata($action);
    }
}
