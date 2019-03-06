<?php

namespace Pintushi\Bundle\GridBundle\Provider;

/**
 * Stores grid modes which can help to filter what extensions to load in different modes
 */
class GridModeProvider
{
    const DATAGRID_FRONTEND_MODE     = 'frontend';
    const DATAGRID_BACKEND_MODE      = 'backend';
    const DATAGRID_IMPORTEXPORT_MODE = 'importexport';
}
