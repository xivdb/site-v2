<?php
//
// Search for characters
//

namespace XIVDB\Apps\Search;

use XIVDB\Apps\Content\ContentDB;

class SearchCharacters extends Search
{
	protected $Content = 'Characters';
    protected $ContentTable = ContentDB::CHARACTERS;

	//
    // Base query
    //
    protected function base()
    {
        $this
            ->qb()
            ->select([
                '{characters}' => [
					'lodestone_id as id',
					'name as name_{lang}',
					'server',
					'avatar as icon',
					'last_updated'
				],
            ])
            ->from('{characters}');
    }

	//
    // Filters
    //
    protected function filters()
    {
		//
        // basic filters
        //
        $this->handleFilters([
            'server|et',
			'achievements_public',
        ]);
	}

	//
	// Custom replacer for this class
	//
	protected function replacer()
	{
		$this
			->qb()
			->replace('name_en', 'name')
			->replace('name_ja', 'name')
			->replace('name_fr', 'name')
			->replace('name_de', 'name')
			->replace('name_cns', 'name')
			->replace('`id`', '`lodestone_id`')
			->replace('characters.id', 'characters.lodestone_id');
	}
}
