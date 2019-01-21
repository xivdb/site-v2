<?php
/**
 * SearchDatabase
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\SearchMenus;

trait SearchDatabase
{
    use SearchDatabaseEquipment;
    use SearchDatabaseItems;
    use SearchDatabaseHnL;
    use SearchDatabaseActions;
    use SearchDatabaseActivities;
    use SearchDatabaseAchievements;
    use SearchDatabaseOther;

    //
    // Get the database menu
    //
    public function getDatabaseMenu()
    {
        $arr = [
            'all' => $this->getAll(),
            'equipment' => $this->getEquipment(),
            'items' => $this->getItems(),
            'hnl' => $this->getHnL(),
            'actions' => $this->getActions(),
            'activities' => $this->getActivities(),
            'achievements' => $this->getAchievements(),
            'other' => $this->getOther(),
        ];

        #die(show($arr));
        return $arr;
    }
}
