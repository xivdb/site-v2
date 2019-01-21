<?php

/**
 * InstanceContent
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class InstanceContent extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_instances';

    protected $real =
    [
        1 => 'instance_content_type',
        3 => 'time_limit',

        10 => 'territory_id',

        16 => 'instance_content_text_data_boss_start',
        17 => 'instance_content_text_data_boss_end',
        18 => 'boss',
        19 => 'instance_content_text_data_objective_start',
        20 => 'instance_content_text_data_objective_end',

        25 => 'currency_a_bonus',
        26 => 'currency_b_bonus',
        27 => 'currency_c_bonus',
    ];

    protected $currency =
    [
        29 => 'a',
        30 => 'b',
        31 => 'c',
        // 32
        // 33
        // 34
        // 35
        // 36
        37 => 'a',
        38 => 'a',
        39 => 'a',
        40 => 'a',
        41 => 'a',

        42 => 'b',
        43 => 'b',
        44 => 'b',
        45 => 'b',
        46 => 'b',

        47 => 'c',
        48 => 'c',
        49 => 'c',
        50 => 'c',
        51 => 'c',
    ];

    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),
        ];
    }

    protected function manual()
    {
        $this->setPlacename();
        $this->setCurrency();
        $this->setIcon();
    }

    /**
     * Set the instance placename
     */
    private function setPlacename()
    {
        $territoryKey = array_flip($this->real)['territory_id'];
        $territoryCsv = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'TerritoryType.csv');

        // territory offsets
        $territoryOffsets = [
            4 => 'region',
            5 => 'zone',
            6 => 'placename',
            7 => 'map',
        ];

        // loop through CSV data
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $territoryId = $line[$territoryKey];
            $territoryLine = isset($territoryCsv[$territoryId]) ? $territoryCsv[$territoryId] : false;

            // if we have a line, process it
            if ($territoryLine)
            {
                $temp = [
                    'id' => $id,
                    'patch' => $this->patch,
                ];

                // loop through offsets to populate temp
                foreach($territoryOffsets as $offset => $column) {
                    $temp[$column] = $territoryLine[$offset];
                }

                $insert[] = $temp;
            }
        }

        $this->insert(self::TABLE, $insert);
    }

    /**
     * Set the instance currency
     */
    private function setCurrency()
    {
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $temp = [
                'id' => $id,
                'patch' => $this->patch,
            ];

            // loop through currency and add it up
            foreach($this->currency as $offset => $label) {
                $label = 'currency_'. $label;

                if (!isset($temp[$label])) {
                    $temp[$label] = 0;
                }

                $temp[$label] = $temp[$label] + $line[$offset];
            }

            $insert[] = $temp;
        }

        $this->insert(self::TABLE, $insert);
    }

    /**
     * Get icon and id
     * Instance Content > InstanceContentType > ContentType
     */
    private function setIcon()
    {
        return;
        
        $InstanceContentType = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'InstanceContentType.csv');
        $ContentType = $this->getCsv(ROOT_EXTRACTS . EXTRACT_PATH . SAINT_EXD . 'ContentType.csv');

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            // grab instance content type
            $ict = $InstanceContentType[$line[1]];
            
            print_r($ict);
            die;

            // grab content type
            $ctOffset = $ict[5];
            $ct = $ContentType[$ctOffset];

            $insert[] = [
                'id' => $id,
                'icon' => $ct[2],
                'content_type' => $ct[0],
                'patch' => $this->patch,
            ];
        }

        $this->insert(self::TABLE, $insert);
    }
}
