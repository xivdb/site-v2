<?php

/**
 * GuildOrder
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GuildOrder extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_guild_orders';

    protected function json($line)
    {
        return
        [
            'text1_ja' => $line['text1']['ja'],
            'text1_en' => ucwords($line['text1']['en']),
            'text1_fr' => ucwords($line['text1']['fr']),
            'text1_de' => ucwords($line['text1']['de']),

            'text2_ja' => $line['text2']['ja'],
            'text2_en' => ucwords($line['text2']['en']),
            'text2_fr' => ucwords($line['text2']['fr']),
            'text2_de' => ucwords($line['text2']['de']),

            'text3_ja' => $line['text3']['ja'],
            'text3_en' => ucwords($line['text3']['en']),
            'text3_fr' => ucwords($line['text3']['fr']),
            'text3_de' => ucwords($line['text3']['de']),

            'text4_ja' => $line['text4']['ja'],
            'text4_en' => ucwords($line['text4']['en']),
            'text4_fr' => ucwords($line['text4']['fr']),
            'text4_de' => ucwords($line['text4']['de']),
        ];
    }
}
