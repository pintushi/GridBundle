<?php

namespace Pintushi\Bundle\GridBundle\Extension\Sorter;

use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Datasource\Orm\OrmQueryConfiguration as OrmQuery;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Oro\Component\DoctrineUtils\ORM\QueryHintResolver;

class HintExtension extends AbstractExtension
{
    /**
     * @var QueryHintResolver
     */
    protected $configHintResolver;

    /**
     * @var string
     */
    protected $hintName;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param QueryHintResolver $configHintResolver
     * @param string $hintName
     * @param int $priority
     */
    public function __construct(QueryHintResolver $configHintResolver, $hintName, $priority)
    {
        $this->queryHintResolver = $configHintResolver;
        $this->hintName = $hintName;
        $this->priority = $priority;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(GridConfiguration $config)
    {
        return parent::isApplicable($config) && $config->isOrmDatasource();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function processConfigs(GridConfiguration $config)
    {
        $addHint = true;
        $resolvedHintName = $this->queryHintResolver->resolveHintName($this->hintName);
        $hints = $config->getHints();
        foreach ($hints as $hintKey => $hint) {
            if (is_array($hint)) {
                $hintName = $this->getHintAttribute($hint, GridConfiguration::NAME_KEY);
                if ($this->hintName === $hintName || $resolvedHintName === $hintName) {
                    $addHint = false;
                    $hintValue = $this->getHintAttribute($hint, GridConfiguration::VALUE_KEY);
                    if (false === $hintValue) {
                        // remove the hint if it was disabled
                        unset($hints[$hintKey]);
                        $config->setHints($hints);
                    }
                    break;
                }
            } elseif ($this->hintName === $hint || $resolvedHintName === $hint) {
                $addHint = false;
                break;
            }
        }
        if ($addHint) {
            $config->addHint($this->hintName);
        }
    }

    /**
     * @param array  $hint
     * @param string $attributeName
     *
     * @return mixed
     */
    private function getHintAttribute(array $hint, $attributeName)
    {
        return array_key_exists($attributeName, $hint)
            ? $hint[$attributeName]
            : null;
    }
}
