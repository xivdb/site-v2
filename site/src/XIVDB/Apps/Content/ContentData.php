<?php

namespace XIVDB\Apps\Content;

trait ContentData
{
    private $store = [];

    //
    // Handle the cache for extended content
    //
    public function cache($key, $data = false)
    {
        // key
        $flags = implode('-', $this->flags);
        $key = sprintf('ec_%s_%s', $key, $flags);

        if (!CACHE_GAME_CONTENT) {
            return false;
        }

        if (isset($this->store[$key])) {
            return $this->store[$key];
        }

        // get redis
        $redis = $this->getRedis();

        // if storing
        if ($data) {
            $redis->set($key, $data, CACHE_GAME_CONTENT_DATA);
            $this->store[$key] = $data;
            return true;
        }

        // redis
        if ($data = $redis->get($key)) {
            return $data;
        }

        return false;
    }

    //
    // Join some data that isn't a specific content class
    //
    public function join($mainTable, $joinTable, $joinColumn, $whereColumn, $mainColumns)
    {
        $dbs = $this->getModule('database');

        //
        // Eg of this query:
        //
        // $this->join(ContentDB::CLASSJOB, ContentDB::ITEMS_TO_CLASSJOB, 'classjob', 'item', [
        //      'id', 'name_{lang} as name', 'abbr_{lang} as abbr', 'is_job', 'icon', 'patch'
        // ]);
        //
        // SELECT
        //   xiv_classjobs.`id`,
        //   xiv_classjobs.`name_en` as `name`,
        //   xiv_classjobs.`abbr_en` as `abbr`,
        //   xiv_classjobs.`is_job`,
        //   xiv_classjobs.`icon`,
        //   xiv_classjobs.`patch`
        // FROM xiv_classjobs
        // LEFT JOIN xiv_items_to_classjob ON xiv_items_to_classjob.classjob = xiv_classjobs.id
        // WHERE (xiv_items_to_classjob.item = :id)
        //

        $dbs->QueryBuilder
            ->select([ $mainTable => $mainColumns ], false)
            ->from($mainTable)
            ->join([ $mainTable => 'id' ], [ $joinTable => $joinColumn ])
            ->where(sprintf('%s.%s = :id', $joinTable, $whereColumn))
            ->bind('id', $this->id);

        return $dbs->get()->all();
    }

    //
    // Fetch some data using its class
    //
    public function fetch($id, $key, $class, $extended = true, $ignore = false)
    {
        if (!$id) {
            return false;
        }

        // local variable when resting a lot of the same data
        if (!$ignore && isset($this->store[$key])) {
            return $this->store[$key];
        }

        // check redis
        if (!$ignore && $data = $this->cache($key)) {
            return $data;
        }

        // get data from db
        $data = $class->setId($id)->setFlag('extended', $extended)->get();

        // if caching
        if (!$ignore) {
            $this->cache($key, $data);
            $this->store[$key] = $data;
        }

        return $data;
    }

