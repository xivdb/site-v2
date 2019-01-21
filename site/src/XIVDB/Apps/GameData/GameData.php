<?php

namespace XIVDB\Apps\GameData;

use Symfony\Component\HttpFoundation\Request;

class GameData extends \XIVDB\Apps\AppHandler
{
    use GameDataTables;
    use GameDataStorage;
    use GameDataFilenames;
    use GameDataProcess;
    use GameDatabase;
    use GameDataLogger;
    use GameDataMisc;
    use GamePatches;

    public $id;
    public $name;
    public $timestamp;
    public $patch;

    function __construct()
    {
        $this->timestamp = date("Y-m-d H:i:s");

        // patch
		$this->patch = $this->getLatestPatch()['patch'];

        // auto offset class exist?
        if (method_exists($this, 'autoOffsets')) {
            $this->autoOffsets();
        }
    }

    //
    // Set the ID
    //
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    //
    // Set content name
    //
    public function setName($name)
    {
        // special case as "Trait" is a keyword
        if ($name == 'Traits') {
            $name = 'Trait';
        }

        $this->name = $name;
        return $this;
    }

    //
    // Get extract class
    //
    public function getGameExtractClass($name)
    {
        // make sure file exists
        $file = __DIR__ .'/ExtractClasses/'. $name .'.php';
        if (!file_exists($file)) {
            die('No Extract Class File: '. $file);
        }

        // get class and call it
        $class = '\\XIVDB\\Apps\\GameData\\ExtractClasses\\' . $name;
        $class = new $class();

        // check class exists
        if (!$class) {
            die('No class for: '. $name);
        }

        return $class;
    }

    //
    // Get offsets
    //
    public function getOffsets()
    {
        return property_exists($this, 'real') ? $this->real : [];
    }

    //
    // Get set offsets
    //
    public function getSetOffsets()
    {
        return property_exists($this, 'set') ? $this->set : [];
    }

    //
    // Get set offsets
    //
    public function getGroupOffsets()
    {
        return property_exists($this, 'group') ? $this->group : [];
    }

    //
    // Get set offsets
    //
    public function getRepeaterOffsets()
    {
        return property_exists($this, 'repeater') ? $this->repeater : [];
    }

    //
    // Get filenames (mainly for frontend)
    //
    public function getFilenames()
    {
        return [
            'csv' => $this->getSaintFilename(),
            'csv_prev' => str_ireplace(CURRENT_PATCH, PREVIOUS_PATCH, $this->getSaintFilename()),
            'json' => $this->getJsonFilename(),
        ];
    }

    //
    // Get an offset
    //
    public function getOffset($name)
    {
        return array_flip($this->real)[$name];
    }

    //
    // Get class table
    //
    public function getTable()
    {
        return static::TABLE;
    }

    //
    // Number of entites that require lodestone parse
    //
    public function getLodestoneParseCount()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('id, name_en, lodestone_id')
            ->from('xiv_items')
            ->where('parsed_lodestone = 0')
            ->where('id > 100')
            ->where("name_en != ''");

        return [
            'items' => $dbs->get()->all(),
        ];
    }

    //
    // Get the action flags
    //
    public function getActions()
    {
        return [
            'real' => property_exists($this, 'real'),
            'set' => property_exists($this, 'set'),
            'manual' => method_exists($this, 'manual'),
        ];
    }
}
