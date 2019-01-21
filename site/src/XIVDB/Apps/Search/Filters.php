<?php

/**
 * Filters
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Search;

class Filters extends \XIVDB\Apps\AppHandler
{
    protected $red;
    protected $filters = [];

    function __construct()
    {
        // get modules
        $this->red = $this->getRedis();

        // cache cache
        if ($filters = $this->red->get('filters')) {
            $this->filters = $filters;
            return true;
        }

        $this->database = $this->getModule('database');

        // gogogo
        $this->order();

        // dont change the order, some functions rely on others being processed
        $this->itemcategories();
        $this->grandcompanies();
        $this->itemslots();
        $this->classjob();
        $this->rarity();
        $this->patches();
        $this->attributes();
        $this->sources();
        $this->genres();
        $this->categories();
        $this->aspects();
        $this->instancePartyCount();
        $this->instanceContentType();
        $this->instanceRoulette();
        $this->gatheringTypes();
        $this->gatheringZones();
        $this->gatheringPlacenames();
        $this->npcZones();
        $this->materia();
        $this->placenames();
        $this->points();
        $this->stars();
        $this->getActionsCategory();
        $this->getRecipeElements();
        $this->getInstanceContentType();
        $this->getRandomContentType();
        $this->getGatheringType();
        $this->servers();

        // cache
        $this->red->set('filters', $this->filters, CACHE_GAME_CONTENT_FILTERS);

        #die(show($this->filters));
    }

    //
    // Get filters
    //
    public function get()
    {
        return $this->filters;
    }

    // ---------------------------------------------------------------------

    // order
    private function order()
    {
        $this->filters['order'] =
        [
            'items'         => \XIVDB\Apps\Content\Item::$order,
            'actions'       => \XIVDB\Apps\Content\Action::$order,
            'quests'        => \XIVDB\Apps\Content\Quest::$order,
            'achievements'  => \XIVDB\Apps\Content\Achievement::$order,
            'recipes'       => \XIVDB\Apps\Content\Recipe::$order,
            'instances'     => \XIVDB\Apps\Content\Instance::$order,
            'places'        => \XIVDB\Apps\Content\Placename::$order,
            'emotes'        => \XIVDB\Apps\Content\Emote::$order,
            'enemies'       => \XIVDB\Apps\Content\Enemy::$order,
            'gathering'     => \XIVDB\Apps\Content\Gathering::$order,
            'npcs'          => \XIVDB\Apps\Content\NPC::$order,
            'shops'         => \XIVDB\Apps\Content\Shop::$order,
            'status'        => \XIVDB\Apps\Content\Status::$order,
            'titles'        => \XIVDB\Apps\Content\Title::$order,
            'minions'       => \XIVDB\Apps\Content\Minion::$order,
            'mounts'        => \XIVDB\Apps\Content\Mount::$order,
            'weather'       => \XIVDB\Apps\Content\Weather::$order,
            'fates'         => \XIVDB\Apps\Content\Fate::$order,
            'leves'         => \XIVDB\Apps\Content\Leve::$order,
            'characters'    => \XIVDB\Apps\Content\Characters::$order,
        ];
    }

    // ---------------------------------------------------------------------

    private function getActionsCategory()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name'])
            ->from('xiv_actions_category');

        $this->filters['actions_category'] = $this->database->get()->all();
    }

    private function getRecipeElements()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name'])
            ->from('xiv_recipes_elements');

        $this->filters['recipes_elements'] = $this->database->get()->all();
    }

    private function getGatheringType()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name'])
            ->from('xiv_gathering_type');

        $this->filters['gathering_type'] = $this->database->get()->all();
    }

    private function getInstanceContentType()
    {
        $ids = implode(',', [21,2,3,6,7,5,9,4]);

        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name'])
            ->from('xiv_contents_type')
            ->where('id IN ('. $ids .')')
            ->order('id', 'asc');

        $this->filters['content_type'] = $this->database->get()->all();
    }

    private function getRandomContentType()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name'])
            ->from('xiv_contents_roulette');

        $this->filters['content_roulette'] = $this->database->get()->all();
    }

    private function stars()
    {
        $this->filters['stars'] =
        [
            [ 'id' => 1, 'name' => '1 ★' ],
            [ 'id' => 2, 'name' => '2 ★★' ],
            [ 'id' => 3, 'name' => '3 ★★★' ],
            [ 'id' => 4, 'name' => '4 ★★★★' ],
            [ 'id' => 5, 'name' => '5 ★★★★★' ],
        ];
    }

    private function points()
    {
        $this->filters['points'] =
        [
            [ 'id' => 5, 'name' => '5' ],
            [ 'id' => 10, 'name' => '10' ],
            [ 'id' => 20, 'name' => '20' ],
            [ 'id' => 30, 'name' => '30' ],
            [ 'id' => 40, 'name' => '40' ],
        ];
    }

    private function materia()
    {
        // add rarity
        $this->filters['materia'] =
        [
            [ 'id' => 1, 'name' => '1 ●' ],
            [ 'id' => 2, 'name' => '2 ●●' ],
            [ 'id' => 3, 'name' => '3 ●●●' ],
            [ 'id' => 4, 'name' => '4 ●●●●' ],
            [ 'id' => 5, 'name' => '5 ●●●●●' ],
        ];
    }

    private function servers()
    {
        $this->database->QueryBuilder
            ->select(['name'])
            ->from('xiv_worlds_servers')
            ->order('name', 'asc');

        $this->filters['servers'] = $this->database->get()->all();
    }

    private function classjob()
    {
        $arms = $this->filters['kinds'][0]['categories'];
        $arms = $this->restructure($arms, 'id');

        $this->database->QueryBuilder
            ->select(['id', 'icon', 'name_{lang} as name', 'name_en', 'abbr_{lang} as abbr', 'is_job', 'classjob_category', 'classjob_parent'])
            ->from('xiv_classjobs')
            ->order('id', 'asc');

        $this->filters['classjob'] = $this->database->get()->all();
        unset($this->filters['classjob'][0]);

        $this->filters['crafting'] = [];
        $this->filters['gathering'] = [];

        foreach($this->filters['classjob'] as $i => $a)
        {
            $id = $a['id'];
            $field = 'all';

            if (in_array($id, [8,9,10,11,12,13,14,15])) {
                $field = 'crafting';
            }

            if (in_array($id, [16,17,18])) {
                $field = 'gathering';
            }

            if (in_array($id, [1,2,3,4,5,6,7,26,29])) {
                $field = 'classes';
            }

            if (in_array($id, [31,32,33,34,35])) {
                $field = 'other_jobs';
            }

            if (in_array($id, [19,20,21,22,23,24,25,27,28,30])) {
                $field = 'jobs';
            }

            // append on
            $this->filters[$field][] = $a;
        }
    }

    private function rarity()
    {
        $this->filters['rarity'] = \XIVDB\Apps\Content\ContentDB::$rarity;
    }

    // ---------------------------------------------------------------------

    private function itemcategories()
    {
        //
        // TODO : REALLY NEEDS TO BE A JOIN STATEMENTZ
        // ok because its cached like crazy
        //

        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_items_ui_kind');

        $kinds = $this->database->get()->all();
        foreach($kinds as $i => $kind)
        {
            $this->database->QueryBuilder
                ->select(['id', 'name_{lang} as name', 'name_en'])
                ->from('xiv_items_ui_category')
                ->where('item_ui_kind = '. $kind['id'])
                ->order('priority', 'asc');

            $kinds[$i]['categories'] = $this->database->get()->all();
        }

        $this->filters['kinds'] = $kinds;
    }

    private function grandcompanies()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_gc')
            ->where('id != 0');

        $this->filters['grand_company'] = $this->database->get()->all();
    }

    private function itemslots()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_items_ui_slot');

        $this->filters['slot_equip'] = $this->database->get()->all();
    }

    private function patches()
    {
        $this->database->QueryBuilder
            ->select(['patch as number', 'command', 'patch_url', 'name_{lang} as name', 'name_en', 'banner', 'date', 'is_expansion'])
            ->from('db_patch')
            ->order('patch', 'desc');

        $this->filters['patches'] = $this->database->get()->all();
    }

    private function attributes()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_baseparams')
            ->notempty('name_en')
            ->order('id', 'asc');

        $this->filters['attributes'] = $this->database->get()->all();
        $this->sksort($this->filters['attributes'], 'name', true);

        foreach($this->filters['attributes'] as $id => $attr) {
            if ($attr['id'] == '47') {
                $attr['name'] = 'Speed (DEV)';
                $this->filters['attributes'][$id] = $attr;
                break;
            }
        }
    }

    private function genres()
    {
        //
        // get journal categories
        //
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_journal_category')
            ->notempty('name_en')
            ->order('id', 'asc');

        $categories = $this->database->get()->all();

        //
        // Get journal genres
        //
        $this->database->QueryBuilder
            ->select(['id', 'journal_category', 'name_{lang} as name', 'name_en'])
            ->from('xiv_journal_genre')
            ->notempty('name_en')
            ->order('id', 'asc');

        $genres = $this->database->get()->all();

        $initlist = [];
        foreach($genres as $g) {
            $initlist[$g['journal_category']][] = $g;
        }

        $datalist = [];
        foreach($categories as $c) {
            if (isset($initlist[$c['id']])) {
                $datalist[$c['id']] = $c;
                $datalist[$c['id']]['genres'] = $initlist[$c['id']];
            }

        }

        $this->filters['genres'] = $datalist;
    }

    private function categories()
    {
        //
        // Achievement categories
        //
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_achievements_kind')
            ->notempty('name_en')
            ->order('id', 'asc');

        $categories = $this->database->get()->all();

        //
        // Achievement kinds
        //
        $this->database->QueryBuilder
            ->select(['id', 'achievement_kind', 'name_{lang} as name', 'name_en'])
            ->from('xiv_achievements_category')
            ->notempty('name_en')
            ->order('id', 'asc');


        $genres = $this->database->get()->all();

        $initlist = [];
        foreach($genres as $g) {
            $initlist[$g['achievement_kind']][] = $g;
        }

        $datalist = [];
        foreach($categories as $c) {
            $datalist[$c['id']] = $c;
            $datalist[$c['id']]['categories'] = $initlist[$c['id']];
        }

        $this->filters['categories'] = $datalist;
    }

    private function sources()
    {
        $arr = [
            'items' =>
            [
                [ 'name' => 'Achievement', 'id' => 'achievement' ],
                [ 'name' => 'Craftable', 'id' => 'craftable' ],
                [ 'name' => 'Instance (General)', 'id' => 'instance' ],
                [ 'name' => 'Instance (Reward)', 'id' => 'instance_reward' ],
                [ 'name' => 'Instance (Chest)', 'id' => 'instance_chest' ],
                [ 'name' => 'Quest', 'id' => 'quest' ],
                [ 'name' => 'Enemy', 'id' => 'enemy' ],
                [ 'name' => 'Shop', 'id' => 'shop' ],
                [ 'name' => 'Gathered', 'id' => 'gathering' ],
            ],
        ];

        $this->filters['source'] = $arr;
    }

    private function aspects()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_recipes_elements');

        $this->filters['aspects'] = $this->database->get()->all();
    }

    private function instancePartyCount()
    {
        $this->database->QueryBuilder
            ->select(['party_count'])
            ->from('xiv_instances');

        $this->filters['partycount'] = $this->database->get()->all();
    }

    private function instanceContentType()
    {
        $this->filters['contenttype'] = [];

        $this->database->QueryBuilder
            ->select('*')
            ->from('xiv_instances_type');

        $instanceTypes = $this->database->get()->all();

        // get content_type ids
        $ids = [];
        foreach($instanceTypes as $i) {
            if (!in_array($i['content_type'], $ids)) {
                $ids[] = $i['content_type'];
            }
        }

        if ($ids) {
            $this->database->QueryBuilder
                ->select(['id', 'name_{lang} as name', 'name_en'])
                ->from('xiv_contents_type')
                ->where('id IN ('. implode(',', $ids) .')');

            $this->filters['contenttype'] = $this->database->get()->all();
        }
    }

    private function instanceRoulette()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_contents_roulette');

        $this->filters['roulette'] = $this->database->get()->all();
    }

    private function gatheringTypes()
    {
        $this->database->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'name_en'])
            ->from('xiv_gathering_type');

        $this->filters['gatheringtypes'] = $this->database->get()->all();
        $this->filters['gatheringtypes'] = $this->restructure($this->filters['gatheringtypes'], 'id');
    }

    private function gatheringZones()
    {
        $this->filters['gatheringzones'] = [];

        $this->database->QueryBuilder
            ->select(['zone'])
            ->from('xiv_gathering_to_nodes')
            ->group('zone');

        $zones = $this->database->get()->all();

        $ids = [];
        foreach($zones as $z) {
            $ids[] = $z['zone'];
        }

        if ($ids)
        {
            $this->database->QueryBuilder
                ->select(['id', 'name_{lang} as name', 'name_en'])
                ->from('xiv_placenames')
                ->where('id IN ('. implode(',', $ids) .')');

            $this->filters['gatheringzones'] = $this->database->get()->all();
        }
    }

    private function gatheringPlacenames()
    {
        $this->filters['gatheringplacename'] = [];

        $this->database->QueryBuilder
            ->select(['placename'])
            ->from('xiv_gathering_to_nodes')
            ->group('placename');

        $zones = $this->database->get()->all();

        $ids = [];
        foreach($zones as $z) {
            $ids[] = $z['placename'];
        }

        if ($ids)
        {
            $this->database->QueryBuilder
                 ->select(['id', 'name_{lang} as name', 'name_en', 'patch'])
                 ->from('xiv_placenames')
                 ->where('id IN ('. implode(',', $ids) .')');

            $this->filters['gatheringplacename'] = $this->database->get()->all();
        }
    }

    private function npcZones()
    {
        $this->filters['npczones'] = [];

        $this->database->QueryBuilder
            ->select(['placename'])
            ->from('xiv_npc_to_area')
            ->group('placename');

        $zones = $this->database->get()->all();

        $ids = [];
        foreach($zones as $z) {
            $ids[] = $z['placename'];
        }

        if ($ids)
        {
            $this->database->QueryBuilder
                 ->select(['id', 'name_{lang} as name', 'name_en'])
                 ->from('xiv_placenames')
                 ->where('id IN ('. implode(',', $ids) .')');

            $this->filters['npczones'] = $this->database->get()->all();
        }
    }

    private function placenames()
    {
        $regions = $this->database->sql('SELECT id, name_{lang} as name, region FROM `xiv_placenames` WHERE region > 0 GROUP BY region ORDER BY `xiv_placenames`.`id` ASC');

        $data = [];
        foreach($regions as $region)
        {
            $placenameSql = 'SELECT id, name_{lang} as name FROM `xiv_placenames` WHERE region = %s and id != %s ORDER BY `xiv_placenames`.`name_{lang}` ASC';
            $placenameSql = sprintf($placenameSql, $region['id'], $region['id']);

            $region['placenames'] = $this->database->sql($placenameSql);
            $data[] = $region;
        }

        $this->filters['placenames'] = $data;
    }
}
