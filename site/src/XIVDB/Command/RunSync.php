<?php

namespace XIVDB\Command;

use Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

use Knp\Command\Command;


class RunSync extends Command
{
    private $output;
    private $AppHandler;

    // databases
    private $db;
    private $sync;

    // vars
    private $lastSyncTime;
    private $time;
    private $microtime;
    private $totalRows = 0;

    protected function configure()
    {
        // Set this to a chunk value as some data max's out PDO bind limit
        define('MAX_CHUNKS', 100);

        // Josh, set this to a number if sync needs to be done manually
	// 60,000 chars = 1hr
        define('MAX_CHARACTERS', false);

        // Sync avgs 1000 characters a minute, so if you process a max of 1000 characters
        // then max time forward should be +1 minute.
        define('MAX_TIME_FORWARD', '+1 hour');

        $this
            ->setName("RunSync")
            ->setDescription("Sync data from XIVSync to XIVDB.");
    }

    protected function writeln($text)
    {
        $this->output->writeln(date('Y-m-d H:i:s') .' '. $text);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        date_default_timezone_set('UTC');

        $this->output = $output;
        $this->microtime = microtime(true);
        $this->time = (new \DateTime())->format('Y-m-d H:i:s');
        $this->AppHandler = new \XIVDB\Apps\AppHandler();

        $this->writeln('<info>XIVSync > XIVDB</info>');
        $this->writeln(sprintf('Sync starting: <comment>%s</comment>', $this->time));

        // xivdb database
        $this->writeln('Connecting to XIVDBs database ...');
        $this->db = $this->AppHandler->getModule('database');
        $this->db->realError();

        // xivsync database
        $this->writeln('Connecting to XIVSyncs database ...');
        $this->sync = $this->AppHandler->getModule('database', 'xivsync');
        $this->sync->realError();

        // all done
        $this->writeln('Connected!');

        // check columns match
        //$this->checkColumns('characters');
        //$this->checkColumns('characters_achievements_list');
        //$this->checkColumns('characters_achievements_possible');
        //$this->checkColumns('characters_events_exp_new');
        //$this->checkColumns('characters_events_lvs_new');
        //$this->checkColumns('characters_events_tracking');
        //$this->checkColumns('characters_gearsets');
        //$this->checkColumns('characters_data');
        //$this->writeln('All tables passed, starting sync');

        // get the last sync time
        $this->setLastSyncTime();

        // begin sync
        $this->sync();
    }

    //
    // Run character sync:
    //
    // Characters are selected that have been
    // updated since the last sync time.
    //      last_updated > lastSyncTime
    //
    private function sync()
    {
        // sync characters and get updated id list
        $updatedIds = $this->syncCharacters();

        if ($updatedIds)
        {
            // sync data
            $this->syncData($updatedIds);

            // sync gearsets
            $this->syncGearsets($updatedIds);

            // sync events
            $this->syncEvents($updatedIds);
        }

        // get achievements updated ids
        $updatedIds = $this->syncAchievements();

        if ($updatedIds)
        {
            // sync achievements
            $this->syncAchievementsList($updatedIds);

            // sync achievement possible
            $this->syncAchievementsPossible($updatedIds);
        }

        // complete
        $this->finish();
    }

    //
    // Finish Sync
    // - Sets last sync time
    //
    private function finish()
    {
        // insert new entry into sync
        $duration = round(microtime(true) - $this->microtime, 3);

        if (!MAX_CHARACTERS) {
            $this->writeln('Saving last update time as: '. $this->time);
            $this->db->QueryBuilder
                ->insert('sync_times')
                ->schema(['time', 'rows_processed', 'duration'])
                ->values([
                    $this->time,
                    $this->totalRows,
                    $duration,
                ]);

            $this->db->execute();
        } else {
            // push time forward by 5 minutes.
            $date = new \DateTime($this->lastSyncTime);
            $date->modify(MAX_TIME_FORWARD);
            $date = $date->format('Y-m-d H:i:s');

            $this->writeln('Saving last update time as: '. $date);

            $this->db->QueryBuilder
                ->insert('sync_times')
                ->schema(['time', 'rows_processed', 'duration'])
                ->values([
                    $date,
                    $this->totalRows,
                    $duration,
                ]);

            $this->db->execute();
        }

        // fin output
        $this->writeln('Finished!');
        $this->writeln(sprintf('Sync time: <info>%s</info>', $this->time));
        $this->writeln(sprintf('Duration: <info>%s</info>', $duration));
        $this->writeln(sprintf('Total Rows: <info>%s</info>', $this->totalRows));
        $this->writeln('');
    }