    //
    // Some column exists?
    //
    public function hasColumn($column)
    {
        return (isset($this->data[$column]) && $this->data[$column]) ? true : false;
    }

    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    # SQL Fetched Content
    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // Add a class job category
    //
    public function addPatch($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                'patch',
                'name_{lang} as name',
                'command as number',
                'patch_url as url',
            ])
            ->from(ContentDB::PATCH)
            ->where('patch = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();

        return $data ? $data : null;
    }

    //
    // Add emote category
    //
    public function addContentMaps($cid, $id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::CONTENT_MAPS)
            ->where('type_id = :cid')->bind('cid', $cid)
            ->where('content_id = :id')->bind('id', $id);

        $data = $dbs->get()->all();

        foreach($data as $i => $d) {
            // if no icon, set to current content
            if (empty($d['icon'])) {
                $data[$i]['icon'] = $this->iconize(isset($this->data['icon']) ? $this->data['icon'] : null);
            }

            // add placename
            if ($d['placename']) {
                $data[$i]['placename'] = $this->addPlacename($d['placename']);
            }
        }

        return $data;
    }

    //
    // Add a class job category
    //
    public function addClassJobCategory($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['name_{lang} as name'])
            ->from(ContentDB::CLASSJOB_CATEGORY)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();

        return $data ? $data['name'] : null;
    }

    //
    // Add emote category
    //
    public function addEmoteCategory($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['name_{lang} as name'])
            ->from(ContentDB::EMOTE_CATEGORY)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();

        return $data ? $data['name'] : null;
    }

    //
    // Add text command
    //
    public function addTextCommand($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                'help_{lang} as help',
                'command1_{lang} as command1',
                'command2_{lang} as command2',
                'command3_{lang} as command3',
                'command4_{lang} as command4'
            ])
            ->from(ContentDB::TEXT_COMMANDS)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();

        return $data ? $data : null;
    }

    //
    // Add an item to the content
    //
    public function addClassJob($idOrName, $isName = false)
    {
        if (!$idOrName) return false;
        $key = __FUNCTION__ . md5($idOrName);
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'abbr_{lang} as abbr', 'is_job', 'classjob_parent', 'icon', 'patch' ])
            ->from(ContentDB::CLASSJOB)
            ->notnull('name_en');

        $qq = $isName
            ? $dbs->QueryBuilder->where('name_en = :name')->bind('name', $idOrName)
            : $dbs->QueryBuilder->where('id = :id')->bind('id', $idOrName);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        // return
        return $data;
    }

    //
    // Add Web Ex Quest
    //
    public function addWebExQuest($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([ 'id', 'name_{lang} as name',  'patch', ])
            ->from(ContentDB::QUEST_WEBEX)
            ->notnull('name_en')
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add an event item
    //
    public function addEventItem($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([ 'id','name_{lang} as name','help_{lang} as help','name_title_{lang} as name_title','icon','quest','rarity','stack_size','patch' ])
            ->from(ContentDB::TO_EVENT_ITEMS)
            ->notnull('name_en')
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add a XYZ coordinate
    //
    public function addXYZ($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::XYZ)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add a XYZ
    //
    public function addXYZByKey($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::XYZ)
            ->where('object_key = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add leve client
    //
    public function addLevesClient($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'patch'])
            ->from(ContentDB::LEVES_CLIENT)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add leve VFX
    //
    public function addLeveVFX($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'file', 'icon'])
            ->from(ContentDB::LEVES_VFX)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add leve assignment type
    //
    public function addLeveAssignmentType($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'name_{lang} as name', 'icon', 'is_faction'])
            ->from(ContentDB::LEVES_ASSIGNMENT)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add reward group
    //
    public function addLeveRewardGroup($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'group', 'probability'])
            ->from(ContentDB::LEVES_REWARD_GROUP)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->all();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add reward item
    //
    public function addLeveRewardItem($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['id', 'item', 'quantity', 'hq'])
            ->from(ContentDB::LEVES_REWARD_ITEM)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->all();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add placename map
    //
    public function addPlacenameMap($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::PLACENAMES_MAPS)
            ->where('placename = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add map
    //
    public function addMap($id)
    {
        if (!$id) return false;
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::PLACENAMES_MAPS)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->one();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add base attributes
    //
    public function addBaseAttributes($id)
    {
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from(ContentDB::TO_BASE)
            ->where('id = :id')
            ->bind('id', $id);

        $data = $dbs->get()->all();
        $data = $data ? $data[0] : null;
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add param attributes
    //
    public function addParamAttributes($id)
    {
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                ContentDB::TO_STATS => ['value', 'value_hq', 'percent', 'percent_hq', 'is_relative', 'is_food', 'patch as stat_patch'],
                ContentDB::PARAMS => ['id', 'name_{lang} as name', 'patch as param_patch']
            ])
            ->from(ContentDB::TO_STATS)
            ->join([ ContentDB::TO_STATS => 'baseparam' ], [ ContentDB::PARAMS => 'id' ])
            ->where(sprintf('%s.item = :id', ContentDB::TO_STATS))
            ->where(sprintf('%s.special = 0', ContentDB::TO_STATS))
            ->bind('id', $id);

        $data = $dbs->get()->all();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add special param attributes
    //
    public function addSpecialParamAttributes($id)
    {
        $key = __FUNCTION__ . $id;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                ContentDB::TO_STATS => ['value', 'value_hq', 'percent', 'percent_hq', 'is_relative', 'is_food', 'patch as stat_patch'],
                ContentDB::PARAMS => ['id', 'name_{lang} as name', 'patch as param_patch']
            ])
            ->from(ContentDB::TO_STATS)
            ->join([ ContentDB::TO_STATS => 'baseparam' ], [ ContentDB::PARAMS => 'id' ])
            ->where(sprintf('%s.item = :id', ContentDB::TO_STATS))
            ->where(sprintf('%s.special = 1', ContentDB::TO_STATS))
            ->bind('id', $id);

        $data = $dbs->get()->all();
        $this->cache($key, $data);

        return $data;
    }

    //
    // Add currency
    //
    public function addTomestonesItems()
    {
        $key = __FUNCTION__;
        if ($data = $this->cache($key)) return $data;

        // get
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select(['item', 'weekly_limit', 'label'])
            ->from(ContentDB::TOMESTONES)
            ->notnull('label');

        $data = [];
        foreach($dbs->get()->all() as $i => $currency) {
            $data[$currency['label']] = [
                'weekly_limit' => $currency['weekly_limit'],
                'item' => $this->addItem($currency['item']),
            ];
        }

        $this->cache($key, $data);
        return $data;
    }

    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    # Class Fetched Content
    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // Add an item to the content
    //
    public function addItem($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Item());
    }

    //
    // Add an item to the content
    //
    public function addItemExtended($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Item(), false);
    }

    //
    // Add an action
    //
    public function addLeve($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Leve());
    }

    //
    // Add a placename
    //
    public function addPlacename($id, $isSimplified = false)
    {
        if ($isSimplified) {
            if (!$id) return false;

            $key = 'simplified'. __FUNCTION__ . $id;
            if ($data = $this->cache($key)) return $data;

            // get
            $dbs = $this->getModule('database');
            $dbs->QueryBuilder
                ->select(['name_{lang} as name'])
                ->from(ContentDB::PLACENAMES)
                ->where('id = :id')
                ->bind('id', $id);

            $data = $dbs->get()->one();
            $this->cache($key, $data);

            return $data;
        }

        return $this->fetch($id, __FUNCTION__ . $id, new Placename());
    }

    //
    // Add a instance
    //
    public function addInstance($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Instance());
    }

    //
    // Add a quest
    //
    public function addQuest($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Quest());
    }

    //
    // Add an action
    //
    public function addAction($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Action());
    }

    //
    // Add a mount
    //
    public function addMount($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Mount());
    }

    //
    // Add a minion
    //
    public function addMinion($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Minion());
    }

    //
    // Add a fate
    //
    public function addFate($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Fate());
    }

    //
    // Add a fate
    //
    public function addGathering($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Gathering());
    }

    //
    // Add a emote
    //
    public function addEmote($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Emote());
    }

    //
    // Add a status
    //
    public function addStatus($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Status());
    }

    //
    // Add a weather
    //
    public function addWeather($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Weather());
    }

    //
    // Add an Enemy
    //
    public function addEnemy($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Enemy());
    }

    //
    // Add an NPC
    //
    public function addNPC($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new NPC());
    }

    //
    // Add a shop
    //
    public function addShop($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Shop());
    }

    //
    // Add a special shop
    //
    public function addSpecialShop($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new SpecialShop());
    }

    //
    // Add an recipe
    //
    public function addRecipe($id, $quantity = 1, $getAll = false, $extended = true)
    {
        $class = new Recipe($getAll, $quantity);
        $class->setFlag('cart', $this->isFlagged('cart'));
        return $this->fetch($id, __FUNCTION__ . $quantity . $id, $class, $extended, true);
    }

    //
    // Add an achievement
    //
    public function addAchievement($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Achievement());
    }

    //
    // Add an title
    //
    public function addTitle($id)
    {
        return $this->fetch($id, __FUNCTION__ . $id, new Title());
    }

    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    # other stuff
    # - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    //
    // Get content ids (for comments/screenshots)
    //
    public function getContentIds($id = false)
    {
        if ($id) {
            return ContentDB::$contentIds[$id];
        }

        return ContentDB::$contentIds;
    }

    //
    // Get the name of a content id based on its number
    //
    public function getContentIdName($id)
    {
        return array_search($id, ContentDB::$contentIds);
    }

    //
    // Get some content based on the content id
    //
    public function getContentToId($contentId, $id)
    {
        switch($contentId)
        {
            case 1: return $this->addItem($id); break;
            case 3: return $this->addAction($id); break;
            case 4: return $this->addQuest($id); break;
            case 5: return $this->addRecipe($id); break;
            case 7: return $this->addFate($id); break;
            case 8: return $this->addAchievement($id); break;
            case 11: return $this->addNpc($id); break;
            case 12: return $this->addEnemy($id); break;
            case 15: return $this->addMount($id); break;
            case 16: return $this->addInstance($id); break;
            case 18: return $this->addMinion($id); break;
            case 20: return $this->addLeve($id); break;
            case 200: return $this->addPlacename($id); break;
            case 201: return $this->addShop($id); break;
            case 202: return $this->addGathering($id); break;
            case 203: return $this->addEmote($id); break;
            case 204: return $this->addStatus($id); break;
            case 205: return $this->addTitle($id); break;
            case 206: return $this->addWeather($id); break;
        }

        return null;
    }

}
