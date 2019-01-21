<?php

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

//
// Search Trait
//
Trait TraitSearch
{
    //
    // Finish up query
    //
    protected function finish()
    {
        // replace all constants
        $constants = new \ReflectionClass('XIVDB\Apps\Content\ContentDB');
        foreach ($constants->getConstants() as $const => $table)
        {
            $const = sprintf('{%s}', strtolower($const));
            $this->qb()->replace($const, $table);
        }

        // {table} = main content table
        $this->qb()->replace('{table}', $this->ContentTable);

        // replace any custom stuff
        if (method_exists($this, 'replacer')) {
            $this->replacer();
        }
    }

    //
    // Check for a search string
    //
    protected function string()
    {
        $strict = ($this->request->get('strict') == 'on') ? true : false;
        if ($string = $this->request->get('string'))
        {
            // if a command, return
            if ($this->command($string)) {
                return;
            }

            // reduce string length
            $string = substr($string, 0, SEARCH_STRING_LENGTH);

            // get chain
            $chain = (stripos($string, '/') !== false) ? '/' : false;
            $chain = (stripos($string, ',') !== false) ? ',' : $chain;

            $stmt = [];

            // if chain, split string
            if ($chain)
            {
                $words = array_values(array_filter(explode($chain, $string)));

                foreach($words as $i => $word)
                {
                    $word = trim($word);

                    // create bind
                    $bind = sprintf(':word%s',$i);

                    // slightly different statement depending on if
                    // number or not
                    if (is_numeric($word)) {
                        $stmt[] = "{table}.`id` = ". $bind;
                        $this->qb()->bind($bind, $word);
                    } else {
                        if (strlen($word) > 1) {
                            if ($this->ContentTable == 'characters') {
                                $stmt[] = "{table}.`name_{lang}` LIKE ". $bind;
                                $this->qb()->bind($bind, $word .'%');
                            } else if ($strict) {
                                $stmt[] = "MATCH({table}.`name_{lang}`) AGAINST(". $bind .")";
                                $this->qb()->bind($bind, $word);
                            } else {
                                $stmt[] = "{table}.`name_{lang}` LIKE ". $bind;
                                $this->qb()->bind($bind, '%'. $word .'%');
                            }
                        }
                    }
                }

                if ($stmt) {
                    $condition = $chain == '/' ? 'OR' : 'AND';
                    $this->qb()->where($stmt, $condition);
                }
            }
            else
            {
                // slightly different statement depending on if
                // number or not
                if (is_numeric($string)) {
                    $stmt = "{table}.`id` = :word";
                    $this->qb()->bind('word', $string);
                } else {
                    if (strlen($string) > 1) {
                        if ($this->ContentTable == 'characters') {
                            $stmt[] = "{table}.`name_{lang}` LIKE :word";
                            $this->qb()->bind('word', $string .'%');
                        } else if ($strict) {
                            $stmt[] = "MATCH({table}.`name_{lang}`) AGAINST(:word)";
                            $this->qb()->bind('word', $string);
                        } else {
                            $stmt = "{table}.`name_{lang}` LIKE :word";
                            $this->qb()->bind('word', '%'. $string .'%');
                        }
                    }
                }

                if ($stmt) {
                    $this->qb()->where($stmt, 'and');
                }
            }
        }
    }

    //
    // Order the search query
    //
    protected function order()
    {
        $order = strtolower($this->request->get('order_field'));
        $order = $order ? trim($order) : 'id';

        $custom = false;
        if ($order != 'id') {
            $custom = true;
        }

        // validate order
        $order = isset($this->Content::$order[$order]) ? $order : 'id';

        // set direction
        $direction = $this->qb()->getDirection($this->request->get('order_direction'));

        // order is attributes? (special case)
        if ($order == 'attributes') {
            $this->qb()->order('{to_stats}.value', $direction);
            return;
        }

        $order = sprintf('%s.`%s`', $this->ContentTable, $order);
        $this->qb()->order($order, $direction);

        if ($custom) {
            $this->qb()->order('{table}.id', 'asc');
        }

    }

    //
    // Set the limit
    //
    protected function limit()
    {
        // set limit
        $limit = $this->request->get('limit');
        $limit = $limit ? intval($limit) : SEARCH_LIMIT;
        $limit = ($limit > SEARCH_LIMIT_MAX || $limit < 1) ? SEARCH_LIMIT : $limit;

        // get page value
        // set page based on parameter
        // if page below 0 or not numeric, reset it
        $page = $this->request->get('page');
        $page = $page ? (intval($page) - 1) * $limit : 0;
        $page = ($page < 0 || !is_numeric($page)) ? 0 : $page;

        $this->limit = $limit;
        $this->qb()->limit($page, $limit);
    }

    //
    // Restrict search to a specific patch
    //
    protected function restrict()
    {
        // if patch is locked
        if (HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
            $this->qb()->where('{table}.patch != :patchRestricted')->bind('patchRestricted', HIDDEN_PATCH);
        }
    }
}
