<?php

namespace XIVDB\Apps\GameData;

//
// Access misc data
//
trait GameDataMisc
{
    public function xivGameDataBasic($table, $columns = '*', $where = false)
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->select($columns, false)->from($table);

        // if a where statement is passed
        if ($where) {
            $dbs->QueryBuilder->where($where);
        }

        // special case for db patch
        $key = $table == 'db_patch' ? 'patch' : 'id';

        return $this->restructure($dbs->get()->all(), $key);
    }

    //
    // Class Jobs Category
    //
    public function xivClassJobsToCategory()
    {
        $dbs = $this->getModule('database');

        // get categories
        $dbs->QueryBuilder
             ->select('id, classjob_list')
             ->from('xiv_classjobs_category');

        $categories = $dbs->get()->all();
        $list = [];

        // loop through categories
        foreach($categories as $category)
        {
            // get category id
            $id = $category['id'];

            // json decode classjob_list
            $json = json_decode($category['classjob_list'], true);

            if (!$json) {
                continue;
            }

            // map up restrictions
            $restrictions = [];
            foreach ($json as $cjid => $boolean)
            {
                // if bool is true, this is allowed
                if ($boolean == 1)
                {
                    $restrictions[] = $cjid;
                }
            }

            // append on list restrictions
            $list[$id] = $restrictions;
        }

        return $list;
    }

    //
    // Param Grow
    //
    public function xivParamGrow()
    {
        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
             ->select('*')
             ->from('xiv_param_grow')
             ->where('exp != 0');

        $data = $dbs->get()->all();
        $list = [];

        // loop through categories
        foreach($data as $d) {
            $list[$d['level']] = $d;
        }

        return $list;
    }

    //
    // Item names
    //
    public function xivItemNames()
    {
        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
             ->select('id, name_ja, name_en, name_fr, name_de, name_cns')
             ->from('xiv_items');

        $data = $dbs->get()->all();
        $list = [];

        // loop through categories
        foreach($data as $d) {
            $list[$d['id']] = $d;
        }

        return $list;
    }

    //
    // Item UI Categories
    //
    public function xivItemsUiCategory()
    {
        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
            ->select('id, item_ui_kind, name_en')
            ->from('xiv_items_ui_category');

        $data = $dbs->get()->all();
        $list = [];

        // loop through categories
        foreach($data as $d) {
            $list[$d['id']] = $d;
        }

        return $list;
    }

    //
    // This fixes recipes so that they have the item
    // name and not the name of all items involved
    // (only when fetched from libra)
    //
    public function fixRecipes()
    {
        $qb = $this->database->builder();
        $qb->select('*')->from('xiv_recipes');

        $recipes = $this->database->sql($qb)->all();

        foreach($recipes as $r)
        {
            // bit tacky ...
            $qb->select('*')->from('xiv_classjobs')->where('id = '. $cj['classjob']);
            $cj = $this->database->sql($qb)->one();

            // update item
            $qb->select('*')->from('xiv_items')->where('id = '. $r['item']);
            $item = $this->database->sql($qb)->one();

            $arr = [
                'name_ja' => addslashes($item['name_ja']),
                'name_en' => addslashes($item['name_en']),
                'name_fr' => addslashes($item['name_fr']),
                'name_de' => addslashes($item['name_de']),
                'name_cns' => addslashes($item['name_cns']),
                'classjob' => $cj['id'],
            ];

            $qb->update('xiv_recipes')->set($arr)->where('id = '. $r['id']);
            $this->database->sql($qb);
        }
    }
}
