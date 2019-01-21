<?php

namespace XIVDB\Apps\Site;

class Language extends \XIVDB\Apps\AppHandler
{
    protected $data = [];

    function __construct()
    {
        // set the default language
        $this->setDefaultLanguage();

		// Get redis
        $redis = $this->getRedis();

        // check cache for data
        if (CACHE_TRANSLATIONS && $data = $redis->get('translations')) {
            $this->data = $data;
            return;
        }

        // add data
        $this->loadLanguageTranslations('db_languages_custom', 'text', 'custom', ['id', 'define', 'text_en as set_default', 'text_{lang} as text']);
        $this->loadLanguageTranslations('xiv_baseparams', 'name', 'params', ['id', 'name_en as set_default', 'name_{lang} as name']);
        $this->loadLanguageTranslations('xiv_items_ui_slot', 'name', 'slots', ['id', 'name_en as set_default', 'name_{lang} as name']);
        $this->loadLanguageTranslations('xiv_items_ui_category', 'name', 'ui', ['id', 'name_en as set_default', 'name_{lang} as name']);

        // language will rarely ever change and if it does I will forcefully reset cache
        if (CACHE_TRANSLATIONS) {
            $redis->set('translations', $this->data, CACHE_TRANSLATIONS_TIME);
        }
    }

    //
    // Get a translations
    //
    public function get($type, $id)
    {
        return $this->data[$type][$id];
    }

    //
    // Get custom translations
    //
    public function custom($id, $replace = null)
    {
        $string = $this->data['custom'][$id];

        if ($replace && is_array($replace)) {
            $string = str_ireplace(array_keys($replace), $replace, $string);
        }

        return $string;
    }

    //
    // Get all language translations
    //
    public function getAll($specific = null)
    {
        return $specific ? $this->data[$specific] : $this->data;
    }

    //
    // Set the default language
    //
    public function setDefaultLanguage()
    {
        if (defined('LANGUAGE')) {
            return;
        }

        $cookie = $this->getModule('cookie');
        $lang = DEFAULT_LANGUAGE;

        // if no http host, set as default language
        if (!isset($_SERVER['HTTP_HOST'])) {
            define('LANGUAGE', $lang);
            return;
        }

        // get from url
        $host = $_SERVER['HTTP_HOST'];
        $host = explode('.', $host);

        // get param over-rules all
        if (isset($_GET['language'])) {
            $setLanguage = trim($_GET['language']);
            $noCookie = true;
        }

        // change based on url, this is only time it is "saved" to cookie
        else if ($host[0] == 'ja' || $host[0] == 'de' || $host[0] == 'fr') {
            $setLanguage = trim($host[0]);
        } else if ($host[0] == 'xivdb' && !isset($_POST['language'])) {
            $setLanguage = 'en';
            $cookie->add(COOKIE_NAME_LANGUAGE, $lang);
        }

        // if language in post, set onto get
        elseif (isset($_POST['language'])) {
            $setLanguage = trim($_POST['language']);
        }

        // if language is in a cookie
        elseif ($cookie->get(COOKIE_NAME_LANGUAGE)) {
            $setLanguage = $cookie->get(COOKIE_NAME_LANGUAGE);
        }

        // passed language
        if (isset($setLanguage)) {
            // get language
            $getLang = strtolower($setLanguage);

            // verify it's an allowed language
            if (in_array($getLang, ['ja', 'en', 'de', 'fr'])) {
                $lang = $getLang;
            }
        }

        // only save lang if its not a post request
        if (!isset($noCookie) && !isset($_POST['language'])) {
            $cookie->add(COOKIE_NAME_LANGUAGE, $lang);
        }

        define('LANGUAGE', $lang);
    }

    //
    // Get a language id
    //
    public function getLangId($language)
    {
        return ['french' => 'fr', 'german' => 'de', 'japanese' => 'ja', 'chinese' => 'ch'][$language];
    }

    //
    // Load some languages
    //
    public function loadLanguageTranslations($table, $field, $index, $columns)
    {
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder->select($columns, false)->from($table);

        $data = [];
        foreach($dbs->get()->all() as $d)
        {
            if (empty($d[$field])) {
                $d[$field] = $d['set_default'];
            }

            $data[$d['id']] = $d[$field];

            if (isset($d['define']) && $d['define']) {
                $data[$d['define']] = $d[$field];
            }
        }

        $data = array_filter($data);
        $this->data[$index] = $data;
    }
}
