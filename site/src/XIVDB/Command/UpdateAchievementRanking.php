<?php

namespace XIVDB\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

use Knp\Command\Command;

define('LIMIT', 100);
define('DOCUMENT_URI', 'cli');

class UpdateAchievementRanking extends Command
{
    private $App;
    private $oldData;

    protected function configure()
    {
        $this
           ->setName("UpdateAchievementRanking")
           ->setDescription("Update achievement ranking");

        // set app handler
        $this->App = new \XIVDB\Apps\AppHandler();
    }

    /**
     * Write a line
     * @param $text
     */
    protected function writeln($text)
    {
        $this->output->writeln(date('Y-m-d H:i:s') .' '. $text);
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        date_default_timezone_set('UTC');

        $this->output = $output;
        $this->setOldData();
        $this->truncateOldData();
        $this->handleGlobalRanking();
        $this->handleServerRanking();
    }

    /**
     * Set the old data so we can get previous ranks
     */
    private function setOldData()
    {
        $this->writeln('Getting old ranking table ...');
        $dbs = $this->App->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from('characters_ranking');

        $temp = $dbs->get()->all();

        foreach($temp as $i => $character) {
            $this->oldData[$character['lodestone_id']] = $character;
        }
    }

    /**
     * Truncate the old data
     */
    private function truncateOldData()
    {
        $this->writeln('Truncating ranking data ...');
        $dbs = $this->App->getModule('database');
        $dbs->QueryBuilder->truncate('characters_ranking');
        $dbs->execute();
    }

    /**
     * Handle global ranking achievements
     */
    private function handleGlobalRanking()
    {
        $this->writeln('Generating global ranking ...');

        $dbs = $this->App->getModule('database');
        $dbs->QueryBuilder
            ->select('*', false)
            ->from('characters')
            ->where('achievements_public = 1')
            ->order('achievements_score_reborn', 'desc')
            ->limit(0, LIMIT);

        $new = [];
        foreach($dbs->get()->all() as $i => $char)
        {
            $lodestoneId = $char['lodestone_id'];
            $server = $char['server'];
            $points = $char['achievements_score_reborn'];
            $rank = ($i + 1);
            $previous = ($i + 1);

            // get old rank
            if ($this->oldData[$lodestoneId]) {
                $previous = $this->oldData[$lodestoneId]['rank_global'];
            }

            $new[] = [
                'lodestone_id' => $lodestoneId,
                'server' => $server,
                'rank_global' => $rank,
                'rank_global_previous' => $previous,
                'points' => $points,
            ];
        }

        file_put_contents(__DIR__.'/ranking/_global.json', json_encode($new));

        $dbs->QueryBuilder
            ->insert('characters_ranking')
            ->schema(array_keys($new[0]))
            ->duplicate(['lodestone_id']);

        // add values and auto bind them
        foreach($new as $value) {
            $dbs->QueryBuilder->values($value, true);
        }

        $dbs->execute();
        $this->writeln('-- Complete');
    }

    /**
     * Handle global ranking achievements
     */
    private function handleServerRanking()
    {
        $this->writeln('Generating server ranking ...');
        $dbs = $this->App->getModule('database');

        // Get servers
        $dbs->QueryBuilder
            ->select('*')
            ->from('xiv_worlds_servers');

        foreach($dbs->get()->all() as $server)
        {
            $this->writeln('Building ranking for: '. $server['name']);

            $dbs->QueryBuilder
                ->select('*', false)
                ->from('characters')
                ->where([
                    'achievements_public = 1',
                    'server = :server'
                ])
                ->bind('server', $server['name'])
                ->order('achievements_score_reborn', 'desc')
                ->limit(0, LIMIT);

            $characters = $dbs->get()->all();
            if (!$characters) {
                continue;
            }

            $new = [];
            foreach ($characters as $i => $char)
            {
                $lodestoneId = $char['lodestone_id'];
                $server = $char['server'];
                $points = $char['achievements_score_reborn'];
                $rank = ($i + 1);
                $previous = ($i + 1);

                // get old rank
                if (isset($this->oldData[$lodestoneId])) {
                    $previous = $this->oldData[$lodestoneId]['rank_server'];
                }

                $new[] = [
                    'lodestone_id' => $lodestoneId,
                    'server' => $server,
                    'rank_server' => $rank,
                    'rank_server_previous' => $previous,
                    'points' => $points,
                ];
            }

            file_put_contents(__DIR__.'/ranking/'. $server .'.json', json_encode($new));

            $dbs->QueryBuilder
                ->insert('characters_ranking')
                ->schema(array_keys($new[0]))
                ->duplicate(['lodestone_id']);

            // add values and auto bind them
            foreach ($new as $value) {
                $dbs->QueryBuilder->values($value, true);
            }

            $dbs->execute();
        }

        $this->writeln('-- Complete');
    }
}