    private function syncCharacters()
    {
        $this->writeln('');
        $this->writeln('Getting characters ...');

        // get characters from xivsync
        $this->sync->QueryBuilder
            ->select('*')
            ->from('characters')
            ->where('last_updated >= :time')
            ->bind('time', $this->lastSyncTime);

        if (MAX_CHARACTERS) {
            $this->sync->QueryBuilder->limit(0,MAX_CHARACTERS);
        }

        $characters = $this->sync->get()->all();
        if (!$characters) {
            return $this->writeln('Odly... no characters updated...');
        }

        $this->writeln(sprintf('<comment>%s</comment> characters updated', count($characters)));

        $ids = [];
        $toUpdate = [];
        foreach(array_chunk($characters, MAX_CHUNKS) as $insert)
        {
            // update XIVDBs character table
            $this->writeln('Updating XIVDB character table: '. count($insert));
            $this->db->QueryBuilder
                ->insert('characters')
                ->schema(array_keys(reset($insert)))
                ->duplicate(array_keys(reset($insert)), true);

            foreach($insert as $char) {
                $ids[] = $char['lodestone_id'];
                $char['last_synced'] = $this->time;
                $toUpdate[] = $char;

                $this->db->QueryBuilder->values($char, true);
            }

            $this->db->execute();
        }

        $this->writeln('Complete');
        $this->writeln('');

        // update member info
        $totalChars = count($toUpdate);
        $this->writeln('Updating member characters: '. $totalChars);
        foreach($toUpdate as $i => $char) {
            $this->writeln('Complete: '. $i .'/'. $totalChars);

            // update members characters
            $this->db
                ->QueryBuilder
                ->update('members_characters')
                ->set('character_name', ':name')->bind('name', $char['name'])
                ->set('character_server', ':server')->bind('server', $char['server'])
                ->set('character_avatar', ':avatar')->bind('avatar', $char['avatar'])
                ->where('lodestone_id = '. $char['lodestone_id']);

            $this->db->execute();

            // update members characters
            $this->db
                ->QueryBuilder
                ->update('members')
                ->set('character_name', ':name')->bind('name', $char['name'])
                ->set('character_server', ':server')->bind('server', $char['server'])
                ->set('character_avatar', ':avatar')->bind('avatar', $char['avatar'])
                ->where('lodestone_id = '. $char['lodestone_id']);

            $this->db->execute();
        }
        $this->writeln('Complete');
        $this->writeln('');


        return $ids;
    }

    private function syncGearsets($ids)
    {
        $this->writeln('Getting gearsets ...');
        $data = $this->getSyncData('characters_gearsets', $ids);
        $this->insertDbData('characters_gearsets', $data);
        $this->deleteSyncData('characters_gearsets', $ids);
    }

    private function syncData($ids)
    {
        $this->writeln('Syncing Data ...');
        $data = $this->getSyncData('characters_data', $ids);
        $this->insertDbData('characters_data', $data);
    }

    private function syncEvents($ids)
    {
        //
        // EXP events
        //

        $this->writeln('EXP Events ...');
        $data = $this->getSyncData('characters_events_exp_new', $ids);
        $this->insertDbData('characters_events_exp_new', $data);
        $this->deleteSyncData('characters_events_exp_new', $ids);

        //
        // level events
        //

        $this->writeln('Level Events ...');
        $data = $this->getSyncData('characters_events_lvs_new', $ids);
        $this->insertDbData('characters_events_lvs_new', $data);
        $this->deleteSyncData('characters_events_lvs_new', $ids);

        //
        // tracking events
        //

        $this->writeln('Tracking Events ...');
        $data = $this->getSyncData('characters_events_tracking', $ids);
        $this->insertDbData('characters_events_tracking', $data);
        $this->deleteSyncData('characters_events_tracking', $ids);
    }

    private function syncAchievements()
    {
        $this->writeln('');
        $this->writeln('Getting characters ...');

        // get characters from xivsync
        $this->sync->QueryBuilder
            ->select('*')
            ->from('characters')
            ->where('achievements_last_updated >= :time')
            ->bind('time', $this->lastSyncTime);

        if (MAX_CHARACTERS) {
            $this->sync->QueryBuilder->limit(0,MAX_CHARACTERS);
        }

        $characters = $this->sync->get()->all();
        if (!$characters) {
            return $this->writeln('Odly... no achievements updated...');
        }

        $this->writeln(sprintf('<comment>%s</comment> achievements updated', count($characters)));

        $ids = $this->AppHandler->Functions()->getValueListFromArray($characters, 'lodestone_id');

        // sync characters
        $this->writeln('Syncing character data ...');
        $data = $this->getSyncData('characters', $ids);
        $this->insertDbData('characters', $data);

        return $ids;
    }

