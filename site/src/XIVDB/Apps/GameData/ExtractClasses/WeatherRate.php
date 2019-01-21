<?php

/**
 * WeatherRate
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class WeatherRate extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_weather_rate';

    protected $repeater =
    [
        'start' => 1,
        'finish' => 16,

        'columns' => [
            'weather',
            'rate'
        ],
    ];
}
