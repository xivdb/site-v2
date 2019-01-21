<?php

namespace XIVDB\Apps\Content;

class ContentDB
{
    public static $rarity = [
        0 => 'none',
        1 => 'common',
        2 => 'uncommon',
        3 => 'rare',
        4 => 'relic',
        7 => 'aetherial'
    ];

    public static $repairCosts = [
        5594 => 4,
        5595 => 12,
        5596 => 24,
        5597 => 48,
        5598 => 80,
        10386 => 120,
    ];

    public static $repairLevels = [
        5594 => 1,
        5595 => 10,
        5596 => 20,
        5597 => 30,
        5598 => 40,
        10386 => 50,
    ];

    // Content ids, for things like comments etc.
    public static $contentIds =
    [
        'item' => 1,
        //'screenshot' => 2,
        'action' => 3,
        'quest' => 4,
        'recipe' => 5,
        //'news' => 6,
        'fate' => 7,
        'achievement' => 8,
        'huntinglog' => 9,
        //'wardrobe' => 10,
        'npc' => 11,
        'enemy' => 12,
        // 13, 14
        'mount' => 15,
        'instance' => 16,
        // 17
        'minion' => 18,
        'fcstatus' => 19,
        'leve' => 20,

        // starting high as xivdb v1 had some around 100.
        'placename' => 200,
        'shop' => 201,
        'gathering' => 202,
        'emote' => 203,
        'status' => 204,
        'title' => 205,
        'weather' => 206,
        'special-shop' => 207,

        'character' => 300,
    ];

    const COMMENTS = 'content_comments';
    const MEMBERS = 'members';
    const MEMBERS_CHARACTERS = 'members_characters';
    const PATCH = 'db_patch';
    const CONTENT_MAPS = 'content_maps';
    const APP_MAPPER = 'app_mapper';

    const ITEMS = 'xiv_items';
    const ACHIEVEMENTS = 'xiv_achievements';
    const ACHIEVEMENTS_CATEGORY = 'xiv_achievements_category';
    const ACHIEVEMENTS_KIND = 'xiv_achievements_kind';
    const ACTIONS = 'xiv_actions';
    const PARAMS = 'xiv_baseparams';
    const CLASSJOB = 'xiv_classjobs';
    const CLASSJOB_CATEGORY = 'xiv_classjobs_category';
    const CONTENT_TYPE = 'xiv_contents_type';
    const INSTANCES = 'xiv_instances';
    const INSTANCES_TYPE = 'xiv_instances_type';
    const INSTANCES_ROULETTE = 'xiv_contents_roulette';
    const QUEST = 'xiv_quests';
    const QUEST_WEBTYPE = 'xiv_quests_webtypes';
    const QUEST_JOBCLASS = 'xiv_quests_to_classjob';
    const QUEST_JOBCLASS_CATEGORY = 'xiv_quests_to_classjob_category';
    const QUEST_POST = 'xiv_quests_to_post_quest';
    const QUEST_PRE = 'xiv_quests_to_pre_quest';
    const QUEST_REWARDS = 'xiv_quests_to_rewards';
    const QUEST_WEBEX = 'xiv_quests_webex';
    const NPC = 'xiv_npc';
    const NPC_AREA = 'xiv_npc_to_region';
    const NPC_POSITION = 'xiv_npc_to_area';
    const RECIPE = 'xiv_recipes';
    const RECIPE_TYPE = 'xiv_recipes_craft_type';
    const RECIPE_ELEMENT = 'xiv_recipes_elements';
    const RECIPE_MASTERBOOKS = 'xiv_recipes_masterbooks';
    const ENEMY = 'xiv_npc_enemy';
    const ENEMY_AREA = 'xiv_npc_enemy_to_area';
    const SHOP = 'xiv_shops';
    const SPECIAL_SHOPS = 'xiv_special_shops';
    const GATHERING = 'xiv_gathering';
    const GATHERING_TYPE = 'xiv_gathering_type';
    const GATHERING_NODES = 'xiv_gathering_to_nodes';
    const PLACENAMES = 'xiv_placenames';
    const PLACENAMES_MAPS = 'xiv_placenames_maps';
    const BEAST_TRIBE = 'xiv_beast_tribe';
    const EMOTE = 'xiv_emotes';
	const EMOTE_CATEGORY = 'xiv_emotes_category';
    const STATUS = 'xiv_status';
    const TITLES = 'xiv_titles';
    const TEXT_COMMANDS = 'xiv_text_commands';
    const TOMESTONES = 'xiv_tomestones';
    const JOURNAL_GENRE = 'xiv_journal_genre';
    const JOURNAL_CATEGORY = 'xiv_journal_category';
    const MINIONS = 'xiv_companions';
    const MINIONS_RACE = 'xiv_companions_race';
    const MOUNTS = 'xiv_mounts';
    const WEATHER = 'xiv_weather';
    const FATES = 'xiv_fates';
    const LEVES = 'xiv_leves';
    const LEVES_CLIENT = 'xiv_leves_client';
    const LEVES_VFX = 'xiv_leves_vfx';
    const LEVES_ASSIGNMENT = 'xiv_leves_assignment_type';
    const LEVES_REWARD_GROUP = 'xiv_leves_reward_groups';
    const LEVES_REWARD_ITEM = 'xiv_leves_reward_items';
    const XYZ = 'xiv_level';

