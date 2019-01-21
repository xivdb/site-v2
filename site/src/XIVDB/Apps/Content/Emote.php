<?php

namespace XIVDB\Apps\Content;

class Emote extends Content
{
    const TYPE = 'emote';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'icon',
        'log_message_targeted',
        'log_message_untargeted',
        'emote_category',
        'text_command',
        'patch',
        'lodestone_id',
        'lodestone_type',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
        'icon',
        'patch',
        'lodestone_id',
        'lodestone_type',
        'text_command',
    ];

    // Language columns
    public static $language =
    [
        'name_{lang} as name',
    ];

    // Search columns
    public static $search =
    [
        'id',
        'name_{lang} as name',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'icon',
        'emote_category',
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'emote_category' => 'Category',
        'patch' => 'Patch',
    ];

    public static $unset = [
        'text_command',
        'icon_hq',
    ];

    //
    // Get the content data
    //
    public function getContentData()
    {
        $dbs = $this->getModule('database');
        $sql = $dbs->QueryBuilder;

        // generate sql query
        $sql->select()
            ->from(ContentDB::EMOTE)
            ->addColumns([ ContentDB::EMOTE => array_merge(
                $this->isFlagged('extended') ? Emote::$basic : Emote::$main,
                Emote::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::EMOTE))
            ->bind('id', $this->id)
            ->limit(0,1);

        // return
        $this->data = $dbs->get()->one();
        return $this;
    }

    //
    // tooltip data
    //
    public function tooltip()
    {
        return [
            'name' => $this->data['name'],
            'icon' => $this->data['icon'],
        ];
    }

    //
    // Manual gamedata modification
    //
    public function manual()
    {
        $dbs = $this->getModule('database');

        $this->data['emote_category'] = $this->hasColumn('emote_category') ? $this->addEmoteCategory($this->data['emote_category']) : null;

        // if not extended
        if (!$this->isFlagged('extended'))
        {
            $this->data['text_command'] = $this->hasColumn('text_command') ? $this->addTextCommand($this->data['text_command']) : null;
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {

        }
    }

    //
    // Set some game data stuff
    //
    public function setGameData()
    {
        // sort out command
        if (!isset($this->data['text_command']) || !$this->data['text_command']) {
            $this->data['command'] = '/' . strtolower($this->data['name']);
            $this->data['command'] = str_ireplace(' ', null, $this->data['command']);
        } else {
            $this->data['command'] = $this->data['text_command']['command1'];
        }

        // if help set
        if (isset($this->data['text_command']['help'])) {
            $findAndReplace = [
                '[subcommand]' => "<em class=\"lime\">[subcommand]</em>",
                '[Unterbefehl]' => "<em class=\"lime\">[Unterbefehl]</em>",
                '→' => "<em class=\"yellow\">→</em> &nbsp;",
                '>>' => "<em class=\"yellow\">>></em> &nbsp;",
                '　motion' => "<em class=\"purple\">　motion</em> &nbsp;&nbsp;-&nbsp;&nbsp;",
            ];

            $this->data['text_command']['help_html'] = str_replace(array_keys($findAndReplace), $findAndReplace, nl2br($this->data['text_command']['help']));
        }
    }

}
