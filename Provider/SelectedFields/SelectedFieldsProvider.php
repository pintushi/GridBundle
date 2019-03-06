<?php

namespace Pintushi\Bundle\GridBundle\Provider\SelectedFields;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\ParameterBag;

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
        GridConfiguration $gridConfiguration,
        ParameterBag $gridParameters
    ): array {
        $selectedFields = [[]];
        foreach ($this->selectedFieldsProviders as $selectedFieldsProvider) {
            $selectedFields[] = $selectedFieldsProvider
                ->getSelectedFields($gridConfiguration, $gridParameters);
        }

        return array_unique(array_merge(...$selectedFields));
    }
}
