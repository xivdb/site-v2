<?php

namespace XIVDB\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

use Knp\Command\Command;

class UpdateAchievementStats extends Command
{
    private $App;

    const LIMIT = 30;

    protected function configure()
    {
        $this
           ->setName("UpdateAchievementStats")
           ->setDescription("Update achievement stats");

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

        // stats_achievements

        $dbs = $this->App->getModule('database');

        // get achievement list
        $dbs->QueryBuilder->select(['id AS achievement_id'])->from('xiv_achievements');
        $list = $dbs->get()->all();

        $dbs->QueryBuilder
            ->insert('stats_achievements')
            ->schema(['achievement_id'])
            ->duplicate(['achievement_id'], true);

        foreach($list as $entry) {
            $dbs->QueryBuilder->values($entry);
        }

        $dbs->execute();
        $output->writeln('Updated stats achievement table');

        // get last updated achievement
        $dbs->QueryBuilder->select(['achievement_id'])->from('stats_achievements')->order('last_updated', 'ASC')->limit(0,self::LIMIT);
        $achievement = $dbs->get()->one();

        foreach($dbs->get()->all() as $achievement) {
            $this->process($output, $dbs, $achievement['achievement_id']);
        }
    }

    private function process($output, $dbs, $achievementId)
    {
        $output->writeln('Generating statistics for: '. $achievementId);
        $start = time();

        // achievement release date
        $dbs->QueryBuilder
            ->select([ 'db_patch' => ['patch', 'date']] )
            ->from('db_patch')
            ->join(['db_patch' => 'patch'], ['xiv_achievements' => 'patch'])
            ->where('xiv_achievements.id = :id')
            ->bind('id', $achievementId);

        $releaseDate = $dbs->get()->one();
        $output->writeln('Obtained release date');

        // get total who have their achievements public
        $dbs->QueryBuilder
            ->reset()
            ->count('lodestone_id')
            ->from('characters')
            ->where('achievements_score_reborn > 1');

        $lodestoneIds = [];
        foreach($dbs->get()->all() as $row) {
            $lodestoneIds[] = $row['lodestone_id'];
        }

        $eligable = count($lodestoneIds);
        $output->writeln('Complete: Elligable');

        // remove rogue ids
        // $sql = 'DELETE FROM characters_achievements_list WHERE lodestone_id NOT IN ('. implode(",", $lodestoneIds) .')';
        // $dbs->sql($sql);
        // die('done');

        // get total who have this achievement
        $dbs->QueryBuilder
            ->reset()
            ->count('COUNT(DISTINCT lodestone_id) as total')
            ->from('characters_achievements_list')
            ->where('achievement_id = :id')
            ->bind('id', $achievementId);

        $count = $dbs->get()->total();
        $percent = round($count / $eligable, 5) * 100;

        $output->writeln('Complete: Obtained');

        $dbs->QueryBuilder
            ->update('stats_achievements')
            ->set('total_possible', $eligable)
            ->set('total_obtained', $count)
            ->set('total_obtained_percent', $percent)
            ->set('release_patch', $releaseDate['patch'])
            ->set('release_date', ':rd')->bind('rd', $releaseDate['date'])
            ->set('last_updated', ':ld')->bind('ld', time())
            ->where('achievement_id = :id')->bind('id', $achievementId);

        $dbs->execute();

        $finish = time() - $start;

        $output->writeln('Percent rate: '. $percent);
        $output->writeln('Updated stats for achievement: '. $achievementId .' in '. $finish .' seconds');
    }
}