<?php

/**
 * ENpcResident
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ENpcResident extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_npc';

    protected $real =
    [
        10 => 'map',
    ];

    protected function manual()
    {
        return;
        
        $csv = $this->getCsv(ROOT_EXTRACTS .'/misc/devnpcs.csv');
        $list = [];

        foreach ($csv as $npc) {
            $list[] = $npc[0];
        }

        $this->log(sprintf('%s Dev NPCs', count($list)));

        $where = sprintf('id IN (%s)', implode(',', $list));

        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
            ->update('xiv_npc')
            ->set('is_dev', '1')
            ->where($where);

        $dbs->execute();
    }


    protected function json($line)
    {
        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),

            'name_plural_ja' => $line['name_plural']['ja'],
            'name_plural_en' => ucwords($line['name_plural']['en']),
            'name_plural_fr' => ucwords($line['name_plural']['fr']),
            'name_plural_de' => ucwords($line['name_plural']['de']),

            'title_ja' => $line['name_title']['ja'],
            'title_en' => ucwords($line['name_title']['en']),
            'title_fr' => ucwords($line['name_title']['fr']),
            'title_de' => ucwords($line['name_title']['de']),
        ];
    }
}
