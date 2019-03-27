<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Embedded;
use Hateoas\Configuration\Exclusion;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Hateoas\Configuration\Route;
use Hateoas\Configuration\Relation;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Hateoas\RelationProvider("getRelations")
 */
class ResultRecord
{
    /**
     * @Serializer\Expose
     * @Serializer\Inline
     */
    private $resource;

    /** @var array */
    private $metas = [];

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($name, $value)
    {
        $this->getPropertyAccessor()->setValue($this->metas, $name, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($name, $default = null)
    {
        return $this->getPropertyAccessor()->getValue($this->metas, $name) ?? $default;
    }

    /**
     * @return PropertyAccessorInterface
     */
    protected function getPropertyAccessor()
    {
        if (null === $this->propertyAccessor) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        $relations = [];

        $routes = $this->getValue('[routes]', []);

        foreach($routes as $name => $route) {
            $relations[] = new Relation(
                $name,
                new Route($route['route'], $route['params'])
            );
        }

        return $relations;
    }
}
