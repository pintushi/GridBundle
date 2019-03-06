<?php

namespace Pintushi\Bundle\GridBundle\Extension\Columns;

use Symfony\Component\Translation\TranslatorInterface;

class CompositeColumn extends AbstractColumn
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize()
    {
        $type = $this->getOr(self::TYPE_KEY);
        if ($type === self::TYPE_SELECT || $type === self::TYPE_MULTI_SELECT) {
            $translator = $this->translator;

            $choices = $this->getOr('choices', []);
            $translated = [];
            array_walk(
                $choices,
                function ($item, $key) use ($translator, &$translated) {
                    $translated[$translator->trans($key)] = $item;
                }
            );

            $this->params['choices'] = $translated;
        }
    }
}
