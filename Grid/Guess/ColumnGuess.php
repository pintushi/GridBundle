<?php

namespace Pintushi\Bundle\GridBundle\Grid\Guess;

use Symfony\Component\Form\Guess\Guess;

/**
 * Contains a guessed options for a grid column
 */
class ColumnGuess extends Guess
{
    /**
     * The guessed options
     *
     * @var array
     */
    private $options;

    /**
     * Constructor
     *
     * @param array $options    The options
     * @param int   $confidence The confidence that the guessed options is correct
     */
    public function __construct(array $options, $confidence)
    {
        parent::__construct($confidence);

        $this->options = $options;
    }

    /**
     * Returns the guessed options of the guessed grid column
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
