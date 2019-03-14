<?php

namespace Pintushi\Bundle\GridBundle\Form\Type;

use Pintushi\Bundle\GridBundle\Entity\GridView;
use Pintushi\Bundle\GridBundle\Entity\AppearanceType;
use Pintushi\Bundle\FormBundle\Form\Type\JsonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GridViewType extends AbstractType
{
    /**
     * Example of usage:
     *     Sorters options choices:
     *     '-1': 'ASC',
     *     '1': 'DESC'
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'pintushi.grid.gridview.name',
            ])
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
                'mapped'   => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => GridView::getTypes(),
            ])
            ->add('gridName', TextType::class, [
                'property_path' => 'gridName',
            ])
            ->add('appearanceType', EntityType::class, [
                'class' => AppearanceType::class,
                'property_path' => 'appearanceType',
                'required' => false,
            ])
            ->add('appearanceData', JsonType::class, [
                'empty_data'    => [],
                'required' => false,
            ])
            ->add('filters', JsonType::class, [
                'property_path' => 'filtersData',
                'empty_data'    => [],
            ])
            ->add('sorters', CollectionType::class, [
                'property_path'  => 'sortersData',
                'error_bubbling' => false,
                'allow_add'      => true,
                'allow_delete'   => true,
                'entry_type'     => ChoiceType::class,
                'entry_options'  => [
                    'choices' => [
                        1  => 1,
                        -1 => -1
                    ],
                ],
            ])
            ->add('columns', JsonType::class, [
                'property_path' => 'columnsData',
                'empty_data'    => []
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GridView::class,
            'ownership_disabled' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pintushi_grid_grid_view';
    }
}
