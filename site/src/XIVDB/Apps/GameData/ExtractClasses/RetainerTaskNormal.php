<?php

/**
 * RetainerTaskNormal
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class RetainerTaskNormal extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_retainer_task_normal';

    protected $real =
    [
        1 => 'item',
        2 => 'quantity_1',
        3 => 'quantity_2',
        4 => 'quantity_3',
    ];
}
