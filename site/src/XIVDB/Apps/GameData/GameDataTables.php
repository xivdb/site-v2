<?php

namespace XIVDB\Apps\GameData;

trait GameDataTables
{
    //
    // Get SQL that creates the columns
    //
    public function getCreateSql()
    {
        $sql = [];

        // real columns
        if (property_exists($this,'real')) {
            foreach($this->real as $offset => $column) {
                $sql[] = sprintf("ALTER TABLE `%s` ADD `%s` INT(16) NULL DEFAULT NULL;ALTER TABLE `%s` ADD INDEX(`%s`);", static::TABLE, $column, static::TABLE, $column);
            }
        }

        // set columns
        if (property_exists($this,'set')) {
            $sql[] = '';

            foreach($this->set as $offset => $column) {
                $sql[] = sprintf("ALTER TABLE `%s` ADD `%s` INT(16) NULL DEFAULT NULL;", static::TABLE, $column);
            }
        }

        // repeater columns
        if (property_exists($this,'repeater')) {
            $sql[] = '';

            foreach($this->repeater['columns'] as $column) {
                $sql[] = sprintf("ALTER TABLE `%s_repeater` ADD `%s` INT(16) NULL DEFAULT NULL;ALTER TABLE `%s_repeater` ADD INDEX(`%s`);", static::TABLE, $column, static::TABLE, $column);
            }
        }

        return implode("\n", $sql);
    }

