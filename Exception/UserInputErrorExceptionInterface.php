<?php

namespace Pintushi\Bundle\GridBundle\Exception;

interface UserInputErrorExceptionInterface extends DatagridException
{
    const TYPE = 'user_input_error';

    /**
     * Get error message translation key
     *
     * @return string
     */
    public function getMessageTemplate();

    /**
     * Get error  message translation params
     *
     * @return mixed
     */
    public function getMessageParams();
}
