<?php

/**
 * GatheringNotebookList
 *
 * @version 1.0
 * @author
 */

namespace XIVDB\Apps\GameData\ExtractClasses;

class GatheringNotebookList extends \XIVDB\Apps\GameData\GameData
{
    const TABLE = 'xiv_gathering_notebook_list';

    public function autoOffsets()
    {
        for ($i=2; $i < 102; $i++) {
            $this->set[$i] = sprintf('item_%s', $i - 1);
        }
    }

    protected $set = [];
}
