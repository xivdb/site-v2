<?php

namespace XIVDB\Apps\Database;

//
// Query builder
//
class QueryBuilder extends QueryBuilderCommands
{
    protected $sql = [];
    protected $binds = [];
    protected $replace = [];
    protected $columns = [];

    //
    // Array string to build the SQL statement
    //
    protected $string =
    [
        'ACTION' => [],
        'ADDCOLUMNS' => [],
        'UPDATE' => [],
        'INSERT' => [],
        'DELETE' => [],
        'SCHEMA' => [],
        'COLUMNS' => [],
        'SET' => [],
        'VALUES' => [],
        'FROM' => [],
        'JOIN' => [],
        'FORCE' => [],
        'WHERE' => [],
        'DUPLICATE' => [],
        'GROUP' => [],
        'ORDER' => [],
        'LIMIT' => [],
    ];

    public function reset()
    {
        $this->sql = [];
        $this->binds = [];
        $this->replace = [];

        //
        // Array string to build the SQL statement
        //
        $this->string =
        [
            'ACTION' => [],
            'ADDCOLUMNS' => [],
            'UPDATE' => [],
            'INSERT' => [],
            'DELETE' => [],
            'SCHEMA' => [],
            'COLUMNS' => [],
            'SET' => [],
            'VALUES' => [],
            'FROM' => [],
            'JOIN' => [],
            'FORCE' => [],
            'WHERE' => [],
            'DUPLICATE' => [],
            'GROUP' => [],
            'ORDER' => [],
            'LIMIT' => [],
        ];

        return $this;
    }

    //
    // Get query built string
    //
    public function get($isCount = false)
    {
        // if count, reset limit
        if ($isCount) {
            $this->string['LIMIT'] = [];
            $this->string['GROUP'] = [];
            $this->string['ACTION'] = [ sprintf('SELECT count(DISTINCT {table}.id) as `total`', $isCount) ];
        }

        // build
        $this->build();

        // return
        return [
            'sql' => $this->sql,
            'binds' => $this->binds,
        ];
    }

    //
    // Get query built string
    //
    public function show($reduce = false, $raw = false, $pretty = false, $binds = false)
    {
        if ($binds) {
            show($this->binds);
            return;
        }

        // if pretty
        if ($pretty) {
            $this->build(true);
            echo sprintf(
                '<div style="padding:10px 20px;border:solid 1px #888;">%s</div>',
                MySQLFormatter::format($this->sql)
            );

            return;
        }

        // if raw
        if ($raw) {
            show($this->string);
            return;
        }

        $this->build($reduce);
        show($this->sql);
    }

    //
    // Get correct symbol from a passed value
    //
    public function getSymbol($symbol)
    {
        switch($symbol)
        {
            default: return '='; break;
            case 'gt': return '>='; break;
            case 'lt': return '<='; break;
            case 'et': return '='; break;
        }
    }

    //
    // Get correct direction
    //
    public function getDirection($direction)
    {
        return $direction ? trim($direction) : 'desc';
    }

    //
    // Get correct and/or
    //
    public function getAndOr($andor)
    {
        return $andor ? trim($andor) : 'and';
    }

    //
    // Add something to the string
    //
    protected function addToString($type, $sql)
    {
        $this->string[$type][] = $sql;
    }

    //
    // Add something to the replace
    //
    protected function addToReplace($find, $replace)
    {
        $this->replace[$find] = $replace;
    }

    //
    // Empty part of the string
    //
    public function emptyStringIndex($index)
    {
        $this->string[$index] = [];
        return $this;
    }

    //
    // Add something to the binds
    //
    protected function addToBinds($bind)
    {
        $this->binds[] = $bind;
    }

    //
    // Builds columns
    //
    protected function buildColumns($columns)
    {
        if ($columns == '*') return $columns;

        // if array
        if (is_array($columns))
        {
            foreach($columns as $table => $column)
            {
                // if table is numeric, don't append table prefix
                if (is_numeric($table))
                {
                    $columns[$table] = sprintf('`%s`', $column);
                }
                else
                {
                    // if columns are strings
                    if (is_string($column))
                    {
                        $column = str_ireplace(',', null, $column);
                        $column = explode(' ', $column);
                    }

                    foreach($column as $i => $col)
                    {
                        $column[$i] = sprintf('%s.`%s`', $table, $col);
                    }

                    $columns[$table] = implode(',', $column);
                }
            }

            $columns = implode(',', $columns);
        }
        else
        {
            $columns = str_ireplace(',', null, $columns);
            $columns = explode(' ', $columns);

            foreach($columns as $i => $column)
            {
                if (stripos($column, '.'))
                {
                    $column = explode('.', $column);
                    $column = sprintf('%s.`%s`', $column[0], $column[1]);
                    $columns[$i] = $column;
                    continue;
                }

                $columns[$i] = sprintf('`%s`', $column);
            }

            $columns = implode(',', $columns);
        }

        $columns = str_ireplace(' as ', '` as `', $columns);
        $columns = str_ireplace('`[count]`', 'count(*) as total', $columns);

        return $columns;
    }

    //
    // Builds where
    //
    protected function buildWheres($condition, $equal = 'AND')
    {
        if (is_string($condition)) {
            return $condition;
        }

        $equal = strtoupper(sprintf(' %s ', $equal));
        return sprintf('%s', implode($equal, $condition));
    }

    //
    // Builds join
    //
    protected function buildJoin($main, $join, $mainAs)
    {
        $table1 = explode('.', $join);
        $table2 = explode('.', $join);

        // normal left join
        $sql = vsprintf('LEFT JOIN %s ON %s = %s', [
            $table1[0], $join, $main
        ]);

        // if main
        if ($mainAs) {
            $sql = vsprintf('LEFT JOIN %s AS %s ON %s.%s = %s', [
                $table1[0], $mainAs, $mainAs, $table2[1], $main
            ]);
        }

        return $sql;
    }

    //
    // Build the query
    //
    protected function build($reduce = true)
    {
        $this->sql = [];

        // go through string and implode everything
        foreach($this->string as $type => $block)
        {
            if ($block)
            {
                $implode = ' ';
                $prefix = '';

                if ($type == 'ADDCOLUMNS') {
                    $implode = ',';
                }

                if ($type == 'WHERE') {
                    $implode = ' AND ';
                    $prefix = 'WHERE';
                }

                if ($type == 'ORDER') {
                    $implode = ',';
                    $prefix = 'ORDER BY';
                }

                if ($type == 'GROUP') {
                    $prefix = 'GROUP BY';
                }

                if ($type == 'SET') {
                    $implode = ',';
                    $prefix = 'SET';
                }

                if ($type == 'VALUES') {
                    $implode = ',';
                    $prefix = 'VALUES';
                }

                $stmt = implode($implode, $block);
                $stmt = sprintf('%s %s', $prefix, $stmt);
                $stmt = trim($stmt);

                $this->sql[] = $stmt;
            }
        }

        // if reducing to one line
        if ($reduce) {
            $this->sql = implode(' ', $this->sql);
            $this->sql = trim($this->sql);
        }

        // set language
        $this->sql = str_ireplace('{lang}', LANGUAGE, $this->sql);

        // find replace
        $this->sql = str_ireplace(array_keys($this->replace), $this->replace, $this->sql);
    }
}