    //
    // Return the data name
    //
    public function getDataNames()
    {
        return [
            // import firsts as they're used in other stuff
            'ParamGrow',
            'ItemSearchCategory',
            'ItemSeries',
            'ItemUICategory',
            'AchievementCategory',
            'AchievementKind',

            // generic stuff
            'ItemSpecialBonus',
            'ActionCategory',
            'Addon',
            'BaseParam',
            'BeastReputationRank',
            'AddonTransient',
            'AddonParam',
            'Aetheryte',
            'AirshipExplorationLog',
            'AirshipExplorationParamType',
            'AirshipExplorationPoint',
            'AirshipExplorationLevel',
            'AirshipExplorationPart',
            'ActionProcStatus',
            'Cabinet',
            'ChainBonus',
            'ChocoboTaxiStand',
            'ChocoboRaceAbilityType',
            'CompanionMove',
            'CompanyAction',
            'CompanyCraftDraft',
            'CompanyCraftDraftCategory',
            'CompanyCraftDraftCategoryTxt',
            'CompanyCraftManufactoryState',
            'CompanyCraftType',
            'CompleteJournal',
            'ContentType',
            'CraftType',
            'CreditCast',
            'Cutscene',
            'Emote',
            'EmoteCategory',
            'Error',
            'EventAction',
            'EventItem',
            'EventSystemDefine',
            'ExVersion',
            'ExportedSG',
            'EquipSlotCategory',
            'EventIconPriority',
            'EventIconType',
            'FCActivity',
            'FCActivityCategory',
            'FCAuthority',
            'FCAuthorityCategory',
            'FCChestName',
            'FCHierarchy',
            'FCProfile',
            'FCReputation',
            'FCRights',
            'FateEvent',
            'FccShop',
            'Festival',
            'FieldMarker',
            'FieldMarkerText',
            'FishParameter',
            'FishingSpot',
            'FishingSpotCategory',
            'GCRankGridaniaFemaleText',
            'GCRankGridaniaMaleText',
            'GCRankLimsaFemaleText',
            'GCRankLimsaMaleText',
            'GCRankUldahFemaleText',
            'GCRankUldahMaleText',
            'GCShopItemCategory',
            'GCShop',
            'GCSupplyDuty',
            'GFateClimbing',
            'GFateStelth',
            'GatheringCondition',
            'GatheringPointBonusType',
            'GatheringPointBonus',
            'GatheringPointName',
            //'GatheringPointBase',
            'GatheringSubCategory',
            'GatheringType',
            'GatheringExp',
            'GatheringItem',
            'GatheringPoint',
            'GatheringNotebookList',
            'GatheringNotebookRegion',
            'GardeningSeed',
            'GimmickBill',
            'GimmickYesNo',
            'GoldSaucerArcadeMachine',
            'GoldSaucerTalk',
            'GoldSaucerTextData',
            'GrandCompany',
            'GrandCompanyRank',
            'GrandCompanySealShopItem',
            'GuardianDeity',
            'GuildOrder',
            'GuildleveEvaluation',
            'HousingExterior',
            'HousingInterior',
            'HousingItemCategory',
            'HousingPreset',
            'HousingFurniture',
            'HousingLayoutLimit',
            'HousingMateAuthority',
            'HousingPlacement',
            'HousingYardObject',
            'HowTo',
            'HowToCategory',
            'HowToPage',
            'Jingle',
            'JournalCategory',
            'JournalGenre',
            'JournalSection',
            'LegacyQuest',
            'LeveAssignmentType',
            'LeveClient',
            'LeveString',
            'LiveCondition',
            'LiveConditionSign',
            'Lobby',
            'LogFilter',
            'LogKind',
            'LogKindCategoryText',
            //'LogMessage',
            'MainCommand',
            'MainCommandCategory',
            'Materia',
            'PublicContent',
            'PublicContentTextData',
            'PartyContent',
            'PvPRankTransient',
            'QuestRewardOther',
            'RCNameCategoryText',
            'Race',
            'RacingChocoboName',
            'RacingChocoboNameCategory',
            'RacingChocoboNameText',
            'RacingChocoboParam',
            'RacingChocoboParamText',
            'RacingChocoboItem',
            'RecipeElement',
            'RecipeLevelTable',
            'Relic',
            'Relic3',
            'RetainerTaskNormal',
            'Relic6Magicite',
            'RelicNoteCategory',
            'RelicNoteCategoryText',
            'RetainerTask',
            'RetainerTaskRandom',
            'RetainerTaskRandomText',
            'Role',
            'TextCommand',
            'TextCommandParam',
            'Town',
            'Treasure',
            'Tribe',
            'TripleTriadCard',
            'TripleTriadCardType',
            'TripleTriadCompetition',
            'TripleTriadRule',
            'TripleTriadCardResident',
            'TripleTriadCardRarity',
            'Tomestones',
            'TomestonesItem',
            'Weather',
            'WeatherRate',
            'WeddingBGM',
            'World',
            'WorldDCGroupType',
            'SecretRecipeBook',

            // important
            'ClassJobCategory',
            //'AddonHud',
            'Adventure',
            'AttackType',
            'BGM',
            'Balloon',
            'BeastTribe',
            'BuddyAction',
            'BNpcName',
            'BuddyEquip',
            'ChocoboRaceAbility',
            'ChocoboRaceItem',
            'Completion',
            'ConfigKey',
            'ContentRoulette',
            'ContentFinderCondition',
            'ContentFinderConditionTransient',
            'ContentTalk',
            'ContentsNote',
            'CustomTalk',
            'GeneralAction',
            'SE',
            'InstanceContentTextData',
            #'EObj',
            'Pet',
            'PetAction',
            'NpcYell',
            'OnlineStatus',
            'Orchestrion',
            'OrchestrionCategory',
            'Marker',
            'Stain',
            'MonsterNote',
            'MonsterNoteTarget',
            'NotebookDivision',
            #'Shop',
            #'ShopItem',
            'SpecialShop',
            'SpecialShopItemCategory',
            'VFX',
            'LeveVfx',
            'LeveRewardItem',
            'LeveRewardItemGroup',
            'LeveCraft',
            'AddonSlotUi',
            'CraftLeveTalk',
            'DefaultTalk',
            'AetherCurrent',
            'BNpcBase',
            'ChocoboRace',
            'MinionRace',
            'MinionRules',
            'MinionSkillType',
            'MinionStage',
            'MasterpieceSupplyDuty',

            // the big stuff
            'ClassJob',
            'Achievement',
            'Companion',
            'CompanionTransient',
            'Item',
            'ItemFood',
            'CraftAction',
            'Traits',
            'TraitTransient',
            'Action',
            'ActionTransient',
            'Status',
            'Title',
            'Mount',
            'MountTransient',
            'PlaceName',
            'Map',
            'Fate',
            'Leve',
            'InstanceContent',
            'ENpcResident',
            'ENpcBase',
            'Recipe',
            'Quest',
            'Level',
        ];
    }