    private function syncAchievementsList($ids)
    {
        $this->writeln('Achievements List ...');
        $data = $this->getSyncData('characters_achievements_list', $ids);

        // temp fix
        foreach($data as $i => $d) {
            unset($data[$i]['points']);
        }

        $this->insertDbData('characters_achievements_list', $data);
        $this->deleteSyncData('characters_achievements_list', $ids);
    }

    private function syncAchievementsPossible($ids)
    {
        $this->writeln('Achievements Possible ...');
        $data = $this->getSyncData('characters_achievements_possible', $ids);
        $this->insertDbData('characters_achievements_possible', $data);
        $this->deleteSyncData('characters_achievements_possible', $ids);
    }

    // -------------------------------------------------------------------------

    //
    // Get data from XIVSync
    //
    private function getSyncData($table, $ids)
    {
        $where = sprintf('lodestone_id in (%s)', implode(',', $ids));

        // get all gearsets for the updated characters
        $this->sync->QueryBuilder
            ->select('*', false)
            ->from($table)
            ->where($where);

        $data = $this->sync->get()->all();
        $this->writeln(sprintf(
            '<comment>%s</comment> entries for <comment>%s</comment>',
            count($data),
            $table
        ));

        return $data;
    }

    //
    // Delete data from XIVSync
    //
    private function deleteSyncData($table, $ids)
    {
        if (!$ids || count($ids) < 1) {
            return;
        }

        $this->writeln(sprintf(
            'Deleting rows from: %s for %s characters', $table, count($ids)
        ));

        $where = sprintf('lodestone_id in (%s)', implode(',', $ids));

        $this->sync->QueryBuilder
            ->delete($table)
            ->where($where);

        $this->sync->execute();
        $this->writeln('Complete');
        $this->writeln('');
    }

    //
    // Insert data on XIVDB
    //
    private function insertDbData($table, $data)
    {
        if (!$data) {
            $this->writeln(sprintf(
                'No data to add to: %s', $table
            ));

            return;
        }

        $chunks = ($table == 'characters_achievements_list') ? 1000 : MAX_CHUNKS;

        foreach(array_chunk($data, $chunks) as $insert)
        {
            $this->writeln(sprintf('Updating %s with %s rows', $table, count($insert) ));

            // insert data into xivdb
            $this->db->QueryBuilder
                ->reset()
                ->insert($table)
                ->schema(array_keys(reset($insert)))
                ->duplicate(array_keys(reset($insert)), true);

            foreach($insert as $gs) {
                if ($table == 'characters_achievements_list') {
                    $this->db->QueryBuilder->values($gs);
                } else {
                    $this->db->QueryBuilder->values($gs, true);
                }
            }

            $this->db->execute();
        }



        // execute

        $this->writeln('Complete');
        $this->writeln('');

        // increment total
        $this->incrementTotal(count($data));
    }

    // -------------------------------------------------------------------------

    //
    // Increment the total row
    //
    private function incrementTotal($count)
    {
        $this->totalRows = $this->totalRows + $count;
    }

    //
    // Returns the time the last sync was processed
    //
    private function setLastSyncTime()
    {
        $this->writeln('Getting the last sync time ...');

        $this
            ->db
            ->QueryBuilder
            ->select('*', false)
            ->from('sync_times')
            ->order('time', 'desc')
            ->limit(0,1);

        $time = $this->db->get()->one();
        $time = $time['time'];

        $this->writeln(sprintf('Last sync: <comment>%s</comment>', $time));
        $this->lastSyncTime = $time;
    }

    //
    // Checks the columns of a table to ensure they're correct
    //
    private function checkColumns($table)
    {
        $this->writeln(sprintf('Checking table: <comment>%s</comment>', $table));

        $sql = sprintf('SHOW COLUMNS IN `%s`', $table);

        $res1 = $this->db->sql($sql);
        $res2 = $this->sync->sql($sql);

        if (count($res1) != count($res2)) {
            die(sprintf('Column count is different: %s', $table));
        }
    }
}
