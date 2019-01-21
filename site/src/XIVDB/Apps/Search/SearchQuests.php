<?php

/**
 * SearchQuests
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchQuests extends Search
{
    protected $Content = 'Quest';
    protected $ContentTable = ContentDB::QUEST;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{quest}' => $this->Content::$search,
                'class_1' => [ 'name_{lang} as class_1' ],
                '{journal_genre}' => [ 'name_{lang} as genre_name', 'icon as genre_icon' ],
                '{journal_category}' => [ 'name_{lang} as category_name' ],
            ])
            ->from('{quest}')
            ->join('{quest}.classjob_category_1', '{to_category}.id', 'class_1')
            ->join('{quest}.journal_genre', '{journal_genre}.id')
            ->join('{journal_genre}.journal_category', '{journal_category}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // levels
        //
        $this->handleFilters([
            'class_level_1|gt',
            'class_level_1|lt'
        ], 'and');

        //
        // item_ui_category
        // item_ui_kind
        // item_series
        // source
        // materia_slot_count
        // rarity
        // patch
        //
        $this->handleFilters([
            'gil_reward|gt',
            'exp_reward|gt',
            'grand_company|et',
            'journal_genre|et',
            'journal_category|et',
            'placename|et',
            'is_repeatable',
            'is_house_required',
            'item_reward_type',
            'tomestone_reward',
            'emote_reward',
            'patch|et',
        ]);

        //
        // class jobs
        //
        $this->filterClassJobs();
    }

    //
    // Handle classjobs
    //
    protected function filterClassJobs()
    {
        // get attributes
        $classjobs = $this->request->get('classjobs');
        $classjobs = array_filter(explode(',', $classjobs));
        if (!$classjobs || empty($classjobs)) {
            return;
        }

        // join required table
        $this->qb()->join('{quest}.id', '{to_classjob_quests}.quest');

        // loop through class jobs
        $where = [];
        foreach($classjobs as $i => $id)
        {
            $bind = ':cj'. $i;

            // if this is first or not
            $string = $i == 0
                ? '({to_classjob_quests}.classjob = %s)'
                : 'EXISTS (SELECT 1 FROM {to_classjob_quests} WHERE {to_classjob_quests}.classjob = %s AND {to_classjob_quests}.quest = {quest}.id)';

             // replace in sql string
            $where[] = sprintf($string, $bind);

            // append where
            $this->qb()->bind($bind, $id);
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('classjobs_andor'));

        // append where
        $this->qb()->where($where, $andor);
    }
}
