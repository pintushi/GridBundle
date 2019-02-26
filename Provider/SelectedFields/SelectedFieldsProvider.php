<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Datagrid\Common\DatagridConfiguration;
use Pintushi\Bundle\GridBundle\Datagrid\ParameterBag;

/**
 * Composite provider which returns selected fields from all inner providers.
 */
class SelectedFieldsProvider implements SelectedFieldsProviderInterface
{
    /** @var SelectedFieldsProviderInterface[] */
    private $selectedFieldsProviders = [];

    /**
     * @param SelectedFieldsProviderInterface $selectedFieldsProvider
     */
    public function addSelectedFieldsProvider(SelectedFieldsProviderInterface $selectedFieldsProvider)
    {
        $this->selectedFieldsProviders[] = $selectedFieldsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedFields(
        DatagridConfiguration $datagridConfiguration,
        ParameterBag $datagridParameters
    ): array {
        $selectedFields = [[]];
        foreach ($this->selectedFieldsProviders as $selectedFieldsProvider) {
            $selectedFields[] = $selectedFieldsProvider
                ->getSelectedFields($datagridConfiguration, $datagridParameters);
        }

        return array_unique(array_merge(...$selectedFields));
    }
}
