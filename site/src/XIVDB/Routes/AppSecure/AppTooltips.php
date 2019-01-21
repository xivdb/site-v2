<?php

namespace XIVDB\Routes\AppSecure;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\Content\ContentDB;

//
// AppTooltips
//
trait AppTooltips
{
    //
    // Content routes
    //
    public $types =
    [
        'item' => ContentDB::ITEMS,
        'achievement' => ContentDB::ACHIEVEMENTS,
        'action' => ContentDB::ACTIONS,
        'gathering' => ContentDB::GATHERING,
        'instance' => ContentDB::INSTANCES,
        'leve' => ContentDB::LEVES,
        'enemy' => ContentDB::ENEMY,
        'emote' => ContentDB::EMOTE,
        'placename' => ContentDB::PLACENAMES,
        'status' => ContentDB::STATUS,
        'title' => ContentDB::TITLES,
        'recipe' => ContentDB::RECIPE,
        'quest' => ContentDB::QUEST,
        'shop' => ContentDB::SHOP,
        'npc' => ContentDB::NPC,
        'minion' => ContentDB::MINIONS,
        'mount' => ContentDB::MOUNTS,
        'weather' => ContentDB::WEATHER,
        'fate' => ContentDB::FATES,
        'special-shop' => ContentDB::SPECIAL_SHOPS,
    ];

    protected function _tooltips()
    {
        /**
         * Tooltip request
         */
        $this->route('/tooltip', 'GET|POST|OPTIONS', function (Request $request)
        {
            // Make sure we have tooltips
            if ($list = $request->get('list')) {
                // remove any invalid routes
                foreach ($list as $index => $ids) {
                    // Check against routes array
                    if (!in_array($index, $this->types)) {
                        unset($list[$index]);
                    }

                    // split ids
                    $ids = explode(',', $ids);

                    // limit them to the max, user will need to call
                    // multiple times for more as the response gets
                    // far tooooo big
                    array_splice($ids, MAX_TOOLTIP_IDS);

                    // split ids
                    $list[$index] = $ids;
                }
            }

            // if list
            if ($list) {
                // where the response is stored
                $response = [];
                $count = 0;

                // loop through each type and call each content class individually
                foreach ($list as $type => $ids) {
                    // set initial state
                    $response[$type] = [];

                    // go through each ID
                    foreach ($ids as $id) {
                        // get content id
                        $id = is_numeric($id) ? filter_var(substr($id, 0, 32), FILTER_SANITIZE_NUMBER_INT) : $id;

                        // get real id
                        if ($type == 'item' && strlen($id) > 5) {
                            $dbs = $this->getModule('database');
                            $dbs->QueryBuilder
                                ->select(['id'], false)
                                ->from('xiv_items')
                                ->where('lodestone_id = :id')
                                ->bind(':id', $id)
                                ->limit(0, 1);

                            $realId = $dbs->get()->one();

                            if ($realId) {
                                $id = $realId['id'];
                            }
                        }

                        // key and save path
                        $key = sprintf('%s_%s_%s', $type, $id, LANGUAGE);
                        $savePath = sprintf('%s/%s/', ROOT_TOOLTIPS, $type);
                        if (!is_dir($savePath)) {
                            mkdir($savePath, '0777', true);
                        }
                        $save = sprintf('%s%s.json', $savePath, $key);

                        // if file exists already, add that to the
                        // response, to avoid querying anything.
                        if (file_exists($save) && $tooltip = file_get_contents($save)) {
                            $response[$type][] = json_decode($tooltip, true);
                            $count++;
                            continue;
                        }

                        // create content class
                        $content = $this->getContentClass($type);
                        $data = $content->setId($id)->setType($type)->setFlag('tooltip', true)->get();

                        if (HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
                            if ($data['patch']['patch'] == HIDDEN_PATCH) {
                                continue;
                            }
                        }

                        // get tooltips
                        $response[$type][] = $content->getTooltip();
                        $count++;
                    }
                }

                return $this->json($response);
            }

            // else faile
            return $this->json('Tooltip request failed due to empty id list');
        });
    }
}