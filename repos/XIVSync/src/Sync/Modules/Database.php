<?php

namespace Sync\Modules;

//
// Database class
//
class Database
{
    public $QueryBuilder;
    public $Connection;
    public $data;

    function __construct()
    {
        $config = require CONFIG;

        $this->Connection = new DatabaseConnection($config['database']);
        $this->QueryBuilder = new DatabaseQueryBuilder();
    }

    //
    // Run raw SQL
    //
    public function sql($sql)
    {
        return $this->Connection->query([
            'sql' => $sql,
        ]);
    }

    //
    // Reset
    //
    public function reset()
    {
        $this->QueryBuilder = new QueryBuilder();
        return $this;
    }

    //
    // Get a new query builder
    //
    public function getQueryBuilder()
    {
        return new QueryBuilder();
    }

    //
    // Set query builder
    //
    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->QueryBuilder = $qb;
        return $this;
    }

    //
    // Get data from database using SQL from QueryBuilder
    //
    public function get($isCount = false)
    {
        $sqlRequest = $this->QueryBuilder->get($isCount);
        $this->data = $this->Connection->query($sqlRequest);
        return $this;
    }

    //
    // Execute is just an alias, you don't "get" when updating so seperates logic.
    //
    public function execute()
    {
        return $this->get(false);
    }

    //
    // Return all data
    //
    public function all()
    {
        return $this->data;
    }

    //
    // Return just the first result
    //
    public function one()
    {
        return isset($this->data[0]) ? $this->data[0] : false;
    }

    //
    // Return last insert id
    //
    public function id()
    {
        return isset($this->data) ? $this->data : false;
    }

    //
    // Return the total (assumes count has been processed)
    //
    public function total()
    {
        return isset($this->data[0]['total']) ? $this->data[0]['total'] : 0;
    }
}
