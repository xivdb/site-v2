<?php

/**
 * TextCommand
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class TextCommand extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_text_commands';

    protected function json($line)
    {
        return
        [
            'help_ja' => $line['help']['ja'],
            'help_en' => ($line['help']['en']),
            'help_fr' => ($line['help']['fr']),
            'help_de' => ($line['help']['de']),

            'command1_ja' => $line['command1']['ja'],
            'command1_en' => ucwords($line['command1']['en']),
            'command1_fr' => ucwords($line['command1']['fr']),
            'command1_de' => ucwords($line['command1']['de']),

            'command2_ja' => $line['command2']['ja'],
            'command2_en' => ucwords($line['command2']['en']),
            'command2_fr' => ucwords($line['command2']['fr']),
            'command2_de' => ucwords($line['command2']['de']),

            'command3_ja' => $line['command3']['ja'],
            'command3_en' => ucwords($line['command3']['en']),
            'command3_fr' => ucwords($line['command3']['fr']),
            'command3_de' => ucwords($line['command3']['de']),

            'command4_ja' => $line['command4']['ja'],
            'command4_en' => ucwords($line['command4']['en']),
            'command4_fr' => ucwords($line['command4']['fr']),
            'command4_de' => ucwords($line['command4']['de']),
        ];
    }
}
