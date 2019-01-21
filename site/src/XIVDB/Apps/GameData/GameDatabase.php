<?php

namespace XIVDB\Apps\GameData;

//
// Handle database interaction
//
trait GameDatabase
{
    //
    // Insert data
    //
    public function insert($table, $data)
    {
        if (method_exists($this, 'log')) {
            $this->log(sprintf('Inserting %s entries into %s', count($data), $table));
        }

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

    //
    // Process some insertable data
    //
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

    //
    // Truncate some tables
    //
    private function processTruncate($tables)
    {
        $dbs = $this->getModule('database');
        foreach($tables as $table) {
            if (method_exists($this, 'log')) {
                $this->log(sprintf('Truncating table: %s', $table));
            }

            $dbs->QueryBuilder->truncate($table);
            $dbs->execute();
        }
    }
}
