<?php

/**
 * Default processing class, handles some of the basics such
 * as getting the data, including the use of functions trait
 * and doing the initial process. This is so that each individual
 * processing class can be specifically create and used to handle
 * their table.
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

class CoreLibra extends \XIVDB\Apps\AppHandler
{
    protected $data;
    protected $patch;
    protected $timestamp;
    protected $database;

    public function process($data)
    {
        // set data
        $this->data = $data;

        // New Database connection
        $this->database = new SQLDatabase();

        // Get patch
        $this->patch = (new GamePatches())->current();

        // set timestamp
        $this->timestamp = date("Y-m-d H:i:s");

        // clean
        foreach($this->data as $i => $d) {
            $this->data[$i] = $this->removeNumericIndexes($d);
        }

        // handle
        return $this->handle();
    }

    /**
     * Insert new data to database, ignores 'patch' on duplicates
     *
     * @param $table - the table to insert into
     * @param $data - the data to insert
     * @return true - always true
     */
    protected function insert($table, $data)
    {
        if (empty($data)) {
            return true;
        }

        $qb = $this->database->builderBasic();

        $qb->insert($table)
           ->columns(array_keys(reset($data)))
           ->bind($data)
           ->duplicate(['patch']);

        $this->database->sql($qb->get(), $qb->getBinds())->id();

        return true;
    }

    /**
     * Provides the 5 name entries
     */
    protected function names($d, $custom = false)
    {
        if ($custom)
        {
            return [
                'name_ja' => $d[$custom .'_ja'],
                'name_en' => $d[$custom .'_en'],
                'name_fr' => $d[$custom .'_fr'],
                'name_de' => $d[$custom .'_de'],

            ];
        }

        return [
            'name_ja' => $d['Name_ja'],
            'name_en' => $d['Name_en'],
            'name_fr' => $d['Name_fr'],
            'name_de' => $d['Name_de'],

        ];
    }
}