    const TO_SERIES = 'xiv_items_series';
    const TO_BONUS = 'xiv_items_special_bonus';
    const TO_BASE = 'xiv_items_base_stats';
    const TO_STATS = 'xiv_items_to_baseparam';
    const TO_CLASSJOB = 'xiv_items_to_classjob';
    const TO_CLASSJOB_QUESTS = 'xiv_quests_to_classjob';
    const TO_CLASSJOB_CATEGORY = 'xiv_classjobs_to_category';
    const TO_CATEGORY = 'xiv_items_ui_category';
    const TO_KIND = 'xiv_items_ui_kind';
    const TO_SLOT = 'xiv_items_ui_slot';
    const TO_INSTANCE = 'xiv_instances_to_items';
    const TO_INSTANCE_CHEST = 'xiv_instances_chests_to_items';
    const TO_INSTANCE_REWARD = 'xiv_instances_to_rewards';
    const TO_INSTANCE_ENEMY = 'xiv_instances_to_bosses';
    const TO_INSTANCE_CURRENCY = 'xiv_instances_to_currency';
    const TO_QUESTS = 'xiv_quests_to_rewards';
    const TO_QUESTS_TEXT = 'xiv_quests_to_text';
    const TO_NPC_QUESTS = 'xiv_npc_to_quest';
    const TO_NPC_AREA = 'xiv_npc_to_area';
    const TO_NPC_REGION = 'xiv_npc_to_region';
    const TO_NPC_SHOP = 'xiv_npc_to_shop';
    const TO_SHOP = 'xiv_shops_to_item';
    const TO_SPECIAL_SHOP = 'xiv_special_shops_to_item';
    const TO_RECIPE = 'xiv_recipes_to_item';
    const TO_ENEMY = 'xiv_npc_enemy_to_items';
    const TO_ENEMY_NONPOP = 'xiv_npc_enemy_nonpop';
    const TO_ENEMY_INSTANCE = 'xiv_npc_enemy_to_instance';
    const TO_ACHIEVEMENT_POST = 'xiv_achievements_post';
    const TO_ACHIEVEMENT_PRE = 'xiv_achievements_pre';
    const TO_ACHIEVEMENT_QUESTS = 'xiv_achievements_pre_quests';
    const TO_EVENT_ITEMS = 'xiv_event_items';
    const TO_LEVES = 'xiv_leves_to_items';

    // New to replace old - More logical meaning
    const ITEMS_TO_CLASSJOB = 'xiv_items_to_classjob';

    // characters
    const CHARACTERS = 'characters';
    const CHARACTERS_DATA = 'characters_data';
    const CHARACTERS_ACHIEVEMENTS_LIST = 'characters_achievements_list';
    const CHARACTERS_ACHIEVEMENTS_POSSIBLE = 'characters_achievements_possible';
    const CHARACTERS_EVENTS_EXP_NEW = 'characters_events_exp_new';
    const CHARACTERS_EVENTS_LVS_NEW = 'characters_events_lvs_new';
    const CHARACTERS_EVENTS_TRACKING = 'characters_events_tracking';
    const CHARACTERS_GEARSETS = 'characters_gearsets';
    const CHARACTERS_GRANDCOMPANY = 'characters_grandcompany';
}
