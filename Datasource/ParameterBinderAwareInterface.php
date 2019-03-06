<?php

namespace Pintushi\Bundle\GridBundle\Datasource;

/**
 * Data sources that supports parameter binding must implement this interface.
 */
interface ParameterBinderAwareInterface
{
    /**
     * Gets parameter binder.
     *
     * @deprecated since 2.0.
     *
     * @return ParameterBinderInterface
     */
    public function getParameterBinder();

    /**
     * Binds grid parameters to datasource query.
     *
     * @see ParameterBinderInterface::bindParameters
     * @param array $datasourceToGridParameters
     * @param bool $append
     */
    public function bindParameters(array $datasourceToGridParameters, $append = true);
}
