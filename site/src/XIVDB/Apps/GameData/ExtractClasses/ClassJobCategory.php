<?php

/**
 * ClassJobCategory
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ClassJobCategory extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_classjobs_category';

    private $list = [];

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
        $this->classjobList();
        $this->classjobListConnections();
    }

    protected function classjobList()
    {
        // split all lines
        $insert = [];
        $csv = $this->getCsvFileData();

        foreach($csv as $id => $line)
        {
            if (!$line || count($line) < 2) continue;

            // remove first two entries, they're ID and Name
            unset($line[0]);
            unset($line[1]);

            // line is now a list of class job restrictions, the order
            // is the id + 1
            $line = array_values(array_filter($line));

            // map restrictions
            $restrictions = [];
            foreach($line as $cj => $boolean)
            {
                $allowed = (trim(strtolower($boolean)) == 'true') ? 1 : 0;
                $restrictions[$cj] = $allowed;
            }

            $this->list[$id] = $restrictions;

            // return restrictions
            $insert[$id] =
            [
                'id' => $id,
                'patch' => $this->patch,
                'classjob_list' => json_encode($restrictions),
            ];
        }

        $this->insert(self::TABLE, $insert);
    }

    protected function classjobListConnections()
    {
        $insert = [];
        foreach($this->list as $classjob_category => $catagories)
        {
            foreach($catagories as $classjob => $bool)
            {
                if (!$bool) continue;

                $insert[] =
                [
                    'classjob' => $classjob,
                    'classjob_category' => $classjob_category,
                    'patch' => $this->patch,
                ];
            }
        }

        $this->insert('xiv_classjobs_to_category', $insert);
    }
}
