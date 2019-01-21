<?php

namespace XIVDB\Apps\Search;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Content\ContentDB;

//
// Site search!
//
class Search extends \XIVDB\Apps\AppHandler
{
    use TraitSearch;
    use TraitCommands;
    use TraitHandlers;

    protected $request;
    protected $redis;
    protected $database;
    protected $data = [];
    protected $limit = SEARCH_LIMIT;
    protected $isPatchSearch = false;

    function __construct()
    {
        // If content is set (this class is extended)
        if (isset($this->Content) && $this->Content) {
            $contentClass = 'XIVDB\\Apps\\Content\\'. $this->Content;
            $this->Content = new $contentClass();
            $this->Content->setFlag('search', true);
        }

        // connect to redis
        $this->database = $this->getModule('database');
    }

    //
    // Access to query builder
    //
    protected function qb()
    {
        return $this->database->QueryBuilder;
    }

    //
    // Set the request
    //
    public function setRequest(Request $request)
    {
        $this->request = $request;

        // check for patch
        $this->commandCheck($request->get('string'));

        return $this;
    }

    //
    // Return the results from the search query
    //
    public function getResults()
    {
        // perform search
        $this->data = $this->request->get('one') ? $this->one() : $this->all();
        return $this->data;
    }

    //
    // Setup a search
    //
    public function search()
    {
        if ($data = $this->process()) {
            return $this->bundle($data);
        }

        return false;
    }

    //
    // Do a search on one tables
    //
    private function one()
    {
        $one = $this->request->get('one');
        switch($one)
        {
            default: $Class = false; break;
            case 'items': $Class = new SearchItems(); break;
            case 'recipes': $Class = new SearchRecipes(); break;
            case 'quests': $Class = new SearchQuests(); break;
            case 'actions': $Class = new SearchActions(); break;
            case 'instances': $Class = new SearchInstances(); break;
            case 'achievements': $Class = new SearchAchievements(); break;
            case 'fates': $Class = new SearchFates(); break;
            case 'leves': $Class = new SearchLeves(); break;
            case 'places': $Class = new SearchPlacenames(); break;
            case 'gathering': $Class = new SearchGathering(); break;
            case 'npcs': $Class = new SearchNPC(); break;
            case 'enemies': $Class = new SearchEnemies(); break;
            case 'emotes': $Class = new SearchEmotes(); break;
            case 'status': $Class = new SearchStatus(); break;
            case 'titles': $Class = new SearchTitles(); break;
            case 'minions': $Class = new SearchMinions(); break;
            case 'mounts': $Class = new SearchMounts(); break;
            case 'weather': $Class = new SearchWeather(); break;
            // case 'shops': $Class = new SearchShops(); break;

            // other types of content
            case 'characters': $Class = new SearchCharacters(); break;
        }

        if ($Class) {
            $Class->setRequest($this->request);
            return [ $one => $Class->search() ];
        }

        return false;
    }

    //
    // Do a search on all tables
    //
    private function all()
    {
        $r = $this->request;

        $sources =
        [
            'items'        => (new SearchItems())->setRequest($r)->search(),
            'recipes'      => (new SearchRecipes())->setRequest($r)->search(),
            'quests'       => (new SearchQuests())->setRequest($r)->search(),
            'actions'      => (new SearchActions())->setRequest($r)->search(),
            'instances'    => (new SearchInstances())->setRequest($r)->search(),
            'achievements' => (new SearchAchievements())->setRequest($r)->search(),
            'fates'        => (new SearchFates())->setRequest($r)->search(),
            'leves'        => (new SearchLeves())->setRequest($r)->search(),
            'places'       => (new SearchPlacenames())->setRequest($r)->search(),
            'gathering'    => (new SearchGathering())->setRequest($r)->search(),
            'npcs'         => (new SearchNPC())->setRequest($r)->search(),
            'enemies'      => (new SearchEnemies())->setRequest($r)->search(),
            'emotes'       => (new SearchEmotes())->setRequest($r)->search(),
            'status'       => (new SearchStatus())->setRequest($r)->search(),
            'titles'       => (new SearchTitles())->setRequest($r)->search(),
            'minions'      => (new SearchMinions())->setRequest($r)->search(),
            'mounts'       => (new SearchMounts())->setRequest($r)->search(),
            'weather'      => (new SearchWeather())->setRequest($r)->search(),
			//'shops'        => (new SearchShops())->setRequest($r)->search(),
        ];

        if (!$this->isPatchSearch) {
            $sources['characters'] = (new SearchCharacters())->setRequest($r)->search();
        }

        return $sources;
    }

    //
    // Bundle a search by passing each one to its Game class
    //
    public function bundle($data)
    {
        // process game class
        foreach($data['results'] as $i => $result) {
            $content = (isset($this->Content) && $this->Content)
                ? $this->Content->setData($result)->get()
                : $result;

            $data['results'][$i] = $content;
        }

        return $data;
    }

    //
    // Process a search
    //
    private function process()
    {
        // basics
        $this->base();
        $this->string();
        $this->order();
        $this->limit();
        $this->restrict();

        // if patch, skip on characters
        if ($this->isPatchSearch && $this->Content == 'Characters') {
            return false;
        }

        // if filters
        if (method_exists($this, 'filters')) {
            $this->filters();
        }

        // finish the SQL
        $this->finish();

        //$this->database->QueryBuilder->show(true);


        // get it
        $results = $this->database->get()->all();

        //show($results);
        //die;

        $total = $this->database->get($this->ContentTable)->total();
        $paging = $this->handlePaging($total);

        // arr
        return [
            'results' => $results,
            'total' => $total,
            'paging' => $paging,
        ];
    }
}
