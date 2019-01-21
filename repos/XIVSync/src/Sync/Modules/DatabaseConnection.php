<?php

namespace Sync\Modules;

use \PDO, \PDOException;

//
// Connection class
//
class DatabaseConnection
{
    const ATTR_PERSISTENT = false;
    const ATTR_EMULATE_PREPARES = false;
    const ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION;
    const ATTR_TIMEOUT = 15;

    public $instance;
    public $data;
    public $executes = [];

    function __construct($config)
    {
        // create connection string
        $conString = sprintf('mysql:%s', implode(';', [
            'host='. $config['host'],
            'port='. $config['port'],
            'dbname='. $config['name'],
            'charset='. $config['char'],
        ]));

        try {
            // setup connection
            $this->instance = new PDO($conString, $config['user'], $config['pass'], [
                PDO::ATTR_PERSISTENT => self::ATTR_PERSISTENT,
            ]);

            // if failed connection
            if (!$this->instance) {
                die('(non-exception) Could not connect to the database');
            }

            $this->instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, self::ATTR_EMULATE_PREPARES);
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, self::ATTR_ERRMODE);
            $this->instance->setAttribute(PDO::ATTR_TIMEOUT, self::ATTR_TIMEOUT);
        } catch (PDOException $e) {
            output($e->getMessage());
        }
    }

    //
    // Run pure SQL
    //
    public function sql($sql, $binds = [])
    {
        return $this->query([
            'sql' => $sql,
            'binds' => $binds,
        ]);
    }

    //
    // Run a query
    //
    public function query($params, $isSingle = false)
    {
        $started = microtime(true);

        $sql = $params['sql'];
        $binds = isset($params['binds']) ? $params['binds'] : [];

        try {
            // prepare and execute SQL statement
            // prepare and execute SQL statement
            $query = $this->instance->prepare($sql);

            // handle any bound parameters
            if ($binds) {
                foreach($binds as $param) {
                    list($name, $value, $type) = $param;
                    $query->bindValue($name, $value, $type);
                }
            }

            output('SQL: %s', [
                substr($sql, 0, SQL_OUTPUT_TRUNCATE)
            ]);

            // execute query
            $query->execute();

            // if any errors
            $this->handleErrors($query);

            // get action
            $action = $this->getSqlAction($sql);

            // time
            $duration = microtime(true) - $started;
            output('--> SQL: [%s ms] %s', [
                round($duration, 3), substr($sql, 0, SQL_OUTPUT_TRUNCATE)
            ]);

            // perform action
            if (in_array($action, ['select', 'show', 'alter'])) {
                return $isSingle ?
                    $query->fetch(PDO::FETCH_ASSOC) :
                    $query->fetchAll(PDO::FETCH_ASSOC);
            }

            if (in_array($action, ['insert', 'update', 'delete'])) {
                return $this->instance->lastInsertId();
            }
        } catch(PDOException $e) {
            output($sql);
            output($e->getMessage());
        }
    }

    //
    // Handle any errors
    //
    private function handleErrors($query)
    {
        // get any errors
        $errors = $query->errorInfo();
        if (isset($errors[2]) && $errors[2]) {
            die(print_r($errors, true));
        }
    }

    //
    // Get SQL action
    //
    private function getSqlAction($sql)
    {
        return strtolower(explode(' ', $sql)[0]);
    }
}
