<?php

namespace XIVDB\Apps\Tools\LoreFinder;

//
// Comments class
//
class LoreFinder extends \XIVDB\Apps\AppHandler
{
	private $dbs;
	private $string;

	function __construct()
	{
		$this->dbs = $this->getModule('database');
	}

	//
	// Set search string
	//
	public function setString($string)
	{
		$this->string = $string;
	}

	//
	// Search xiv_quests_to_text
	//
	public function searchQuests()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select()
			->addColumns([ 'xiv_quests_to_text' => ['id', 'quest', 'define', 'text_{lang} as text'] ])
			->addColumns([ 'xiv_quests' => ['name_{lang} as name'] ])
			->from('xiv_quests_to_text')
			->join(['xiv_quests_to_text' => 'quest'],['xiv_quests' => 'id'])
			->where('text_{lang} LIKE :text')->bind('text', $this->string, true)
			->limit(0,1000);

		if (HIDDEN_PATCH && time() < HIDDEN_PATCH_REMOVAL) {
		    $this->dbs
                ->QueryBuilder->where('xiv_quests.patch != '. HIDDEN_PATCH);
        }

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
	// Search xiv_balloons
	//
	public function searchBalloons()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as text'])
			->from('xiv_balloons')
			->where('name_{lang} LIKE :name')->bind('name', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
	// Search xiv_contents_description
	//
	public function searchContentDescription()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select(['id', 'help_{lang} as text'])
			->from('xiv_contents_description')
			->where('help_{lang} LIKE :help')->bind('help', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
	// Search xiv_contents_description
	//
	public function searchInstanceTextData()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as text'])
			->from('xiv_instances_text_data')
			->where('name_{lang} LIKE :name')->bind('name', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
	// Search xiv_npc_yells
	//
	public function searchNpcYells()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as text'])
			->from('xiv_npc_yells')
			->where('name_{lang} LIKE :name')->bind('name', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
	// Search xiv_public_content_text_data
	//
	public function searchPublicContentTextData()
	{
		if (!$this->string) {
			return false;
		}

		// Search quest text
		$this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as text'])
			->from('xiv_public_content_text_data')
			->where('name_{lang} LIKE :name')->bind('name', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
	}

	//
    // Search xiv_items
    //
    public function searchItemDescriptions()
    {
        if (!$this->string) {
            return false;
        }

        // Search item text
		$this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as name', 'help_{lang} as text'])
			->from('xiv_items')
			->where('help_{lang} LIKE :help')->bind('help', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
    }

    //
    // Search xiv_leves
    //
    public function searchLeveDescriptions()
    {
        if (!$this->string) {
            return false;
        }

        // Search leves
        $this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as name', 'help_{lang} as text'])
			->from('xiv_leves')
			->where('help_{lang} LIKE :help')->bind('help', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
    }

    //
    // Search xiv_fates
    //
    public function searchFateDescriptions()
    {
        if (!$this->string) {
            return false;
        }

        // Search leves
        $this->dbs->QueryBuilder
			->select(['id', 'name_{lang} as name', 'help_{lang} as text'])
			->from('xiv_fates')
			->where('help_{lang} LIKE :help')->bind('help', $this->string, true)
			->limit(0,1000);

		$results = $this->dbs->get()->all();
		$results = $this->highlight($results, 'text');
		return $results;
    }

	//
	// Highlight entries
	//
	private function highlight($results, $field)
	{
		foreach($results as $i => $r) {
			$results[$i][$field] = str_ireplace(
				$this->string,
				sprintf('<span class="lf-highlight">%s</span>', $this->string),
				$r[$field]);
		}

		return $results;
	}

	//
	// Get a total for an entry
	//
	public function getTotal($table)
	{
		$this->dbs->QueryBuilder->total()->from($table);
		return $this->dbs->get()->one()['total'];
	}
}
