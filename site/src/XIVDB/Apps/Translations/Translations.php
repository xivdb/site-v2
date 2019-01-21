<?php

namespace XIVDB\Apps\Translations;

use Symfony\Component\HttpFoundation\Request;

class Translations extends \XIVDB\Apps\AppHandler
{
    // request
    protected $translations;
    protected $request;
    protected $categories;
    protected $language;
    protected $languageToColumn = [
        'french' => 'text_fr',
        'german' => 'text_de',
        'japanese' => 'text_ja',
        'chinese' => 'text_cns',
    ];

    //
    // Set language
    //
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    //
    // Set request
    //
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    //
    // Get translations
    //
    public function get($id = null, $max = null)
    {
        if ($id) {
            return (new TranslationsStorage())->getSingleTranslation($id);
        }

        $language = $this->languageToColumn[$this->language];

        // order
        $order = ['updated', 'desc'];
        if ($this->request->get('order')) {
            $order = explode(',', $this->request->get('order'));
        }

        return (new TranslationsStorage())->getTranslations(
            $language,
            $order,
            $this->request->get('category'),
            $this->request->get('max')
        );
    }

    public function search($text)
    {
        $hash = sha1($text);
        $found = (new TranslationsStorage())->findByHash($hash);

        return $found;
    }

    //
    // Get translation categories
    //
    public function getCategories()
    {
        return (new TranslationsStorage())->getCategories();
    }

    //
    // Get coverage
    //
    public function getCoverage($translations = null)
    {
        if (!$translations) {
            $translations = $this->get();
        }

        $coverage = [
            'total' => 0,
            'completed' => 0,
            'percent' => 0,
            'remaining' => 0,
        ];

        foreach($translations as $line)
        {
            $coverage['total']++;

            if ($line['text_lang']) {
                $coverage['completed']++;
            } else {
                $coverage['remaining']++;
            }
        }

        $coverage['percent'] = round(($coverage['completed'] / $coverage['total'] * 100), 3);
        return $coverage;
    }

    //
    // Update a translation
    //
    public function update($id, $data)
    {
        return (new TranslationsStorage())->update($id, $data);
    }

    //
    // Insert a translation
    //
    public function insert($data)
    {
        return (new TranslationsStorage())->insert($data);
    }

    //
    // Update bulk
    //
    public function updateBulk($json)
    {
        // get language we're translating
        $language = $this->languageToColumn[$this->language];

        // get translatio narray
        $array = json_decode($json, true);
        return (new TranslationsStorage())->updateBulk($language, $array);
    }

    //
    // Get a list of backed up translation files
    //
    public function getBackups()
    {
        $directory = ROOT_BACKUPS . '/translations/';
        $backups = array_diff(scandir($directory), array('..', '.'));

        $list = [];
        foreach($backups as $filename) {
            $created = filemtime($directory . $filename);
            
            $list[$created] = [
                'filename' => $filename,
                'created' => date('F j, Y, g:i a', $created),
                'filesize' => $this->convertSize(filesize($directory . $filename)),
            ];
        }

        krsort($list);

        return $list;
    }
}
