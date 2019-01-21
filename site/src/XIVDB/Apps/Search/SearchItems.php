<?php
//
// Search for items
//

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchItems extends Search
{
    protected $Content = 'Item';
    protected $ContentTable = ContentDB::ITEMS;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{items}' => $this->Content::$search,
                '{to_category}' => [ 'name_{lang} as category_name' ],
                '{to_kind}' => [ 'name_{lang} as kind_name' ],
            ])
            ->from('{items}')
            ->join('{items}.item_ui_category', '{to_category}.id')
            ->join('{items}.item_ui_kind', '{to_kind}.id')
            ->where('{items}.`is_dated` = 0');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // levels
        //
        $this->handleFilters(['level_item|gt','level_item|lt'], 'and');
        $this->handleFilters(['level_equip|gt','level_equip|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'item_ui_category|et',
            'item_ui_kind|et',
            'item_series|et',
            'materia_slot_count|et',
            'rarity|et',
            'patch|et',
        ]);

        //
        // source
        //
        $this->filterSource();

        //
        // attributes
        //
        $this->filterAttributes();

        //
        // class jobs
        //
        $this->filterClassJobs();
    }

    //
    // Filter: Source
    //
    private function filterSource()
    {
        $flag = false;

        switch($this->request->get('source|et'))
        {
            case 'achievement': $flag = 'connect_achievement'; break;
            case 'craftable': $flag = 'connect_craftable'; break;
            case 'instance': $flag = 'connect_instance'; break;
            case 'instance_reward': $flag = 'connect_instance_reward'; break;
            case 'instance_chest': $flag = 'connect_instance_chest'; break;
            case 'quest': $flag = 'connect_quest_reward'; break;
            case 'enemy': $flag = 'connect_enemy_drop'; break;
            case 'shop': $flag = 'connect_shop'; break;
            case 'gathering': $flag = 'connect_gathering'; break;
        }

        if ($flag) {
            $this->qb()->where(sprintf('{items}.%s > 0', $flag));
        }
    }

    //
    // Filter: Attributes
    //
    private function filterAttributes()
    {
        $base =
        [
            12 => 'damage',
            13 => 'magic_damage',
            21 => 'defense',
            24 => 'magic_defense',
            18 => 'block_rate',
            17 => 'block_strength',
            16 => 'attack_speed',
            20 => 'auto_attack',
        ];

        /*

        Reference help, taken from XIVDB v1

        SELECT DISTINCT * FROM xiv_items_to_baseparam
        WHERE (baseparam = 1 AND value > 1)
        AND EXISTS ( SELECT 1 FROM xiv_items_to_baseparam WHERE baseparam = 2 AND value > 1)
        AND EXISTS ( SELECT 1 FROM xiv_items_to_baseparam WHERE baseparam = 5 AND value > 1)


        SELECT DISTINCT items".TBL_ITEM2ATTR_FK_ITEM." FROM ".TBL_ITEM2ATTR." AS A
        WHERE (FK_Attribute = :BP0 AND Mini > :BP1)
        AND EXISTS ( SELECT 1 FROM item2attribute AS B WHERE FK_Attribute = :BP2 AND Mini > :BP3 AND table.FK_Item = itemsFK_Item LIMIT 0,1)
        AND EXISTS ( SELECT 1 FROM item2attribute AS B WHERE FK_Attribute = :BP4 AND Mini > :BP5 AND table.FK_Item = itemsFK_Item LIMIT 0,1)
        */

        // get attributes
        $attributes = $this->request->get('attributes');
        $attributes = array_filter(explode(',', $attributes));

        // if empty
        if (!$attributes || empty($attributes)) {
            if ($this->request->get('order_field') == 'attributes') {
                $this->qb()
                    ->emptyStringIndex('ORDER')
                    ->order('{items}.id');
            }
            return;
        }

        // join required tables
        $this->qb()
            ->join('{items}.id', '{to_stats}.item')
            ->join('{items}.id', '{to_base}.id');

        // get attributes and loop through them
        $where = [];
        foreach($attributes as $i => $attr)
        {
            // get attribute data
            list($id, $symbol, $value, $isHq) = explode('|', $attr);
            $symbol = $this->qb()->getSymbol($symbol);

            // get data
            $column = null;

            // get field
            $field = $isHq ? 'value_hq' : 'value';

            // get bind param
            $bind1 = sprintf(':param%s',$i);
            $bind2 = sprintf(':value%s',$i);

            // if search is a base value
            if (isset($base[$id]))
            {
                $column = $base[$id];
                $string = '({to_base}.{column} {symbol} {value})';

                $this->qb()
                    ->bind($bind2, $value);
            }
            else
            {
                // if this is first or not
                $string = $i == 0 ?
                    '({to_stats}.baseparam = {param} AND {to_stats}.{field} {symbol} {value})' :
                    'EXISTS (SELECT 1 FROM {to_stats} WHERE {to_stats}.baseparam = {param} AND {to_stats}.{field} {symbol} {value} AND {to_stats}.item = {items}.id ORDER BY {to_stats}.{field} DESC LIMIT 0,1)';

                $this->qb()
                    ->bind($bind1, $id)
                    ->bind($bind2, $value);
            }

            // replace in sql string
            $where[] = str_ireplace(
                ['{param}', '{field}', '{symbol}', '{value}', '{column}'],
                [$bind1, $field, $symbol, $bind2, $column],
                $string
            );
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('attributes_andor'));

        // append where
        $this->qb()
            ->where($where, $andor);
    }

    //
    // Filter: Class/Jobs
    //
    private function filterClassJobs()
    {
        // get attributes
        $classjobs = $this->request->get('classjobs');
        $classjobs = array_filter(explode(',', $classjobs));
        if (!$classjobs || empty($classjobs)) {
            return;
        }

        // join required table
        $this->qb()->join('{items}.id', '{to_classjob}.item');

        // loop through class jobs
        $where = [];
        foreach($classjobs as $i => $id)
        {
            $bind = ':cj'. $i;

            // if this is first or not
            $string = $i == 0
                ? '({to_classjob}.classjob = {cj})'
                : 'EXISTS (SELECT 1 FROM {to_classjob} WHERE {to_classjob}.classjob = {cj} AND {to_classjob}.item = {items}.id)';

            // replace in sql string
            $where[] = str_ireplace('{cj}', $bind, $string);

            // append where
            $this->qb()->bind($bind, $id);
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('classjobs_andor'));

        // append where
        $this->qb()->where($where, $andor);

        // if exclusive
        if ($this->request->get('classjobs_exclusive'))
        {
            // id 1 = all classes, we don't want this for exclusivity
            $this->qb()->where('{items}.classjob_category != 1');
        }
    }
}
