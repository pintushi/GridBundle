<?php

namespace Pintushi\Bundle\GridBundle\Extension\Toolbar;

use Pintushi\Bundle\ConfigBundle\Config\ConfigManager;
use Pintushi\Bundle\GridBundle\Grid\Common\GridConfiguration;
use Pintushi\Bundle\GridBundle\Grid\Common\MetadataObject;
use Pintushi\Bundle\GridBundle\Grid\Common\ResultsObject;
use Pintushi\Bundle\GridBundle\Exception\LogicException;
use Pintushi\Bundle\GridBundle\Extension\AbstractExtension;
use Pintushi\Bundle\GridBundle\Provider\GridModeProvider;

class ToolbarExtension extends AbstractExtension
{
    /**
     * Configuration tree paths
     */
    const METADATA_KEY = 'options';

    const OPTIONS_PATH                         = '[options]';
    const TOOLBAR_OPTION_PATH                  = '[options][toolbarOptions]';
    const TOOLBAR_HIDE_OPTION_PATH             = '[options][toolbarOptions][hide]';
    const PAGER_ITEMS_OPTION_PATH              = '[options][toolbarOptions][pageSize][items]';
    const PAGER_DEFAULT_PER_PAGE_OPTION_PATH   = '[options][toolbarOptions][pageSize][default_per_page]';
    const PAGER_ONE_PAGE_OPTION_PATH           = '[options][toolbarOptions][pagination][onePage]';
    const TURN_OFF_TOOLBAR_RECORDS_NUMBER_PATH = '[options][toolbarOptions][turnOffToolbarRecordsNumber]';
    const TOOLBAR_PAGINATION_HIDE_OPTION_PATH  = '[options][toolbarOptions][pagination][hide]';

    /** @var ConfigManager */
    private $cm;

    /** {@inheritdoc} */
    protected $excludedModes = [
        GridModeProvider::DATAGRID_IMPORTEXPORT_MODE
    ];

    /**
     * @param ConfigManager $cm
     */
    public function __construct(ConfigManager $cm)
    {
        $this->cm = $cm;
    }

    /**
     * {@inheritDoc}
     */
    public function processConfigs(GridConfiguration $config)
    {
        $options = $config->offsetGetByPath(self::TOOLBAR_OPTION_PATH, []);
        // validate configuration and pass default values back to config
        $configuration = $this->validateConfiguration(new Configuration($this->cm), ['toolbarOptions' => $options]);
        $config->offsetSetByPath(sprintf('%s[%s]', self::OPTIONS_PATH, 'toolbarOptions'), $configuration);
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(GridConfiguration $config, MetadataObject $data)
    {
        /**
         * Default toolbar options
         *  [
         *      'hide'       => false,
         *      'pageSize'   => [
         *          'hide'  => false,
         *          'items' => [10, 25, 50, 100],
         *          'default_per_page' => 10
         *       ],
         *      'pagination' => [
         *          'hide' => false,
         *          'onePage' => false,
         *      ]
         *  ];
         */

        $perPageDefault = $config->offsetGetByPath(self::PAGER_DEFAULT_PER_PAGE_OPTION_PATH);
        $pageSizeItems  = $config->offsetGetByPath(self::PAGER_ITEMS_OPTION_PATH);

        $exist = array_filter(
            $pageSizeItems,
            function ($item) use ($perPageDefault) {
                if (is_array($item) && isset($item['size'])) {
                    return $perPageDefault == $item['size'];
                } elseif (is_numeric($item)) {
                    return $perPageDefault == $item;
                }

                return false;
            }
        );

        if (empty($exist)) {
            throw new LogicException(
                sprintf('Default page size "%d" must present in size items array', $perPageDefault)
            );
        }

        $options = $config->offsetGetByPath(ToolbarExtension::OPTIONS_PATH, []);


        // in case of one page pagination page selector should be hidden
        if ($config->offsetGetByPath(self::PAGER_ONE_PAGE_OPTION_PATH, false)) {
            $options['toolbarOptions']['pageSize']['hide'] = true;
        }

        // grid options passed under "options" node
        $data->offsetAddToArray(self::METADATA_KEY, $options);
    }
}
