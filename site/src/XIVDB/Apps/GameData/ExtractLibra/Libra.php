<?php

/**
 * Libra
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\Site\FunctionsTrait;

class Libra
{
    use FunctionsTrait;

    public $LibraDB;

    function __construct()
    {
        $this->db();
        $this->connectToLibraSqlite();
    }

    /**
     * Request a connection to the libra database
     *
     * @return class(LibraDatabase) - the libra database class
     */
    public function connectToLibraSqlite()
    {

    }

    /**
     * Removes tables we don't need
     */
    public function cleanTables($libraTables)
    {
        // we dont need these tables
        unset($libraTables['app_data']);
        unset($libraTables['BNpcName_PlaceName']);
        unset($libraTables['ENpcResident_Quest']);
        unset($libraTables['Item_ClassJob']);
        unset($libraTables['ItemCategory']);

        return $libraTables;
    }
}
