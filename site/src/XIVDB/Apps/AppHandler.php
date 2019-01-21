<?php

namespace XIVDB\Apps;

//
// App Handler
//
use XIVDB\Apps\Database\Database,
    XIVDB\Apps\Site\RedisCache;

class AppHandler
{
    use \XIVDB\Apps\Site\FunctionsTrait;

    private $DatabaseInstance;
    private $RedisInstance;

    //
    // Get the current logged in user
    //
    protected function getUser()
    {
        $user = $this->getModule('users')->get();
        return $user ? $user : false;
    }

    /**
     * Get a shared database
     * @return Database
     */
    public function getRedis()
    {
        if (!$this->RedisInstance) {
            $this->RedisInstance = $this->getModule('redis');
        }

        return $this->RedisInstance;
    }

    /**
     * Get a shared database
     * @return Database
     */
    public function getDatabase()
    {
        if (!$this->DatabaseInstance) {
            $this->DatabaseInstance = $this->getModule('database');
        }

        return $this->DatabaseInstance;
    }

    //
    // Get a module
    //
    public function getModule($module, $var1 = null)
    {
        // Special case for twig
        if ($module == 'twig') {
            global $AppKernal;
            return $AppKernal->get('twig');
        }

        // custom modules
        switch($module)
        {
            // database
            case 'database': return new \XIVDB\Apps\Database\Database($var1); break;
            case 'sqlite': return new \XIVDB\Apps\Database\SQLiteDatabase(); break;
            case 'libra': return new \XIVDB\Apps\LibraData\LibraData(); break;

            // site stuff
            case 'redis': return new RedisCache(); break;
            case 'mail': return new \XIVDB\Apps\Site\Mail(); break;
            case 'sanitize': return new \XIVDB\Apps\Site\Sanitize(); break;
            case 'language': return new \XIVDB\Apps\Site\Language(); break;
            case 'cookie': return new \XIVDB\Apps\Site\Cookie(); break;
            case 'session': return new \XIVDB\Apps\Site\Session(); break;
            case 'feedback': return new \XIVDB\Apps\Site\Feedback(); break;
            case 'tracking': return new \XIVDB\Apps\Site\Tracking(); break;
            case 'csrf': return new \XIVDB\Apps\Site\CsrfManager(); break;

            // Libraries
            case 'moment': return new \Moment\Moment($var1); break;
            case 'xivsync': return new \XIVDB\Apps\Services\XIVSync\XIVSync(); break;

            // community
            case 'users': return new \XIVDB\Apps\Users\Users(); break;
            case 'comments': return new \XIVDB\Apps\Community\Comments($var1); break;
            case 'screenshots': return new \XIVDB\Apps\Community\Screenshots(); break;
            case 'characters': return new \XIVDB\Apps\Characters\Characters(); break;

            // game stuff
            case 'content': return new \XIVDB\Apps\Content\Content(); break;
            case 'maps': return new \XIVDB\Apps\Tools\Maps\Maps(); break;
            case 'mapper': return new \XIVDB\Apps\Tools\Mapper\Mapper(); break;
            case 'lorefinder': return new \XIVDB\Apps\Tools\LoreFinder\LoreFinder(); break;
            case 'icons': return new \XIVDB\Apps\Tools\Icons\Icons(); break;
            case 'gamedata': return new \XIVDB\Apps\GameData\GameData(); break;
            case 'gearsets': return new \XIVDB\Apps\Tools\Gearsets\Gearsets(); break;
            case 'shoppingcart': return new \XIVDB\Apps\Tools\ShoppingCart\Cart(); break;

            // google
            case 'google-analytics': return new \XIVDB\Apps\Services\Google\Analytics(); break;

            // third-party libraries
            case 'uuid4': return \Ramsey\Uuid\Uuid::uuid4()->toString(); break;
        }

        return false;
    }

    //
    // Access to various functions
    //
    public function Functions()
    {
        return new \XIVDB\Apps\Site\Functions\Functions();
    }

    //
    // Get a specific piece of game data
    //
    public function getGameData($type)
    {
        // database
        $dbs = $this->getModule('database');

        // get patch
        if ($type == 'patch') {
            $dbs->QueryBuilder
                ->reset()
                ->select('*')
                ->from('db_patch')
                ->order('patch', 'desc');

            return $dbs->get()->one()['patch'];
        }

        return false;
    }
}
