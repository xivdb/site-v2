<?php
/**
 * SearchRecipes
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchRecipes extends Search
{
    protected $Content = 'Recipe';
    protected $ContentTable = ContentDB::RECIPE;

    //
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{recipe}' => $this->Content::$search,
                '{classjob}' => [ 'name_{lang} as class_name' ],
                '{items}' => [
                    'name_{lang} as item_name',
                    'name_ja',
                    'name_en',
                    'name_fr',
                    'name_de',
                    'name_cns',
                    'icon',
                    'icon_lodestone',
                    'rarity as item_rarity'
                ],
            ])
            ->from('{recipe}')
            ->join('{recipe}.item', '{items}.id')
            ->join('{recipe}.craft_type', '{recipe_type}.id')
            ->join('{recipe_type}.classjob', '{classjob}.id');
    }

    //
    // Filters
    //
    protected function filters()
    {
        //
        // levels
        //
        $this->handleFilters(['level|gt','level|lt'], 'and');
        $this->handleFilters(['level_view|gt','level_view|lt'], 'and');

        //
        // basic filters
        //
        $this->handleFilters([
            'can_quick_synth',
            'can_hq',
            'level_diff|et',
            'recipe_element|et',
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

        // loop through class jobs
        $where = [];
        foreach($classjobs as $i => $id)
        {
            $bind = ':cj'. $i;

            // where query
            $where[] = sprintf('({table}.classjob = %s)', $bind);

            // append where
            $this->qb()->bind($bind, $id);
        }

        // get valid and/or
        $andor = $this->qb()->getAndOr($this->request->get('classjobs_andor'));

        // append where
        $this->qb()->where($where, $andor);
    }
}
