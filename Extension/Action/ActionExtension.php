<?php

namespace Pintushi\Bundle\GridBundle\Extension\Action;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Symfony\Component\Translation\TranslatorInterface;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Process actions metadata for frontend.;
 */
class ActionExtension extends AbstractExtension
{
    /** @var ColumnInterface[] */
    protected $actions = [];

    /** @var TranslatorInterface */
    protected $translator;

    protected $authorizationChecker;

     /** @var bool */
    protected $isMetadataVisited = false;

    public function __construct(
        TranslatorInterface $translator,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->translator = $translator;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        if (!parent::isApplicable($config)) {
            return false;
        }

        $actions    = $config->offsetGetOr(Configuration::ACTIONS_KEY, []);
        $applicable = $actions;
        $this->processConfigs($config);

        return $applicable;
    }


    /**
     * Validate configs nad fill default values
     *
     * @param GridConfiguration $config
     */
    public function processConfigs(GridConfiguration $config)
    {
        $actions    = $config->offsetGetOr(Configuration::ACTIONS_KEY, []);

        $configuration   = new Configuration();

        return parent::validateConfiguration($configuration, [Configuration::ACTIONS_KEY => $actions]);
    }

    /**
     * {@inheritdoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        $this->isMetadataVisited = true;

        $data->offsetAddToArray('actions', $this->getActionsMetadata($config));
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(GridConfiguration $config, ResultsObject $result)
    {
        if (!$this->isMetadataVisited) {
            $config->offsetAddToArray('actions', $this->getActionsMetadata($config));
        }

        $itemActions = $config->offsetGetByPath('[actions][item]', []);

        $data = $result->getData();

        foreach($data as $record) {
            /** @var ResultRecord $record */
            foreach($itemActions as $name => $actionConfig) {
                $aclResource = $actionConfig[Configuration::ACL_KEY];
                if ($aclResource && $this->authorizationChecker->isGranted($aclResource, $record->getResource())) {
                    $record->setValue(sprintf('[routes][%s]', $name), $actionConfig['link'] );
                }
            }
        }
    }

    protected function getActionsMetadata(GridConfiguration $config)
    {
        $actions = $config->offsetGetOr(Configuration::ACTIONS_KEY, []);
        $actionsMetadata = [];
        foreach ($actions as $groupName => $actionGroup) {
            foreach($actionGroup as $actionName => $actionConfig) {
                $label = $actionConfig['label'] ?? 'pintushi.grid.action.'.$actionName;

                $actionConfig = array_merge($actionConfig, [
                    'label' => $this->translator->trans($label)
                ]);

                $actionsMetadata[$groupName][$actionName] = $actionConfig;

                $aclResource = $actionConfig[Configuration::ACL_KEY];
                if ($aclResource && !$this->authorizationChecker->isGranted($aclResource)) {
                    dump($aclResource);
                    unset($actionsMetadata[$groupName][$actionName]);
                }
            }
        }

        return $actionsMetadata;
    }

    public function getPriority()
    {
        return -400;
    }
}
