<?php

namespace Pintushi\Bundle\GridBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Pintushi\Bundle\GridBundle\Entity\AppearanceType;

class AppearanceTypeManager
{
    /** @var EntityManager */
    protected $em;

    /** @var  array */
    protected $appearanceTypes;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get appearance types
     *
     * @return array
     */
    public function getAppearanceTypes()
    {
        if (null === $this->appearanceTypes) {
            $this->appearanceTypes = [];
            $types = $this->em->getRepository('PintushiGridBundle:AppearanceType')->findAll();
            /** @var  $type AppearanceType */
            foreach ($types as $type) {
                $this->appearanceTypes[$type->getName()] = [
                    'label' => $type->getLabel(),
                    'icon'  => $type->getIcon()
                ];
            }
        }

        return $this->appearanceTypes;
    }
}
