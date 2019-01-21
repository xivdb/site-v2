<?php

/**
 * ContentFinderCondition
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ContentFinderCondition extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_instances';

    protected $special =
    [
        0 => 'id',
        //5 => 'content_roulette',
        16 => 'level',
        17 => 'level_sync',
        18 => 'item_level',
        19 => 'item_level_sync',
        38 => 'banner',
    ];

    //
    // Offsets for ContentMemberType
    //
    protected $contentMemberTypeOffsets =
    [
        5 => 'alliance',
        6 => 'players_per_party',
        7 => 'party_count',
        10 => 'tanks_per_party',
        11 => 'healers_per_party',
        12 => 'melees_per_party',
        13 => 'ranged_per_party',
        //11 => 'force_party_setup',
        //12 => 'free_role',
    ];

    protected function manual()
    {
        $this->addContentData();
        $this->addContentMemberTypes();
        $this->addDescriptions();
    }

    private function addContentData()
    {
        // loop through csv
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            if ($line[4] != '1') {
                continue;
            }

            $data = [];
            foreach($this->special as $offset => $column) {
                $data[$column] = $line[$offset];
            }

            $insert[] = $data;
        }

           $this->insert('xiv_instances', $insert);
    }

    private function addContentMemberTypes()
    {
        $ContentMemberType = $this->getSaintFilename('ContentMemberType');
        $ContentMemberType = $this->getCsv($ContentMemberType);

        // loop through csv
        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $memberType = $ContentMemberType[$line[8]];
			$instanceId = $line[4];

            $data = [ 'id' => $instanceId ];
            foreach($this->contentMemberTypeOffsets as $offset => $column) {
                $data[$column] = $memberType[$offset];
            }

            $insert[] = $data;
        }

        $this->insert('xiv_instances', $insert);
    }

    /**
     * Add instance descriptions
     */
    private function addDescriptions()
    {
        // get descriptions
        $descriptions = $this->getJson($this->getJsonFilename('ContentFinderConditionTransient'));

        $insert = [];
        foreach($this->getCsvFileData() as $id => $line)
        {
            $instanceId = $line[4];
            $desc = $descriptions[$id]['help'];
            if (!$desc['en']) {
                continue;
            }

            $insert[] = [
                'id' => $instanceId,
                'help_ja' => $desc['ja'],
                'help_en' => $desc['en'],
                'help_de' => $desc['de'],
                'help_fr' => $desc['fr'],
            ];
        }

        $this->insert('xiv_instances', $insert);
    }
}
