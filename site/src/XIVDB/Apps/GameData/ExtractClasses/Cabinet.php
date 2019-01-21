<?php

/**
 * Cabinet
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class Cabinet extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_cabinet';

    protected $real =
    [
        1 => 'item',
        2 => 'order',
        3 => 'category',
    ];
}
