<?php

namespace Pintushi\Bundle\GridBundle\Provider;

use Pintushi\Component\PhpUtils\ArrayUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SystemAwareResolver implements ContainerAwareInterface
{
    const KEY_EXTENDS       = 'extends';
    const KEY_EXTENDED_FROM = 'extended_from';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array parent configuration array node
     */
    protected $parentNode;

    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param string $gridName
     * @param array  $gridDefinition
     * @param bool   $recursion
     *
     * @return array
     */
    public function resolve($gridName, $gridDefinition, $recursion = false)
    {
        foreach ($gridDefinition as $key => $val) {
            if (is_array($val)) {
                $this->parentNode         = $val;
                $gridDefinition[$key] = $this->resolve($gridName, $val, true);
                continue;
            }

            $val = $this->resolveSystemCall($gridName, $key, $val);
            if (!$recursion && self::KEY_EXTENDS === $key) {
                // get parent grid definition, resolved
                $definition = $this->container
                    ->get('pintushi_grid.grid.manager')
                    ->getConfigurationForGrid($val);

                // merge them and remove extend directive
                $gridDefinition = ArrayUtil::arrayMergeRecursiveDistinct(
                    $definition->toArray(),
                    $gridDefinition
                );
                unset($gridDefinition['extends']);

                $gridDefinition[self::KEY_EXTENDED_FROM]   = isset($gridDefinition[self::KEY_EXTENDED_FROM]) ?
                    $gridDefinition[self::KEY_EXTENDED_FROM] : [];
                $gridDefinition[self::KEY_EXTENDED_FROM][] = $val;

                // run resolve again on merged grid definition
                $gridDefinition = $this->resolve($val, $gridDefinition);

                // break current loop cause we've just extended grid definition
                break;
            }

            $gridDefinition[$key] = $val;
        }

        return $gridDefinition;
    }

    /**
     * Replace static call, service call or constant access notation to value they returned
     * while building grid
     *
     * @param string $gridName
     * @param string $key key from grid definition (columns, filters, sorters, etc)
     * @param string $val value to be resolved/replaced
     *
     * @return mixed
     */
    protected function resolveSystemCall($gridName, $key, $val)
    {
        // resolve only scalar value, if it's not - value was already resolved
        // this can happen in case of extended grid definitions
        if (!is_scalar($val)) {
            return $val;
        }

        if (is_scalar($val) && strpos($val, '%') !== false) {
            $val = $this->resolveClassName($val);
        }

        while (is_scalar($val) && strpos($val, '::') !== false) {
            $newVal = $this->resolveStatic($gridName, $key, $val);
            if ($newVal == $val) {
                break;
            }
            $val = $newVal;
        }

        if (is_scalar($val) && strpos($val, '@') !== false) {
            $val = $this->resolveService($gridName, $key, $val);
        }

        return $val;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param string $val
     * @return mixed
     */
    protected function resolveClassName($val)
    {
        if (preg_match('#%([^\'":\s]+)%#', $val, $match)) {
            $matchedString = $match[0];
            $class = $match[1];

            return $this->replaceValueInString(
                $val,
                $matchedString,
                $this->container->getParameter($class)
            );
        }

        return $val;
    }

    /**
     * Resolve static call class:method or class::const
     *
     * @param string $gridName
     * @param string $key
     * @param string $val
     * @return mixed
     */
    protected function resolveStatic($gridName, $key, $val)
    {
        if (preg_match('#([^\'"%:\s]+)::([\w\._]+)#', $val, $match)) {
            $matchedString = $match[0];
            $class = $match[1];
            $method = $match[2];

            $classMethod = [$class, $method];
            if (is_callable($classMethod)) {
                return $this->replaceValueInString(
                    $val,
                    $matchedString,
                    call_user_func_array($classMethod, [$gridName, $key, $this->parentNode])
                );
            } elseif (defined(implode('::', $classMethod))) {
                return $this->replaceValueInString(
                    $val,
                    $matchedString,
                    constant(implode('::', $classMethod))
                );
            }
        }

        return $val;
    }

    /**
     * Resolve service or service->method call.
     *
     * @param string $gridName
     * @param string $key
     * @param string $val
     * @return mixed
     */
    protected function resolveService($gridName, $key, $val)
    {
        if (strpos($val, '\@') !== false) {
            return str_replace('\@', '@', $val);
        }

        $serviceRegex = '@(?P<lazy>\??)(?P<service>[\w\.]+)';
        $methodRegex = '(?P<method>\w+)';
        $argumentsRegex = '(?P<arguments>\(.*?\))';

        $service = null;
        $method = null;
        $arguments = null;
        $matchedString = null;
        $lazy = false;
        if (strpos($val, '->') !== false) {
            if (preg_match("~{$serviceRegex}->{$methodRegex}{$argumentsRegex}~six", $val, $matches)) {
                // Match @service->method("argument")
                $matchedString = $matches[0];
                $lazy = (bool) $matches['lazy'];
                $service = $matches['service'];
                $method = $matches['method'];
                $arguments = $this->getArguments($matches['arguments']);
            } elseif (preg_match("~{$serviceRegex}->{$methodRegex}~six", $val, $matches)) {
                // Match @service->method
                $matchedString = $matches[0];
                $lazy = (bool) $matches['lazy'];
                $service = $matches['service'];
                $method = $matches['method'];
                $arguments = [
                    $gridName,
                    $key,
                    $this->parentNode
                ];
            }
        } else {
            if (preg_match("~{$serviceRegex}~six", $val, $matches)) {
                // Match @service
                $service = $matches['service'];
            }
        }

        if ($service) {
            // Resolve service
            $service = $this->container->get($service);

            // Perform method call
            if ($method) {
                if ($lazy) {
                    return function () use ($val, $matchedString, $service, $method, $arguments) {
                        return $this->replaceValueInString(
                            $val,
                            $matchedString,
                            call_user_func_array([$service, $method], $arguments)
                        );
                    };
                }

                return $this->replaceValueInString(
                    $val,
                    $matchedString,
                    call_user_func_array([$service, $method], $arguments)
                );
            }

            return $service;
        }

        return $val;
    }

    /**
     * Replace matched string with resolved value in original string.
     *
     * Example:
     *    Input: Hello, @user_provider->getCurrentUserName
     *    Output: Hello, Some User
     *
     * @param string $originalString
     * @param string $matchedString
     * @param mixed $resolved
     * @return mixed
     */
    protected function replaceValueInString($originalString, $matchedString, $resolved)
    {
        if (is_scalar($resolved) && $originalString !== $matchedString) {
            return str_replace($matchedString, (string)$resolved, $originalString);
        }

        return $resolved;
    }

    /**
     * Get arguments as array from parsed arguments string.
     *
     * Example:
     *      Input: ("The", 'answer', 42)
     *      Output: ['The', 'answer', 42]
     *
     * @param string $argumentsString
     * @return array
     */
    protected function getArguments($argumentsString)
    {
        $argumentsString = trim($argumentsString);
        $argumentsString = trim($argumentsString, '()');
        $arguments = explode(',', $argumentsString);

        return array_map(
            function ($val) {
                $val = trim($val);
                return trim($val, '\'"');
            },
            $arguments
        );
    }
}
