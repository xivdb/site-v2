<?php

namespace XIVDB\Apps\LibraData;

trait LibraDatabase
{
    public $database;

    /**
     * connect to the lite database
     *
     * @param $sqlite
     */
    public function connect($sqlite)
    {
        if (!$sqlite) {
            die('Could not connect to: '. $sqlite);
        }

        $this->database = $this->getModule('sqlite')->connect($sqlite);
    }

    /**
     * Get the tables from the sqlite file
     *
     * @return mixed
     */
    public function tables()
    {
        $tables = $this->database->sql("SELECT name FROM sqlite_master WHERE type = 'table'");

        // organize
        foreach($tables as $i => $t) {
            $tables[$i] = $t['name'];
        }

        asort($tables);
        return $tables;
    }

    /**
     * Get data for a named entity
     *
     * @param $name
     * @return array
     */
    public function select($name)
    {
        $data = $this->database->sql('SELECT * FROM '. $name);

        // remove numeric indexes
        $arr = [];
        foreach($data as $i => $values)
        {
            $values = $this->removeNumericIndexes($values);

            // has data key?
            if (isset($values['data'])) {
                $values['data'] = json_decode($values['data']);
                $values['data'] = json_encode($values['data'], JSON_PRETTY_PRINT);
            }

            $arr[$values['Key']] = $values;
        }

        return $arr;
    }

    /**
     * Run an SQL statement on the lite database
     *
     * @param $sql
     * @return mixed
     */
    public function sql($sql)
    {
        return $this->database->sql($sql);
    }

    /**
     * Insert data
     *
     * @param $table
     * @param $data
     */
    public function insert($table, $data)
    {
        $this->log(sprintf('Inserting %s entries into %s', count($data), $table));

        $insert = [];
        foreach($data as $entry)
        {
            $insert[] = $entry;

            // if hit
            if (count($insert) == MANAGER_INSERT_LIMIT)
            {
                $this->processInsertableData($table, $insert);
                $insert = [];
            }
        }

        if (count($insert) > 0) {
            $this->processInsertableData($table, $insert);
        }
    }

    /**
     * Process some insertable data
     *
     * @param $table
     * @param $data
     */
    private function processInsertableData($table, $data)
    {
        $dbs = $this->getModule('database');
        $qb = $dbs->QueryBuilder;

        $columns = array_keys(reset($data));

        // build insert query
        $qb->reset()->insert($table)->schema($columns)->duplicate(['patch']);

        // pass in values
        foreach($data as $entry) {
            $qb->values($entry, true);
        }

        // execute
        $dbs->execute();
    }
}
