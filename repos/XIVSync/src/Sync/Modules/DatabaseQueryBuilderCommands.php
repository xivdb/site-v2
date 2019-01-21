<?php

namespace Sync\Modules;

use \PDO;

//
// Query builder
//
class DatabaseQueryBuilderCommands
{
    //
    // TRUNCATE
    //
    public function truncate($table)
    {
        $this->reset();
        $this->addToString('ACTION', sprintf('TRUNCATE TABLE %s', $table));
        return $this;
    }

    //
    // SELECT
    //
    public function select($columns = false, $isDistinct = true)
    {
        $this->reset();

        if (!$columns) {
            $sql = 'SELECT';
        } else {
            $sql = $isDistinct
                    ? sprintf('SELECT DISTINCT %s', $this->buildColumns($columns))
                    : sprintf('SELECT %s', $this->buildColumns($columns));
        }

        $this->addToString('ACTION', $sql);
        return $this;
    }

    //
    // SELECT TOTAL
    //
    public function total()
    {
        $this->reset();

        $sql = 'SELECT count(*) as total';
        $this->addToString('ACTION', $sql);
        return $this;
    }

    //
    // FROM
    //
    public function from($table)
    {
        $sql = sprintf('FROM %s', $table);
        $this->addToString('FROM', $sql);
        return $this;
    }

    //
    // WHERE
    //
    public function where($condition, $equal = 'AND')
    {
        $equal = $equal ? $equal : 'and';

        $sql = $this->buildWheres($condition, $equal);
        $sql = sprintf('(%s)', $sql);

        $this->addToString('WHERE', $sql);
        return $this;
    }

    //
    // JOIN
    //
    public function join($main, $table, $mainAs = null)
    {
        // if main is an array
        if (is_array($main)) {
            $main = sprintf('%s.%s', key($main), reset($main));
            $table = sprintf('%s.%s', key($table), reset($table));
        }

        $sql = $this->buildJoin($main, $table, $mainAs);
        $this->addToString('JOIN', $sql);
        return $this;
    }

    //
    // GROUP
    //
    public function group($main)
    {
        $sql = sprintf('%s', $main);
        $this->addToString('GROUP', $sql);
        return $this;
    }

    //
    // ORDER
    //
    public function order($order, $direction = 'desc')
    {
        $direction = $direction ? $direction : 'desc';

        $sql = sprintf('%s %s', $order, strtoupper($direction));
        $this->addToString('ORDER', $sql);
        return $this;
    }

    //
    // LIMIT
    //
    public function limit($start, $amount = null)
    {
        if ($amount) {
            $sql = sprintf('LIMIT %s,%s', $start, $amount);
        } else {
            $sql = sprintf('LIMIT %s', $start);
        }

        $this->addToString('LIMIT', $sql);
        return $this;
    }

    //
    // UPDATE
    //
    public function update($table)
    {
        $this->reset();

        $sql = sprintf('UPDATE %s', $table);
        $this->addToString('UPDATE', $sql);
        return $this;
    }

    //
    // INSERT
    //
    public function insert($table)
    {
        $this->reset();

        $sql = sprintf('INSERT INTO %s', $table);
        $this->addToString('INSERT', $sql);
        return $this;
    }

    //
    // DELETE
    //
    public function delete($table)
    {
        $this->reset();

        $sql = sprintf('DELETE FROM %s', $table);
        $this->addToString('DELETE', $sql);
        return $this;
    }

    //
    // SET
    //
    public function set($column, $value = null)
    {
        if (is_array($column)) {
            foreach($column as $col => $value) {
                $sql = sprintf('%s = %s', $col, $value);
                $this->addToString('SET', $sql);
            }
        } else {
            $sql = sprintf('%s = %s', $column, $value);
            $this->addToString('SET', $sql);
        }

        return $this;
    }

    //
    // VALUES
    //
    public function values($values, $autoBind = false)
    {
        $arr = [];

        foreach($values as $i => $value) {
            // if auto binding
            if ($autoBind) {
                $bind = ':a'.mt_rand(0,9999999999999999);
                $this->bind($bind, $value);
                $value = $bind;
            }

            if (isset($value[0]) && $value[0] !== ':' && $value) {
                $value = sprintf("'%s'", $value);
            }

            if (!$value) {
                $value = 'NULL';
            }

            $arr[] = $value;
        }

        $sql = sprintf("(%s)", implode(",", $arr));
        $this->addToString('VALUES', $sql);
        return $this;
    }

    //
    // SCHEMA
    //
    public function schema($values)
    {
        $this->columns = $values;

        $sql = sprintf('(`%s`)', implode('`,`', $values));
        $this->addToString('SCHEMA', $sql);
        return $this;
    }

    //
    // Bind a Parameter
    //
    public function bind($param, $variable, $isWild = false)
    {
        $param = str_ireplace(':', null, $param);

        // if wild, put wild card around bind
        if ($isWild) {
            $variable = '%'. $variable .'%';
        }

        $this->addToBinds([
            trim($param),
            $variable,
            is_numeric($variable) ? PDO::PARAM_INT : PDO::PARAM_STR,
        ]);

        return $this;
    }

    //
    // Replace a string
    //
    public function replace($find, $replace)
    {
        $this->addToReplace($find, $replace);
        return $this;
    }

    //
    // Apply duplicates (optional: pass in an array to ignore)
    //
    public function duplicate($columns = [], $include = false)
    {
        if (!$include) {
            $columns = array_diff($this->columns, $columns);
        } else if ($include && !$columns) {
            $columns = $this->columns;
        }

        $duplicate = [];
        foreach($columns as $c) {
            $duplicate[] = sprintf('`%s`=VALUES(`%s`)', $c, $c);
        }

        $sql = 'ON DUPLICATE KEY UPDATE '. implode(',', $duplicate);
        $this->addToString('DUPLICATE', $sql);
    }

    //
    // Some column is not null
    //
    public function notnull($column)
    {
        $this->where($column .' IS NOT NULL', 'AND');
        return $this;
    }

    //
    // Some column is not empty
    //
    public function notempty($column)
    {
        $this->where($column ." != ''", 'AND');
        return $this;
    }

    //
    // Some column is empty
    //
    public function isempty($column)
    {
        $this->where($column ." = ''", 'AND');
        return $this;
    }

    //
    // Some column is not value
    //
    public function not($column, $value)
    {
        $this->where($column ." != ". $value);
        return $this;
    }

    //
    // Add Columns
    //
    public function addColumns($columns = '*')
    {
        $sql = $this->buildColumns($columns);
        $this->addToString('ADDCOLUMNS', $sql);
        return $this;
    }

    //
    // Add Columns
    //
    public function forceIndex($keys)
    {
        $sql = sprintf('FORCE INDEX (%s)', $keys);
        $this->addToString('FORCE', $sql);
        return $this;
    }
}
