<?php

/**
 * Action
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ActionTransient extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_actions';

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
            'help_ja' => $line['help']['ja'],
            'help_en' => $line['help']['en'],
            'help_fr' => $line['help']['fr'],
            'help_de' => $line['help']['de'],

            'json_ja' => $line['json']['ja'],
            'json_en' => $line['json']['en'],
            'json_fr' => $line['json']['fr'],
            'json_de' => $line['json']['de'],
        ];
    }

    //
    // Generate a simple help description
    //
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

    //
    // Recurrsive help description
    //
    protected function recurrsiveHelp($desc, $tabs = "")
    {
        if (is_array($desc))
        {
            $tabs = $tabs . "\t";

            foreach($desc as $i => $line)
            {
                if (is_array($line))
                {
                    // get condition
                    $c = $line['condition'];
                    $this->help[] = $tabs . '(if '. implode(' ', $line['condition']) .')';

                    // get yes and no
                    $yes = $line['yes'];
                    $no = $line['no'];

                    $this->help[] = $tabs . 'YES = ';
                    $this->recurrsiveHelp($yes, $tabs);

                    $this->help[] = $tabs . 'NO = ';
                    $this->recurrsiveHelp($no, $tabs);
                }
                else
                {
                    $this->help[] = $tabs . trim($line);
                }
            }
        }
        else
        {
            $this->help[] = $tabs . trim($desc);
        }
    }
}
