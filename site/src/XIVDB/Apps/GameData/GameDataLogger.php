<?php

namespace XIVDB\Apps\GameData;

//
// Handle logging
//
trait GameDataLogger
{
    public $log = [];

    public function log($text)
    {
        $this->log[] = $text;
    }

    public function error($text)
    {
        $this->log($text);
        show('ERROR');
        show($this->log);
        die;
    }
}