    //
    // Removes blank entries from db
    //
    public function removenulls()
    {
        $dbs = $this->getModule('database');

        // not really a null but here is fine
        $dbs->sql("update xiv_items set icon_lodestone = '', icon_lodestone_hq = '' where id IN (20,21,22,23,24,25,26,27,28,29,30,31,32);");

        // special
        $dbs->sql("DELETE FROM xiv_items_to_baseparam WHERE item = 0");
        $dbs->sql("DELETE FROM xiv_gardening_seed WHERE item = 0");
        $dbs->sql("DELETE FROM xiv_gathering_exp WHERE exp = 0");
        $dbs->sql("DELETE FROM xiv_gathering_item WHERE item = 0");
        $dbs->sql("DELETE FROM xiv_gathering_point WHERE gathering_point_base = 0");
        $dbs->sql("DELETE FROM xiv_gathering_point_base WHERE gathering_type = 0");
        $dbs->sql("DELETE FROM xiv_gathering_point_bonus WHERE `condition` = 0");
        $dbs->sql("DELETE FROM xiv_gc_shop WHERE grand_company = 0");
        $dbs->sql("DELETE FROM xiv_gc_supply_duty WHERE item_1_1 = 0");
        $dbs->sql("DELETE FROM xiv_gc_seal_shop_item WHERE item = 0");
        $dbs->sql("DELETE FROM xiv_gc_rank WHERE tier = 0");
        $dbs->sql("DELETE FROM xiv_housing_furniture WHERE model_key = 0");
        $dbs->sql("DELETE FROM xiv_housing_yard_object WHERE model_key = 0");
        $dbs->sql("DELETE FROM xiv_materia WHERE `item_1` = 0");
        $dbs->sql("DELETE FROM xiv_masterpiece_supply_duty_repeater WHERE `required_item` = 0");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_item WHERE `item` = 0");
        $dbs->sql("DELETE FROM xiv_retainer_task WHERE `classjob_category` = 0");
        $dbs->sql("DELETE FROM xiv_relic3 WHERE `item` = 0");
        $dbs->sql("DELETE FROM xiv_relic WHERE `item_atma` = 0");
        $dbs->sql("DELETE FROM xiv_relic_notes WHERE `event_item` = 0");
        $dbs->sql("DELETE FROM xiv_relic_notes_repeater WHERE `monster_note_target` = 0");
        $dbs->sql("DELETE FROM xiv_retainer_task_normal WHERE `item` = 0");
        $dbs->sql("DELETE FROM xiv_weather_rate_repeater WHERE `weather` = 0");
        $dbs->sql("DELETE FROM xiv_triple_triad_card_resident WHERE `id` = 0");
        $dbs->sql("DELETE FROM xiv_actions_proc_status WHERE `status` = 0");
        $dbs->sql("DELETE FROM xiv_airship_exploration_part WHERE `rank` = 0");
        $dbs->sql("DELETE FROM xiv_cabinet WHERE `item` = 0");
        $dbs->sql("DELETE FROM xiv_shops_item WHERE `item` = 0");
        $dbs->sql("DELETE FROM xiv_placenames_maps WHERE folder = '' or folder is NULL");
        $dbs->sql("DELETE FROM xiv_tomestones WHERE `item` = 0");

        // extra special
        $dbs->sql("DELETE FROM xiv_npc WHERE name_en = ''");
        $dbs->sql("DELETE FROM xiv_npc WHERE name_en = '???'");

        // name related
        $dbs->sql("DELETE FROM xiv_achievements WHERE ((name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_achievements_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_achievements_kind WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_actions WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_actions_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_actions_general WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_addons WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_adventures WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_aetherytes WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_beast_tribe WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_classjobs WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_classjobs_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_companions WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_companions_move WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_company_action WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_company_craft_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_complete_journal WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_completion WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_config_key WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_contents_description WHERE (help_en = '' or help_en is NULL) AND (help_cns = '' or help_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_contents_note WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_contents_roulette WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_contents_talk WHERE (text_en = '' or text_en is NULL) AND (text_cns = '' or text_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_contents_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_craft_action WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_craft_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_credit_cast WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_custom_talk WHERE (text_en = '' or text_en is NULL) AND (text_cns = '' or text_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_emotes WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_emotes_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_errors WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_event_actions WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_event_items WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fates WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fates_climbing WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fates_event WHERE (text1_en = '' or text1_en is NULL) AND (text1_cns = '' or text1_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fates_stelth WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fcc_shop WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_activity WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_activity_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_authority WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_authority_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_chest_name WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_hierarchy WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_profile WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fc_rights WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_field_marker WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_field_marker_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fishing_spot_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_fish_parameters WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering_condition WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering_point_bonus_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering_point_name WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering_sub_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gathering_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_gridania_female_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_gridania_male_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_limsa_female_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_limsa_male_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_uldah_female_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_rank_uldah_male_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_shop_item_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_title_gridania WHERE (name_en_male = '' or name_en_male is NULL) AND (name_cns_male = '' or name_cns_male is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_title_limsa WHERE (name_en_male = '' or name_en_male is NULL) AND (name_cns_male = '' or name_cns_male is NULL)");
        $dbs->sql("DELETE FROM xiv_gc_title_uldah WHERE (name_en_male = '' or name_en_male is NULL) AND (name_cns_male = '' or name_cns_male is NULL)");
        $dbs->sql("DELETE FROM xiv_gimmick_bill WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gimmick_yesno WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gold_saucer_arcade_machine WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gold_saucer_talk WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_gold_saucer_text_data WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_guardian WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_guildleve_evaluation WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_guild_orders WHERE (text1_en = '' or text1_en is NULL) AND (text1_cns = '' or text1_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_housing_item_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_housing_preset WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_housing_mate_authority WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_housing_placement WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_how_to WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_how_to_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_how_to_page WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_hunting_log WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_instances WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_instances_text_data WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_search_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_series WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_special_bonus WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_ui_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_ui_kind WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_items_ui_slot WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_journal_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_journal_genre WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_legacy_quest WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_leves WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_leves_assignment_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_leves_client WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_leves_string WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_live_condition WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_live_condition_sign WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_loading_tips WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_loading_tips_sub WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_lobbys WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_log_filter WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_log_kind WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_log_kind_category_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_log_message WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_main_commands WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_main_commands_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_markers WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_mounts WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_movie_subtitles WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_movie_subtitles_voyage WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_notebook_division WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_npc WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_npc_enemy WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_npc_yells WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_objs WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_online_status WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_orchestrion WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_orchestrion_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_pets WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_pets_action WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_placenames WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_public_content WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_public_content_text_data WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_party_content WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_pvp_ranks WHERE (storm_en = '' or storm_en is NULL) AND (storm_cns = '' or storm_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_quests WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_quests_reward_other WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_quests_to_text WHERE (text_en = '' or text_en is NULL) AND (text_cns = '' or text_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_quests_webex WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_quests_webtypes WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_name WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_name_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_name_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_param WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_racing_chocobo_param_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_rc_name_category_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_recipes WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_recipes_elements WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_relic6_magicite WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_relic6_magicite_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_relic_note_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_relic_note_category_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_retainer_task_random WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_retainer_task_random_text WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_roles WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        //$dbs->sql("DELETE FROM xiv_shops WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_special_shops WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_special_shops_item_category WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_stains WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_status WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_titles WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_text_commands WHERE (help_en = '' or help_en is NULL) AND (help_cns = '' or help_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_text_commands_param WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_towns WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_traits WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_treasures WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_tribes WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_triple_triad_card WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_triple_triad_card_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_triple_triad_competition WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_triple_triad_rule WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_weather WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_races WHERE (name_en_male = '' or name_en_male is NULL) AND (name_cns_male = '' or name_cns_male is NULL)");
        $dbs->sql("DELETE FROM xiv_companions_race WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_companions_rules WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_companions_stage WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
        $dbs->sql("DELETE FROM xiv_companions_skill_type WHERE (name_en = '' or name_en is NULL) AND (name_cns = '' or name_cns is NULL)");
    }
}
