<?php

/**
 * ModelChara
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class ModelChara extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_model_chara';

    protected $exmapped =
    [
        1 => 'type',
        2 => 'model',
        3 => 'base',
        4 => 'variant',
    ];
}
