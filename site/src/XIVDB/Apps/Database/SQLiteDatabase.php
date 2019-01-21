<?php

namespace XIVDB\Apps\Database;

use \PDO;

/**
 * Lite Database
 *
 * @version 1.0
 * @author
 */
class SQLiteDatabase
{
    protected $connection;

    //
    // Connect to an sqlite file
    //
    public function connect($sqlite)
    {
        if (!file_exists($sqlite)) {
            die('Sqlite File does not exist: '. $sqlite);
        }

        // create connection
        $this->connection = new PDO('sqlite:'. $sqlite);
        if (!$this->connection) {
            die('Could not connect to SQLite');
        }

        return $this;
    }

    // run an sqlite query
    public function sql($sql)
    {
        if (!$this->connection) {
            die('No connection');
        }

        $query = $this->connection->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
}
