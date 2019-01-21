<?php

/**
 * InstanceContentType
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class InstanceContentType extends \XIVDB\Apps\GameData\GameData 
{
    const TABLE = 'xiv_instances_type';

    protected $real =
    [
        2 => 'icon',
        3 => 'icon_duty_finder',
    ];
}
