<?php

namespace Sync\Modules;

//
// XIVDB Api
//
class XIVDBApi
{
	private $Http;
	private $host = 'https://api.xivdb.com';
	private $data;

	function __construct()
	{
		$this->Http = new \Sync\Modules\HttpRequest();
		$this->init();
	}

	public function init()
	{
		global $XIVDBDATA;

		// check global var
		if ($XIVDBDATA) {
			$this->data = $XIVDBDATA;
			return;
		}

		// if still no XIVDB, get it again
		if (!$XIVDBDATA)
		{
            output('Getting XIVDB Data');
			$this->query('exp_table', '/data/exp_table');
			$this->query('classjobs', '/data/classjobs');
			$this->query('grand_company', '/data/grand_company');
			$this->query('minions', '/minion?columns=id,name_en');
			$this->query('mounts', '/mount?columns=id,name_en');
			$this->query('items', '/item?columns=id,name_en,lodestone_id');

			$this->arrange();

			// simplify contents
			$data = json_encode($this->data);
			$data = base64_encode($data);

			// save file
			$data = sprintf('<?php return "%s"; ?>', $data);
			file_put_contents(XIVDB_FILE, $data);
            output('Saved XIVDB data to: '. XIVDB_FILE);
		}
	}

	//
	// Get some XIVDB data (this would of already been pre-populated);
	//
	public function get($type)
	{
		return $this->data[$type];
	}

	public function getData()
	{
		return $this->data;
	}

	//
	// Query some data from xivdb
	//
	private function query($name, $route)
	{
		$data = $this->Http->get($this->host . $route);
		$this->data[$name] = json_decode($data, true);
	}

	// get the role id from a name
	public function getRoleId($name)
	{
		$classjobs = $this->data['classjobs'];

		$found = false;
		foreach($classjobs as $cj) {
			if (strtolower($cj['name_en']) == strtolower($name)) {
				$found = $cj;
				break;
			}
		}

		if ($found['is_job'] == 1) {
		    $found = $classjobs[$found['classjob_parent']];
        }

		return $found['id'];
	}

	public function searchForItem($name)
    {
        //show($this->data['items']);
        foreach($this->data['items'] as $item) {
            if (strtolower($item['name_en']) == strtolower($name)) {
                return $item;
            }
        }

        return false;
    }

	//
	// Reorder some data
	//
	private function arrange()
	{
		// build array of items against their lodestone id
		foreach($this->data['items'] as $i => $obj) {
            unset($this->data['items'][$i]);

            $id = $obj['lodestone_id'] ? $obj['lodestone_id'] : 'game_'. $obj['id'];
			$this->data['items'][$id] = $obj;
		}

		// build array of items against their lodestone id
		foreach($this->data['classjobs'] as $i => $obj) {
			unset($this->data['classjobs'][$i]);
			$this->data['classjobs'][$obj['id']] = $obj;
		}
	}
}
