<?php
/**
 * Search menus
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

use XIVDB\Apps\Site\FunctionsTrait;

class SearchMenus
{
    use FunctionsTrait;
    use SearchDatabase;
    use SearchPatches;

    protected $builder;
    protected $filters;
    protected $lang;
    protected $url;

    function __construct()
    {
        // set language
        $language = new \XIVDB\Apps\Site\Language();
        $this->lang = $language->getAll('custom');

        // start builder
        $this->builder = new SearchMenuBuilder();
    }

    //
    // Set filters
    //
    public function setFilters($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    //
    // Get menu
    //
    public function get()
    {
        return [
            'database' => $this->getDatabaseMenu(),
            'patches' => $this->getPatchesMenu()
        ];
    }

    //
    // Format for class job entry
    //
    protected function getClassJob($cj, $link = null)
    {
        return [
            'icon' => sprintf('/img/classes/set1/%s.png', $cj['icon']),
            'title' => $cj['name'],
        ];
    }
}
