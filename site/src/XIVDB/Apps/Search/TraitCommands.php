<?php

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;
use XIVDB\Apps\Search\Filters;

//
// Command Trait
//
Trait TraitCommands
{
    //
    // Is a command?
    //
    protected function command($string)
    {
        // get command
        $command = strtolower(trim(explode(' ', $string)[0]));

        // if patch
        if ($command == 'patch')
        {
            $string = explode(' ', $string);
            if (!isset($string[1])) {
                return false;
            }

            // get patch number
            $patch = strtolower(trim($string[1]));
            $patchId = null;

            // find patch id
            foreach((new Filters())->get()['patches'] as $p) {
                if ($patch == $p['command']) {
                    $patchId = $p['number'];
                    break;
                }
            }

            // if not found, return false
            if (!$patchId) {
                return false;
            }

            // add condition
            $this
                ->qb()
                ->where('{table}.patch = :patch')
                ->bind('patch', $patchId);

            $this->isPatchSearch = true;
            return true;
        }

        return false;
    }

    protected function commandCheck($string)
    {
        // get command
        $command = strtolower(trim(explode(' ', $string)[0]));

        // if patch
        if ($command == 'patch') {
            $this->isPatchSearch = true;
        }
    }
}
