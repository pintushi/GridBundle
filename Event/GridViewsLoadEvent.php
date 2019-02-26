<?php

namespace Pintushi\Bundle\GridBundle\Event;

use Pintushi\Bundle\GridBundle\Entity\GridView;
use Symfony\Component\EventDispatcher\Event;

class GridViewsLoadEvent extends Event
{
    const EVENT_NAME = 'pintushi_grid.grid_views_load';

    /** @var string */
    protected $gridName;

    /** @var GridView[] */
    protected $gridViews = [];

    /**
     * @param string $gridName
     * @param GridView[] $gridViews
     */
    public function __construct($gridName, array $gridViews = [])
    {
        $this->gridName = $gridName;
        $this->gridViews = $gridViews;
    }

    /**
     * @return string
     */
    public function getGridName()
    {
        return $this->gridName;
    }

    /**
     * @return GridView[]
     */
    public function getGridViews()
    {
        return $this->gridViews;
    }

    /**
     * @param GridView[] $gridViews
     */
    public function setGridViews(array $gridViews = [])
    {
        $this->gridViews = $gridViews;
    }
}
