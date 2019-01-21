<?php

namespace XIVDB\Apps\Translations;

class TranslationsStorage extends \XIVDB\Apps\AppHandler
{
    //
    // Run backup
    //
    public function backup()
    {
        $dbs = $this->getModule('database');

        // backup translations
        $dbs->QueryBuilder
            ->reset()
            ->select('*')
            ->from('db_languages_custom');

        $translations = $dbs->get()->all();
        $translations = json_encode($translations);

        $backupFolder = ROOT_BACKUPS . '/translations/';
        if (!is_dir($backupFolder)) {
            mkdir($backupFolder, 0777, true);
        }

        $filename = $backupFolder .'translations_%s.json';
        $filename = sprintf($filename, date('Y-m-d_H-i-s'));

        file_put_contents($filename, $translations);
    }

    public function findByHash($hash)
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from('db_languages_custom')
            ->where('hash = :hash')
            ->bind('hash', $hash)
            ->limit(0,1);

        return $dbs->get()->one();
    }

    //
    // Get translations for a specific language
    //
    public function getTranslations($language, $order, $category = null, $max = null)
    {
        // get translations
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select([
                'id', 'define', 'text_en',
                sprintf('%s as text_lang', $language),
                'notes', 'category', 'updated',
            ])
            ->from('db_languages_custom')
            ->order($order[0], $order[1]);

        if ($category) {
            $dbs->QueryBuilder
                ->where('category = :category')
                ->bind('category', $category);
        }

        if ($max) {
            $dbs->QueryBuilder
                ->limit(0, $max);
        }

        return $dbs->get()->all();
    }

    //
    // Get a single translation
    //
    public function getSingleTranslation($id)
    {
        // get translations
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('*')
            ->from('db_languages_custom')
            ->where('id = :id')
            ->bind('id', $id);

        return $dbs->get()->one();
    }

    //
    // Get categories
    //
    public function getCategories()
    {
        $dbs = $this->getModule('database');
        $dbs->QueryBuilder
            ->select('category')
            ->from('db_languages_custom')
            ->group('category');

        return $dbs->get()->all();
    }

    //
    // Update
    //
    public function update($id, $data)
    {
        $this->backup();

        $dbs = $this->getModule('database');

        $dbs->QueryBuilder
            ->reset()
            ->update('db_languages_custom')
            ->set('hash', ':bind0')->bind('bind0', sha1($data['text_en']))
            ->set('text_en', ':bind1')->bind('bind1', $data['text_en'])
            ->set('text_fr', ':bind2')->bind('bind2', $data['text_fr'] ? $data['text_fr'] : NULL)
            ->set('text_de', ':bind3')->bind('bind3', $data['text_de'] ? $data['text_de'] : NULL)
            ->set('text_ja', ':bind4')->bind('bind4', $data['text_ja'] ? $data['text_ja'] : NULL)
            ->set('text_cns', ':bind5')->bind('bind5', $data['text_cns'] ? $data['text_cns'] : NULL)
            ->set('define', ':bind6')->bind('bind6', $data['define'] ? $data['define'] : NULL)
            ->set('notes', ':bind7')->bind('bind7', $data['notes'] ? $data['notes'] : NULL)
            ->set('category', ':bind8')->bind('bind8', $data['category'] ? $data['category'] : 'misc')
            ->where('id = '. $id);

        $dbs->execute();
        return true;
    }

    //
    // Update
    //
    public function insert($data)
    {
        $dbs = $this->getModule('database');

        $hash = sha1($data['text_en']);

        $dbs->QueryBuilder
            ->insert('db_languages_custom')
            ->schema(['hash', 'text_en', 'text_fr', 'text_de', 'text_ja', 'text_cns', 'define', 'notes', 'category'])
            ->values([':bind0', ':bind1', ':bind2', ':bind3', ':bind4', ':bind5', ':bind6', ':bind7', ':bind8'])
            ->bind('bind0', $hash)
            ->bind('bind1', $data['text_en'])
            ->bind('bind2', $data['text_fr'] ? $data['text_fr'] : NULL)
            ->bind('bind3', $data['text_de'] ? $data['text_de'] : NULL)
            ->bind('bind4', $data['text_ja'] ? $data['text_ja'] : NULL)
            ->bind('bind5', $data['text_cns'] ? $data['text_cns'] : NULL)
            ->bind('bind6', $data['define'] ? $data['define'] : NULL)
            ->bind('bind7', $data['notes'] ? $data['notes'] : NULL)
            ->bind('bind8', $data['category'] ? $data['category'] : 'misc');

        $dbs->execute();
        return $dbs->id();
    }

    //
    // Bulk update
    //
    public function updateBulk($language, $translations)
    {
        $this->backup();

        $dbs = $this->getModule('database');
        $count = 0;

        // update translations
        foreach($translations as $id => $line)
        {
            $dbs->QueryBuilder
                ->reset()
                ->update('db_languages_custom')
                ->set('hash', ':bind3')->bind('bind3', sha1($line['english']))
                ->set('text_en', ':bind1')->bind('bind1', $line['english'])
                ->set($language, ':bind2')->bind('bind2', $line['translation'])
                ->where('id = '. $id);

            $dbs->execute();
            $count++;
        }

        return $count;
    }
}
