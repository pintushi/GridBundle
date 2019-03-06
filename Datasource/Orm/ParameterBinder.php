<?php

namespace Pintushi\Bundle\GridBundle\Datasource\Orm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Pintushi\Bundle\GridBundle\Grid\GridInterface;
use Pintushi\Bundle\GridBundle\Datasource\ParameterBinderInterface;
use Pintushi\Bundle\GridBundle\Exception\InvalidArgumentException;
use Oro\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * Binds parameters from grid to it's datasource query
 */
class ParameterBinder implements ParameterBinderInterface
{
    /**
     * Binds grid parameters to datasource query.
     *
     * Example of usage:
     * <code>
     *  // get parameter "name" from grid parameter bag and add it to datasource query
     *  $queryParameterBinder->bindParameters($grid, ['name']);
     *
     *  // get parameter "id" from grid parameter bag and add it to datasource query as parameter "client_id"
     *  $queryParameterBinder->bindParameters($grid, ['client_id' => 'id']);
     *
     *  // get parameter "email" from grid parameter bag and add it to datasource query, all other existing query
     *  // parameters will be cleared
     *  $queryParameterBinder->bindParameters($grid, ['email'], false);
     *
     *  // get parameter with path "_parameters.data_in" from grid parameter
     *  // and add it to datasource query as parameter "data_in"
     *  $queryParameterBinder->bindParameters($grid, ['data_in' => '_parameters.data_in']);
     *
     *  // get parameter with path "_parameters.data_in" from grid parameter
     *  // and add it to datasource query as parameter "data_in",
     *  // if parameter is not exist, set default value - empty array,
     *  // and do the same for data_not_in
     *  $queryParameterBinder->bindParameters(
     *      $grid,
     *      [
     *          'data_in' => [
     *              'path' => '_parameters.data_in',
     *              'default' => [],
     *          ],
     *          [
     *              'name' => 'data_not_in'
     *              'path' => '_parameters.data_not_in',
     *              'default' => [],
     *          ]
     *      ]
     *  );
     * </code>
     *
     * @param GridInterface $grid
     * @param array $datasourceToGridParameters
     * @param bool $append
     * @throws InvalidArgumentException When datasource of grid is not ORM
     * @throws NoSuchPropertyException When grid has no parameter with specified name or path
     */
    public function bindParameters(
        GridInterface $grid,
        array $datasourceToGridParameters,
        $append = true
    ) {
        if (!$datasourceToGridParameters) {
            return;
        }

        $datasource = $grid->getDatasource();

        if (!$datasource instanceof OrmDatasource) {
            throw new InvalidArgumentException(
                sprintf(
                    'Grid datasource has unexpected type "%s", "%s" is expected.',
                    get_class($datasource),
                    'Pintushi\Bundle\GridBundle\Datasource\Orm\OrmDatasource'
                )
            );
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $datasource->getQueryBuilder();

        $queryParameters = $queryBuilder->getParameters();

        if (!$append) {
            $queryParameters->clear();
        }

        $gridParameters = $grid->getParameters()->all();

        foreach ($datasourceToGridParameters as $index => $value) {
            $config = $this->parseArrayParameterConfig($index, $value);

            $value = $this->getParameterValue($gridParameters, $config);
            $type = isset($config['type']) ? $config['type'] : null;

            $this->addOrReplaceParameter($queryParameters, new Parameter($config['name'], $value, $type));
        }
    }

    /**
     * @param string $index
     * @param string|array $value
     * @return array
     * @throws InvalidArgumentException
     */
    protected function parseArrayParameterConfig($index, $value)
    {
        if (!is_array($value)) {
            if (is_numeric($index)) {
                $config = ['name' => $value, 'path' => $value];
            } else {
                $config = ['name' => $index, 'path' => $value];
            }
        } else {
            $config = $value;
        }

        if (empty($config['name']) && !is_numeric($index)) {
            $config['name'] = $index;
        }

        if (empty($config['name'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot bind parameter to data source, expected bind parameter format is a string ' .
                    'or array with required "name" key, actual array keys are "%s".',
                    implode('", "', array_keys($config))
                )
            );
        }

        if (empty($config['path'])) {
            $config['path'] = $config['name'];
        }

        return $config;
    }

    /**
     * @param array $source
     * @param array $config
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function getParameterValue($source, array $config)
    {
        $propertyAccessor = new PropertyAccessor();
        try {
            $path = '';
            foreach (explode('.', $config['path']) as $part) {
                $path .= '[' . $part . ']';
            }
            $result = $propertyAccessor->getValue($source, $path);
        } catch (NoSuchPropertyException $exception) {
            if (array_key_exists('default', $config)) {
                $result = $config['default'];
            } else {
                throw new InvalidArgumentException(
                    sprintf(
                        'Cannot bind datasource parameter "%s", there is no grid parameter with path "%s".',
                        $config['name'],
                        $config['path']
                    ),
                    0,
                    $exception
                );
            }
        }
        if ((null === $result || $result === [] || $result === ['']) && isset($config['default'])) {
            $result = $config['default'];
        }

        return $result;
    }

    /**
     * Adds parameter to collection and removes all other parameters with same name.
     *
     * @param ArrayCollection $parameters
     * @param Parameter $newParameter
     */
    protected function addOrReplaceParameter(ArrayCollection $parameters, Parameter $newParameter)
    {
        $removeParameters = [];

        /** @var Parameter $parameter */
        foreach ($parameters->getValues() as $parameter) {
            if ($parameter->getName() === $newParameter->getName()) {
                $removeParameters[] = $parameter;
            }
        }

        foreach ($removeParameters as $removeParameter) {
            $parameters->removeElement($removeParameter);
        }

        $parameters->add($newParameter);
    }
}
