<?php

namespace XIVDB\Apps\Database;

use \PDO, \PDOException;

//
// Connection class
//
class Connection extends \XIVDB\Apps\AppHandler
{
    protected $instance;
    protected $count = 0;
    protected $data;
    protected $showerror = false;

    public function realError()
    {
        $this->showerror = true;
        return $this;
    }

    function __construct($profile = false)
    {
        if (!$config = require FILE_CONFIG) {
            // TODO : This needs handling much better, maybe a redirect
            die('Error reading site configuration');
        }

        // config
        $config = $profile ? $config[$profile] : $config['database'];

        // create connection string
        $conString = sprintf('mysql:%s', implode(';', [
            'host='. $config['host'],
            'port='. $config['port'],
            'dbname='. $config['name'],
            'charset='. $config['char'],
        ]));

        try
        {
            // setup connection
            $this->instance = new PDO($conString, $config['user'], $config['pass'], [
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES 'utf8'"
            ]);

            // if failed connection
            if (!$this->instance) {
                die('(non-exception) Could not connect to the database');
            }

            $this->instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->instance->setAttribute(PDO::ATTR_TIMEOUT, 15);
        }
        catch (PDOException $e) {
            $this->error($e);
        }
    }

    //
    // Null connection on destruct
    //
    function __destruct()
    {
        $this->instance = null;
    }

    function end()
    {
        $this->instance = null;
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
    public function query($sqlRequest, $isSingle = false)
    {
        $sql = $sqlRequest['sql'];
        $binds = isset($sqlRequest['binds']) ? $sqlRequest['binds'] : [];

        // replace language params
        $sql = str_ireplace('{lang}', LANGUAGE, $sql);

        try
        {
            // prepare and execute SQL statement
            $query = $this->instance->prepare($sql);
            $query = $this->handleBinds($query, $binds);
            $query->execute();

            // if any errors, if non, increment SQL count
            $this->handleErrors($query);
            $this->count++;

            // get action
            $action = $this->getSqlAction($sql);

            // perform action
            if (in_array($action, ['select', 'show', 'alter']))
            {
                return $isSingle ?
                    $query->fetch(PDO::FETCH_ASSOC) :
                    $query->fetchAll(PDO::FETCH_ASSOC);
            }
            else if (in_array($action, ['insert', 'update', 'delete']))
            {
                return $this->instance->lastInsertId();
            }
        }
        catch(PDOException $e)
        {
            $this->error($e, $sql);
        }
    }

    //
    // show an error
    //
    private function error($e, $sql = '')
    {
        if (defined('DOCUMENT_URI')) {
            print_r($e->getMessage());
            return;
        }

        if ($this->showerror) {
            show($e->getMessage());
            die;
        }

        if (!DEV) {
            $url = URL . isset($_SERVER['DOCUMENT_URI']) ? $_SERVER['DOCUMENT_URI'] : null;
            $this->getModule('mail')->send(MAIL_DEV, 'XIVDB Database Error', 'database error', 'Email/db_error.html.twig', [
                'sql' => $sql,
                'url' => $url,
                'message' => $e->getMessage(),
                'server_data' => json_encode($_SERVER, JSON_PRETTY_PRINT),
                'backtrace' => json_encode(debug_backtrace(), JSON_PRETTY_PRINT),
            ]);

            die('A database error happened, the site admin has been informed and will fix it ASAP!');
        }

        // TODO : This needs handling much better, maybe a redirect
        show($e->getMessage());
        show($sql);

        foreach(debug_backtrace(null, 7) as $event) {
            show(sprintf('[Line #%s] %s', isset($event['line']) ? $event['line'] : '??', isset($event['class']) ? $event['class'] : '??'));
        }

        die;
    }

    //
    // Handle passed binds parameters
    //
    private function handleBinds($query, $binds)
    {
        // check if there are bind params
        if ($binds)
        {
            // append bind params
            foreach($binds as $param)
            {
                $id     = ':'. $param[0];
                $value  = $param[1];
                $type   = isset($param[2]) ? $param[2] : null;

                $query->bindValue($id, $value, $type);
            }
        }

        return $query;
    }

    //
    // Handle any errors
    //
    private function handleErrors($query)
    {
        // get any errors
        $errors = $query->errorInfo();
        if (isset($errors[2]) && $errors[2]) {
            // TODO : This needs handling much better, maybe a redirect
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
