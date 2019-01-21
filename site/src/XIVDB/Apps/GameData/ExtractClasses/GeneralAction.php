<?php

/**
 * GeneralAction
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GeneralAction extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_actions_general';

    protected $real =
    [
        7 => 'icon',
    ];

    protected function json($line)
    {
        foreach($line['help'] as $lang => $help)
        {
            if (is_array($help))
            {
                $this->simpleHelp($help);
                $line['help'][$lang] = implode(' ', $this->help);
                $line['json'][$lang] = json_encode($help);
            }
            else
            {
                $line['help'][$lang] = $help;
                $line['json'][$lang] = json_encode([]);
            }

            $this->help = [];
        }

        return
        [
            'name_ja' => $line['name']['ja'],
            'name_en' => ucwords($line['name']['en']),
            'name_fr' => ucwords($line['name']['fr']),
            'name_de' => ucwords($line['name']['de']),


            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),
            'help_cns' => null,

            'json_ja' => $line['json']['ja'],
            'json_en' => $line['json']['en'],
            'json_fr' => $line['json']['fr'],
            'json_de' => $line['json']['de'],
            'json_cns' => null,
        ];
    }

    protected function simpleHelp($desc)
    {
        if (is_array($desc))
        {
            foreach($desc as $i => $line)
            {
                if (is_array($line))
                {
                    $yes = $line['yes'];
                    $this->simpleHelp($yes);
                }
                else
                {
                    $this->help[] = trim($line);
                }
            }
        }
        else
        {
            $this->help[] = trim($desc);
        }
    }
}
