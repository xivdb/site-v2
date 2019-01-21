<?php

namespace XIVDB\Apps\GameSetup;

Trait ConnectionsLinksTrait
{
	// Structure:
	//
	// Type
	// description
	// - (left) table, link to (left join), save as (the content id to save)
	// - (right) table, link to (left join), save as  (the content id to save)
	//
	private $connections =
	[
		// items 0-10
		[
			'instance',
			'Item found in a dungeon',
			['xiv_items', 'id', 'id'],
			['xiv_instances_to_items', 'item', 'instance'],
		],[
			'instance_chest',
			'Item found in a dungeon chest',
			['xiv_items', 'id', 'id'],
			['xiv_instances_chests_to_items', 'item', 'instance'],
		],[
			'instance_reward',
			'Item obtained as a instance reward',
			['xiv_items', 'id', 'id'],
			['xiv_instances_to_rewards', 'item', 'instance'],
		],[
			'quest_reward',
			'Item obtained as a chest reward',
			['xiv_items', 'id', 'id'],
			['xiv_quests_to_rewards', 'item', 'quest'],
		],[
			'enemy_drop',
			'Item dropped from an enemy',
			['xiv_items', 'id', 'id'],
			['xiv_npc_enemy_to_items', 'item', 'enemy'],
		],[
			'recipe',
			'Item used in a recipe',
			['xiv_items', 'id', 'id'],
			['xiv_recipes_to_item', 'item', 'recipe'],
		],[
			'craftable',
			'Item is craftable',
			['xiv_items', 'id', 'id'],
			['xiv_recipes', 'item', 'id'],
		],[
			'gathering',
			'Item obtained from gathering',
			['xiv_items', 'id', 'id'],
			['xiv_gathering', 'item', 'id'],
		],[
			'achievement',
			'Item rewarded from achievements',
			['xiv_items', 'id', 'id'],
			['xiv_achievements', 'item', 'id'],
		],[
			'shop',
			'Item purchsable from a shop',
			['xiv_items', 'id', 'id'],
			['xiv_shops_to_item', 'item', 'shop'],


		],[
			'specialshop_receive_1',
			'Item received from a special shop (1)',
			['xiv_items', 'id', 'id'],
			['xiv_special_shops_to_item', 'receive_item_1', 'special_shop'],
		],[
			'specialshop_receive_2',
			'Item received from a special shop (2)',
			['xiv_items', 'id', 'id'],
			['xiv_special_shops_to_item', 'receive_item_2', 'special_shop'],
		],[
			'specialshop_cost_1',
			'Item is a cost in a special shop (1)',
			['xiv_items', 'id', 'id'],
			['xiv_special_shops_to_item', 'cost_item_1', 'special_shop'],
		],[
			'specialshop_cost_2',
			'Item is a cost in a special shop (2)',
			['xiv_items', 'id', 'id'],
			['xiv_special_shops_to_item', 'cost_item_2', 'special_shop'],
		],[
			'specialshop_cost_3',
			'Item is a cost in a special shop (3)',
			['xiv_items', 'id', 'id'],
			['xiv_special_shops_to_item', 'cost_item_3', 'special_shop'],


		// achievements 10-12
		],[
			'post',
			'Post achievements',
			['xiv_achievements', 'id', 'id'],
			['xiv_achievements_post', 'achievement', 'post_achievement'],
		],[
			'pre',
			'Previous required achievements',
			['xiv_achievements', 'id', 'id'],
			['xiv_achievements_pre', 'achievement', 'pre_achievement'],
		],[
			'quest',
			'Required quests to complete',
			['xiv_achievements', 'id', 'id'],
			['xiv_achievements_pre_quests', 'achievement', 'quest'],

		// instances 13-19
		],[
			'chests',
			'Items found in instance chest',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_chests_to_items', 'instance', 'item'],
		],[
			'bosses',
			'Bosses found in instance',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_to_bosses', 'instance', 'npc_enemy'],
		],[
			'currency',
			'Currency found in instance',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_to_currency', 'instance', 'item'],
		],[
			'enemy',
			'Enemy found in instance',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_to_enemy', 'instance', 'enemy'],
		],[
			'items',
			'Item found in instance',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_to_items', 'instance', 'item'],
		],[
			'rewards',
			'Item rewarded from instance',
			['xiv_instances', 'id', 'id'],
			['xiv_instances_to_rewards', 'instance', 'item'],

		// npc 20-24
		],[
			'area',
			'NPC found in area',
			['xiv_npc', 'id', 'id'],
			['xiv_npc_to_area', 'npc', 'placename'],
		],[
			'quest',
			'NPC involved in quest',
			['xiv_npc', 'id', 'id'],
			['xiv_npc_to_quest', 'npc', 'quest'],
		],[
			'region',
			'NPC found in region',
			['xiv_npc', 'id', 'id'],
			['xiv_npc_to_region', 'npc', 'region'],
		],[
			'shop',
			'NPC tied to a shop',
			['xiv_npc', 'id', 'id'],
			['xiv_npc_to_shop', 'npc', 'shop'],

		// enemy 25-29
		],[
			'nonpop',
			'Enemy non pop in an area',
			['xiv_npc_enemy', 'id', 'id'],
			['xiv_npc_enemy_nonpop', 'enemy', 'placename'],
		],[
			'area',
			'Enemy found in area',
			['xiv_npc_enemy', 'id', 'id'],
			['xiv_npc_enemy_to_area', 'enemy', 'placename'],
		],[
			'instance',
			'Enemy found in instance',
			['xiv_npc_enemy', 'id', 'id'],
			['xiv_npc_enemy_to_instance', 'enemy', 'instance'],
		],[
			'items',
			'Item dropped from enemy',
			['xiv_npc_enemy', 'id', 'id'],
			['xiv_npc_enemy_to_items', 'enemy', 'item'],


		// quests 30-36
		],[
			'classjob',
			'Class Jobs tied to quest',
			['xiv_quests', 'id', 'id'],
			['xiv_quests_to_classjob', 'quest', 'classjob'],
		],[
			'classjob_category',
			'Class Job category tied to quests',
			['xiv_quests', 'id', 'id'],
			['xiv_quests_to_classjob_category', 'quest', 'classjob_category'],
		],[
			'npc',
			'NPC tied to quest',
			['xiv_quests', 'id', 'id'],
			['xiv_npc_to_quest', 'quest', 'npc'],
		],[
			'post',
			'Post quests tied to a quest',
			['xiv_quests', 'id', 'id'],
			['xiv_quests_to_post_quest', 'quest', 'quest_post'],
		],[
			'pre',
			'Previous quests tied to a quest',
			['xiv_quests', 'id', 'id'],
			['xiv_quests_to_pre_quest', 'quest', 'quest_pre'],
		],[
			'reward',
			'Item rewards tied to a quest',
			['xiv_quests', 'id', 'id'],
			['xiv_quests_to_rewards', 'quest', 'item'],
		]
	];
}
