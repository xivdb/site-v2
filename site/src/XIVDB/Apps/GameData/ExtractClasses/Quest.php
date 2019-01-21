<?php

/**
 * Quest
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Quest extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_quests';

    const MAX_LEVEL = 100;

    protected $real =
    [
        2 => 'data_file',
        4 => 'classjob_category_1',
        5 => 'class_level_1',
        6 => 'quest_level_offset',

        8 => 'classjob_category_2',
        9 => 'class_level_2',

        21 => 'classjob_unlock',
        22 => 'grand_company',
        23 => 'grand_company_rank',
        24 => 'instance_content_join',

        31 => 'bell_start',
        32 => 'bell_end',
        33 => 'beast_tribe',
        34 => 'beast_reputation_rank',
        36 => 'mount_required',
        37 => 'is_house_required',

        41 => 'npc_start',
        43 => 'npc_end',

        44 => 'is_repeatable',
        45 => 'repeat_interval_type',

        1439 => 'classjob_required',
        1441 => 'exp_factor',
        1442 => 'gil_reward',
        1444 => 'gc_seals',
        1451 => 'item_reward_type',

        1493 => 'emote_reward',
        1494 => 'action_reward',


        1501 => 'instance_content_unlock',
        1503 => 'tomestone_reward',
        1504 => 'tomestone_count_reward',
        1505 => 'reputation_reward',
        1506 => 'placename',
        1507 => 'journal_genre',
        1509 => 'header',
        1510 => 'header_special',
        1515 => 'sort_key',
    ];

    protected $group =
    [
        1452 => 'item_reward_1_1',
        1453 => 'item_reward_1_2',
        1454 => 'item_reward_1_3',
        1455 => 'item_reward_1_4',
        1456 => 'item_reward_1_5',
        1457 => 'item_reward_1_6',

        1459 => 'item_quantity_1_1',
        1460 => 'item_quantity_1_2',
        1461 => 'item_quantity_1_3',
        1462 => 'item_quantity_1_4',
        1463 => 'item_quantity_1_5',
        1464 => 'item_quantity_1_6',

        1466 => 'item_stain_1_1',
        1467 => 'item_stain_1_2',
        1468 => 'item_stain_1_3',
        1469 => 'item_stain_1_4',
        1470 => 'item_stain_1_5',
        1471 => 'item_stain_1_6',

        1473 => 'item_reward_2_1',
        1474 => 'item_reward_2_2',
        1475 => 'item_reward_2_3',
        1476 => 'item_reward_2_4',
        1477 => 'item_reward_2_5',

        1478 => 'item_quantity_2_1',
        1479 => 'item_quantity_2_2',
        1480 => 'item_quantity_2_3',
        1481 => 'item_quantity_2_4',
        1482 => 'item_quantity_2_5',

        1483 => 'item_stain_2_1',
        1484 => 'item_stain_2_2',
        1485 => 'item_stain_2_3',
        1486 => 'item_stain_2_4',
        1487 => 'item_stain_2_5',
    ];

    protected $required = [ 13,14 ];

    protected function json($line)
    {
        // Remove some junk
        foreach(['ja', 'en', 'fr', 'de'] as $lang) {
            $line['name'][$lang] = trim(str_ireplace('', null, $line['name'][$lang]));
        }

        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }

    protected function manual()
    {
        $this->setRewardExp();
        $this->setRequiredQuests();
        $this->setClassJobs();
        $this->setRewards();
        $this->setNpcs();
        $this->setJournalCategory();
        $this->setTextData();
        $this->deleteTest();
    }

    private function setRewardExp()
    {
        $csv = $this->getCsvFileData();

        $expOffset = array_flip($this->real)['exp_factor'];
        $levelOffset = array_flip($this->real)['class_level_1'];
        $paramGrowList = $this->xivParamGrow();

        $insert = [];
        foreach($csv as $id => $line)
        {
            // get values
            $base = trim($line[$expOffset]);
            $level = trim($line[$levelOffset]);

            if ($level > self::MAX_LEVEL) {
                $level = 0;
            }

            $paramgrow = $paramGrowList[$level]['quest_exp_modifier'];

            // do math
            $rewardExp = ($base * $paramgrow * (45 + 5 * $level)) / 100;
            $additional = $level > 70 ? ($base / 100) * 10000 : 0;
            $rewardExp = $rewardExp + $additional;

            // make sure above 0
            if ($rewardExp > 0)
            {
                $insert[] =
                [
                    'id' => $id,
                    'exp_reward' => $rewardExp,
                ];
            }
        }

        // quests
        $this->insert('xiv_quests', $insert);
    }

    private function setRequiredQuests()
    {
        $csv = $this->getCsvFileData();

        $os = (Object)array_flip($this->real);

        $preQuests = [];
        $postQuests = [];
        foreach($csv as $id => $line)
        {
            foreach($this->required as $offset)
            {
                $required = ($line[$offset] != '0') ? trim($line[$offset]) : null;
                $genre = $line[$os->journal_genre];
                $sortKey = $line[$os->sort_key];

                if ($required && $required > 60000 && $id > 60000)
                {
                    $preQuests[] = [
                        'quest' => $id,
                        'quest_pre' => $required,
                        'genre' => $genre,
                        'sort_key' => $sortKey,
                    ];

                    $postQuests[] = [
                        'quest' => $required,
                        'quest_post' => $id,
                        'genre' => $genre,
                        'sort_key' => $sortKey,
                    ];
                }
            }
        }

        // quests
        $this->insert('xiv_quests_to_post_quest', $postQuests);
        $this->insert('xiv_quests_to_pre_quest', $preQuests);
    }

    private function setClassJobs()
    {
        $categories = $this->xivClassJobsToCategory();
        $csv = $this->getCsvFileData();

        $offset1 = array_flip($this->real)['classjob_category_1'];
        $offset2 = array_flip($this->real)['classjob_category_2'];

        $lvOffset1 = array_flip($this->real)['class_level_1'];
        $lvOffset2 = array_flip($this->real)['class_level_2'];

        $classjobs = [];
        $classjobsCategories = [];
        foreach($csv as $id => $line)
        {
            $cat1 = ($line[$offset1] != '0') ? trim($line[$offset1]) : null;
            $cat2 = ($line[$offset2] != '0') ? trim($line[$offset2]) : null;

            $lv1 = ($line[$lvOffset1] != '0') ? trim($line[$lvOffset1]) : null;
            $lv2 = ($line[$lvOffset2] != '0') ? trim($line[$lvOffset2]) : null;

            // category 1
            $cat1ClassJobList = (isset($categories[$cat1]) && $cat1) ? $categories[$cat1] : null;
            if ($cat1ClassJobList)
            {
                $classjobsCategories[] = [
                    'quest' => $id,
                    'classjob_category' => $cat1,
                    'level' => $lv1,
                ];

                foreach($cat1ClassJobList as $cjid)
                {
                    $classjobs[] = [
                        'quest' => $id,
                        'classjob' => $cjid,
                    ];
                }
            }

            // category 2
            $cat2ClassJobList = (isset($categories[$cat2]) && $cat2) ? $categories[$cat2] : null;
            if ($cat2ClassJobList)
            {
                $classjobsCategories[] = [
                    'quest' => $id,
                    'classjob_category' => $cat2,
                    'level' => $lv2,
                ];

                foreach($cat2ClassJobList as $cjid)
                {
                    $classjobs[] = [
                        'quest' => $id,
                        'classjob' => $cjid,
                    ];
                }
            }
        }

        $this->insert('xiv_quests_to_classjob_category', $classjobsCategories);
        $this->insert('xiv_quests_to_classjob', $classjobs);
    }

    private function setRewards()
    {
        $set = array_flip($this->group);
        $offsets1 = [
            [ $set['item_reward_1_1'], $set['item_quantity_1_1'], $set['item_stain_1_1'] ],
            [ $set['item_reward_1_2'], $set['item_quantity_1_2'], $set['item_stain_1_2'] ],
            [ $set['item_reward_1_3'], $set['item_quantity_1_3'], $set['item_stain_1_3'] ],
            [ $set['item_reward_1_4'], $set['item_quantity_1_4'], $set['item_stain_1_4'] ],
            [ $set['item_reward_1_5'], $set['item_quantity_1_5'], $set['item_stain_1_5'] ],
            [ $set['item_reward_1_6'], $set['item_quantity_1_6'], $set['item_stain_1_6'] ],
        ];

        $offsets2 = [
            [ $set['item_reward_2_1'], $set['item_quantity_2_1'], $set['item_stain_2_1'] ],
            [ $set['item_reward_2_2'], $set['item_quantity_2_2'], $set['item_stain_2_2'] ],
            [ $set['item_reward_2_3'], $set['item_quantity_2_3'], $set['item_stain_2_3'] ],
            [ $set['item_reward_2_4'], $set['item_quantity_2_4'], $set['item_stain_2_4'] ],
            [ $set['item_reward_2_5'], $set['item_quantity_2_5'], $set['item_stain_2_5'] ],
        ];

        $mainRewards = [];
        $optionalRewards = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            // main
            foreach($offsets1 as $os)
            {
                $item = isset($line[$os[0]]) ? $line[$os[0]] : null;
                $quantity = isset($line[$os[1]]) ? $line[$os[1]] : null;
                $stain = isset($line[$os[2]]) ? $line[$os[2]] : null;

                if ($item)
                {
                    $mainRewards[] = [
                        'quest' => $id,
                        'optional' => '0',

                        'item' => $item,
                        'stain' => $stain,
                        'quantity' => $quantity,
                    ];
                }
            }

            // optional
            foreach($offsets2 as $os)
            {
                $item = isset($line[$os[0]]) ? $line[$os[0]] : null;
                $quantity = isset($line[$os[1]]) ? $line[$os[1]] : null;
                $stain = isset($line[$os[2]]) ? $line[$os[2]] : null;

                if ($item)
                {
                    $optionalRewards[] = [
                        'quest' => $id,
                        'optional' => '1',

                        'item' => $item,
                        'stain' => $stain,
                        'quantity' => $quantity,
                    ];
                }
            }
        }

        $this->insert('xiv_quests_to_rewards', $mainRewards);
        $this->insert('xiv_quests_to_rewards', $optionalRewards);
    }

    private function setNpcs()
    {
        $startNpc = array_flip($this->real)['npc_start'];
        $endNpc = array_flip($this->real)['npc_end'];

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $startNpcId = (isset($line[$startNpc]) && $line[$startNpc] != '0') ? trim($line[$startNpc]) : null;
            $endNpcId = (isset($line[$endNpc]) && $line[$endNpc] != '0') ? trim($line[$endNpc]) : null;

            if ($startNpcId)
            {
                $insert[] = [
                    'npc' => $startNpcId,
                    'quest' => $id,
                    'is_client' => 1,
                ];
            }

            if ($endNpcId)
            {
                $insert[] = [
                    'npc' => $endNpcId,
                    'quest' => $id,
                    'is_client' => 0,
                ];
            }
        }

        $this->insert('xiv_npc_to_quest', $insert);
    }

    private function setTextData()
    {
        // quest data
        $insert = [];
        foreach($this->getJsonFileData() as $id => $data)
        {
            if (!isset($data['text_data'])) {
                continue;
            }

            $i = 0;
            foreach($data['text_data'] as $define => $text)
            {
                if (empty(trim($text['ja']))) {
                    continue;
                }

                $i++;

                $insert[] = [
                    'id' => $i,
                    'quest' => $id,
                    'define' => $define,

                    'text_ja' => $text['ja'],
                    'text_en' => $text['en'],
                    'text_fr' => $text['fr'],
                    'text_de' => $text['de'],
                ];
            }
        }

        $this->insert('xiv_quests_to_text', $insert);
    }

    private function deleteTest()
    {
        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
            ->delete('xiv_quests')
            ->where('id = 65536');

        $dbs->execute();
    }

    public function setJournalCategory()
    {
        $journalOffset = array_flip($this->real)['journal_genre'];
        $journalData = $this->xivGameDataBasic('xiv_journal_genre', '*', "name_en != ''");

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $journalId = isset($line[$journalOffset]) ? trim($line[$journalOffset]) : null;

            if ($journalId)
            {
                $categoryId = $journalData[$journalId]['journal_category'];

                if ($categoryId !== null)
                {
                    $insert[] = [
                        'id' => $id,
                        'journal_category' => $categoryId
                    ];
                }
            }
        }

        $this->insert('xiv_quests', $insert);
    }
}
