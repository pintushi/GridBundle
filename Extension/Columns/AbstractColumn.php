<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

use Pintushi\Bundle\GridBundle\Exception\LogicException;

abstract class AbstractColumn implements ColumnInterface
{
    /** @var array */
    protected $params;

    /** @var array */
    protected $excludeParamsDefault = [];


    /**
     * {@inheritdoc}
     */
    final public function init(ColumnConfiguration $params)
    {
        $this->params = $params;
        $this->initialize();

        return $this;
    }

    /**
     * Override this method instead "init" in case when we want to customize something
     */
    protected function initialize()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $defaultMetadata = [
            // use field name if label not set
            'label' => ucfirst($this->get('name')),
        ];

        $metadata = $this->get()->toArray([], $this->excludeParamsDefault);
        $metadata = array_merge($defaultMetadata, $this->guessAdditionalMetadata(), $metadata);

        return $metadata;
    }

    /**
     * Get param or throws exception
     *
     * @param string $paramName
     *
     * @throws LogicException
     * @return ColumnConfiguration|mixed
     */
    protected function get($paramName = null)
    {
        $value = $this->params;

        if ($paramName !== null) {
            if (!isset($this->params[$paramName])) {
                throw new LogicException(sprintf('Trying to access not existing parameter: "%s"', $paramName));
            }

            $value = $this->params[$paramName];
        }

        return $value;
    }

    /**
     * Get param if exists or default value
     *
     * @param string $paramName
     * @param null   $default
     *
     * @return mixed
     */
    protected function getOr($paramName = null, $default = null)
    {
        if ($paramName !== null) {
            return isset($this->params[$paramName]) ? $this->params[$paramName] : $default;
        }

        return $this->params;
    }

    /**
     * Guess additional metadata dependent on frontend type
     *
     * @return array
     */
    protected function guessAdditionalMetadata()
    {
        $metadata = [];

        switch ($this->getOr(self::TYPE_KEY)) {
            case self::TYPE_INTEGER:
                $metadata = ['style' => 'integer'];
                break;
            case self::TYPE_DECIMAL:
                $metadata = ['style' => 'decimal'];
                break;
            case self::TYPE_PERCENT:
                $metadata = ['style' => 'percent'];
                break;
            case self::TYPE_CURRENCY:
                $metadata = ['style' => 'currency'];
                break;
            case self::TYPE_BOOLEAN:
                $metadata = ['width' => 10];
                break;
        }

        return $metadata;
    }
}
