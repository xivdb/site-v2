<?php

namespace Sync\Tasks;

use Sync\Modules\Database;

/**
 * Class TaskBenchmarkCharacters
 * @package Sync\App
 */
class TaskBenchmarkCharacters
{
    private $maxCharacters = 100;
    private $Characters;

    function __construct()
    {
        $this->Characters = new \Sync\App\Characters();
    }

    public function init()
    {
        $start = microtime(true);
        $dbs = new Database();
        $dbs->QueryBuilder
            ->select('*', false)
            ->from('characters')
            ->where('deleted != 1')
            ->order('last_updated', 'asc')
            ->limit(0, $this->maxCharacters);

        $list = $dbs->get()->all();

        output('');
        $response = $this->runAction($list);
        $timePassed = time() - $start;
        output(sprintf('Completed %s parses in %s seconds', $response, $timePassed));
    }

    //
    // Loop through all characters
    //
    private function runAction($list)
    {
        $complete = 0;
        foreach($list as $character) {
            // get character data (also parses it)
            $url = sprintf(LODESTONE_CHARACTERS_URL, $character['lodestone_id']);
            $this->Characters->requestFromLodestone($url);
            $complete++;
        }

        return $complete;
    }
}
