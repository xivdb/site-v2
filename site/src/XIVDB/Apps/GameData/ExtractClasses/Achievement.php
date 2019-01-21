<?php

/**
 * Achievement
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Achievement extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_achievements';

    protected $real =
    [
        1 => 'achievement_category',
        4 => 'points',
        5 => 'title',
        6 => 'item',
        7 => 'icon',
        9 => 'type',

        10 => 'requirement_1',
        11 => 'requirement_2',
        12 => 'requirement_3',
        13 => 'requirement_4',
        14 => 'requirement_5',
        15 => 'requirement_6',
        16 => 'requirement_7',
        17 => 'requirement_8',
        18 => 'requirement_9',
        //18 => 'requirement_10',

        19 => 'order',
    ];

    protected $requirements =
    [
        10 => 'requirement_1',
        11 => 'requirement_2',
        12 => 'requirement_3',
        13 => 'requirement_4',
        14 => 'requirement_5',
        15 => 'requirement_6',
        16 => 'requirement_7',
        17 => 'requirement_8',
        18 => 'requirement_9',
        //18 => 'requirement_10',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),
        ];
    }

    protected function manual()
    {
        $this->requirements();
    }

    /**
     * Base stats
     */
    private function requirements()
    {
        $csv = $this->getCsvFileData();
        $typeOffset = array_flip($this->real)['type'];

        // loop through csv
        foreach($csv as $id => $line)
        {
            $type = intval($line[$typeOffset]);

            // switch action based on type
            switch($type)
            {
                // Achievement
                case 2:
                    $this->insertPreAndPostAchievements($id, $line);
                    break;

                // Class
                case 3:
                    $this->insertJobClassRequirements($id, $line);
                    break;

                // Quest
                case 6:
                case 9:
                    $this->insertQuestRequirements($id, $line, $type);
                    break;
            }
        }
    }

    //
    // Handle pre and post achievements
    //
    private function insertPreAndPostAchievements($id, $line)
    {
        $insert = [];
        $insert2 = [];

        // loop through mapped
        foreach(array_keys($this->requirements) as $offset)
        {
            $value = intval($line[$offset]);
            if ($value && $value > 0)
            {
                $insert[] =
                [
                    'achievement' => $id,
                    'pre_achievement' => $value,
                    'patch' => $this->patch,
                ];

                $insert2[] =
                [
                    'achievement' => $value,
                    'post_achievement' => $id,
                    'patch' => $this->patch,
                ];
            }
        }

        $this->insert('xiv_achievements_pre', $insert);
        $this->insert('xiv_achievements_post', $insert2);
    }

    //
    // Handle quest requirements
    //
    public function insertQuestRequirements($id, $line, $type)
    {
        // loop through mapped
        foreach(array_keys($this->requirements) as $offset)
        {
            $value = intval($line[$offset]);
            if ($value && $value > 0)
            {
                $insert[] =
                [
                    'achievement' => $id,
                    'quest' => $value,
                    'patch' => $this->patch,
                    'is_or' => ($type == 6) ? 0 : 1,
                ];
            }
        }

        $this->insert('xiv_achievements_pre_quests', $insert);
    }


    //
    // Handle job class requirements
    //
    public function insertJobClassRequirements($id, $line)
    {
        $classjob = $line[array_flip($this->requirements)['requirement_1']];
        $level = $line[array_flip($this->requirements)['requirement_2']];

        // basic validation
        if ($classjob < 1 || $level < 1) {
            return;
        }

        $insert[] =
        [
            'achievement' => $id,
            'jobclass' => $classjob,
            'level' => $level,
            'patch' => $this->patch,
        ];

        $this->insert('xiv_achievements_jobclass', $insert);
    }
}
