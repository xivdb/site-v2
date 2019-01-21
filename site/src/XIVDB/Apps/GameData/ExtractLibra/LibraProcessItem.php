<?php

/**
 * LibraProcessItem
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractLibra;

use XIVDB\Apps\GameData\ExtractLibra\CoreLibra;

class LibraProcessItem extends CoreLibra
{
    protected function handle()
    {
        ob_end_flush();

        $table = 'xiv_items';

        $insert = [];
        $basestats = [];
        $bonus = [];
        $classjob = [];
        $instance = [];

        foreach($this->data as $i => $d)
        {
            $id = $d['Key'];
            $json = json_decode($d['data'], true);
            $lodestone = explode('/', $d['path']);

            $new = [
                /*'help_ja' => $d['Help_ja'],
                'help_en' => $d['Help_en'],
                'help_fr' => $d['Help_fr'],
                'help_de' => $d['Help_de'],
                'help_cns' => null,

                'level_equip'         => $d['EquipLevel'],
                'level_item'          => $d['Level'],
                'rarity'              => $d['Rarity'],
                'hq'                  => $d['HQ'],
                'item_special_bonus'    => $d['SpecialBonus'],
                'item_series'           => $d['item_series'],
                'slot'                => $d['Slot'],
                'category'         => $d['UICategory'],
                'price'               => $d['Price'],
                'price_min'           => $d['PriceMin'],
                'price_sell'          => isset($json['sell_price']) ? $json['sell_price'] : 0,
                'item_glamour'      => $d['MirageItem'],
                'icon_lodestone'      => explode('.', $d['icon'])[0],
                'icon_lodestone_hq'   => explode('.', $d['icon_hq'])[0],
                'salvage'             => $d['Salvage'],
                'materia_slot_count'       => isset($json['MateriaSocket']) ? $json['MateriaSocket'] : 0,
                'materialize_type'    => isset($json['MaterializeType']) ? $json['MaterializeType'] : null,
                'sort'             => $d['SortId']
,               'is_unique'           => isset($json['OnlyOne']) ? $json['OnlyOne'] : 0,
                'is_untradable'       => isset($json['DisablePassedOthers']) ? $json['DisablePassedOthers'] : 0,
                'is_legacy'           => $d['legacy'],
                'is_crest_worthy'        => isset($json['Crest']) ? $json['Crest'] : 0,
                'classjob_repair'     => isset($json['Repair']) ? $json['Repair'] : null,
                'repair_item'      => isset($json['RepairItem']) ? $json['RepairItem'] : null,
                'repair_cost'         => isset($json['repair_price']) ? $json['repair_price'] : 0,
                */

                'lodestone_type'   => $lodestone[0],
                'lodestone_id'        => $lodestone[1],
            ];

            $new = array_merge($new, $this->names($d, 'UIName'), ['id' => $id, 'patch' => $this->patch ]);
            $insert[] = $new;

            if (count($insert) == 500)
            {
                $this->insert($table, $insert);
                $insert = [];
            }

            /*$basestat = [
                'damage' => $d['Damage'],
                'damage_hq' => $d['Damage_hq'],
                'magic_damage' => $d['MagicDamage'],
                'magic_damage_hq' => $d['MagicDamage_hq'],
                'defense' => $d['Defense'],
                'defense_hq' => $d['Defense_hq'],
                'magic_defense' => $d['MagicDefense'],
                'magic_defense_hq' => $d['MagicDefense_hq'],
                'shield_rate' => $d['ShieldRate'],
                'shield_rate_hq' => $d['ShieldRate_hq'],
                'shield_block_rate' => $d['ShieldBlockRate'],
                'shield_block_rate_hq' => $d['ShieldBlockRate_hq'],
                'attack_speed' => $d['AttackInterval'],
                'attack_speed_hq' => $d['AttackInterval_hq'],
                'auto_attack' => $d['AutoAttack'],
                'auto_attack_hq' => $d['AutoAttack_hq'],
                'dps' => ($d['Damage'] > 0) ? round($d['Damage'] / 3, 2) : 0,
                'dps_hq' => ($d['Damage_hq'] > 0) ? round($d['Damage_hq'] / 3, 2) : 0,
            ];

            // only add basestats if it actually has some
            if (array_sum($basestat) > 0)
            {
                $basestat['id'] = $id;
                $basestat['patch'] = $this->patch;

                $basestats[] = $basestat;

                if (count($basestats) == 500)
                {
                    $this->insert('xiv_items_base_stats', $basestats);
                    $basestats = [];
                }
            }

            // classjobs
            if (isset($json['classjob']))
            {
                foreach($json['classjob'] as $i => $cjId)
                {
                    $classjob[] = [
                        'item' => $id,
                        'classjob' => $cjId,
                        'patch' => $this->patch,
                    ];

                    if (count($classjob) == 500)
                    {
                        $this->insert('xiv_items_to_classjob', $classjob);
                        $classjob = [];
                    }
                }
            }
            */
            // instances
            if (isset($json['instance_content']))
            {
                foreach($json['instance_content'] as $i => $instanceId)
                {
                    $instance[] = [
                        'item' => $id,
                        'instance' => $instanceId,
                        'patch' => $this->patch,
                    ];

                    if (count($instance) == 500)
                    {
                        $this->insert('xiv_items_to_instance', $instance);
                        $instance = [];
                    }
                }
            }

            /*
            // bonus - the item attributes/stats
            if (isset($json['bonus']))
            {
                foreach($json['bonus'] as $i => $bonusData)
                {
                    reset($bonusData);

                    $baseparamId = key($bonusData);

                    $bonus[] = [
                        'item' => $id,
                        'baseparam' => $baseparamId,
                        'value' => reset($bonusData),
                        'value_hq' => isset($json['bonus_hq'][$i][$baseparamId]) ? $json['bonus_hq'][$i][$baseparamId] : 0,
                        'patch' => $this->patch,
                    ];

                    if (count($bonus) == 500)
                    {
                        $this->insert('xiv_items_to_baseparam', $bonus);
                        $bonus = [];
                    }
                }
            }
            */
        }

        flush();

        $this->insert($table, $insert);
        //$this->insert('xiv_items_base_stats', $basestats);
        //$this->insert('xiv_items_to_baseparam', $bonus);
        //$this->insert('xiv_items_to_classjob', $classjob);
        $this->insert('xiv_items_to_instance', $instance);

        return true;
    }
}
