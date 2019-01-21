<?php

namespace XIVDB\Apps\Content;

trait ContentProcess
{
    //
    // Reduce data that isn't needed
    //
    public function unsetAction()
    {
        if (!isset(static::$unset)) {
            return;
        }

        foreach(static::$unset as $i) {
            unset($this->data[$i]);
        }
    }

    //
    // Replace index key
    //
    public function replaceAction()
    {
        if (!isset(static::$replace)) {
            return;
        }

        foreach(static::$replace as $old => $new) {
            if (!isset($this->data[$old])) {
                continue;
            }

            $this->data[$new] = $this->data[$old];
            unset($this->data[$old]);
        }
    }

    //
    // Attaches the website url formats onto the data
    //
    public function attachWebsiteUrls()
    {
        $id = (static::TYPE == 'character' && !isset($this->data['id'])) ? $this->data['lodestone_id'] : $this->data['id'];
        $name = (static::TYPE == 'character') ? sprintf('%s/%s', $this->data['name'], $this->data['server']) : $this->data['name'];

        // basic urls
        $this->data['url'] = $this->url(static::TYPE, $id, $name);
        $this->data['url_type'] = static::TYPE;

        $this->data['url_api'] = API . $this->url(static::TYPE, $id);
        $this->data['url_xivdb'] = URL . $this->url(static::TYPE, $id, $name);

        // if this is not a search request, append additional urls
        if (!$this->isFlagged('search')) {
            // language urls
            $this->data['url_xivdb_ja'] = URL_JA . $this->url(static::TYPE, $id, $name);
            $this->data['url_xivdb_fr'] = URL_FR . $this->url(static::TYPE, $id, $name);
            $this->data['url_xivdb_de'] = URL_DE . $this->url(static::TYPE, $id, $name);
            # $this->data['url_xivdb_cns'] = URL_CNS . $this->url(static::TYPE, $this->data['id'], $this->data['name']);
            # $this->data['url_xivdb_cnt'] = URL_CNT . $this->url(static::TYPE, $this->data['id'], $this->data['name']);
            # $this->data['url_xivdb_ko'] = URL_KO . $this->url(static::TYPE, $this->data['id'], $this->data['name']);
        }

        // lodestone url
        if (isset($this->data['lodestone_type'])) {
            $this->data['url_lodestone'] = 'https://na.finalfantasyxiv.com/lodestone/playguide/db/' . $this->data['lodestone_type'] . '/' . $this->data['lodestone_id'];
        }

        return $this;
    }

    //
    // Fix icons
    //
    public function attachGameIcons()
    {
        $iconSet = null;
        $iconLodestone = false;

        // if fate, skip. Handle independantly
        if (static::TYPE == 'fate') {
            return $this;
        }

        // if icon set
        if (defined('static::ICON')) {
            return $this->data['icon'] = SECURE . static::ICON;
        }

        // if tomestone or allied note, prevent lodestone icon.
        if (in_array($this->data['id'], [23, 24, 26,28, 30, 31])) {
            $this->data['icon_lodestone'] = null;
        }

        // check for a local icon
        $local = '/img/game_local/'. substr($this->data['id'], 0, 1) .'/'. $this->data['id'] .'.jpg';

        //$user = $this->getUser();
        //print_r(ROOT_WEB . $local);
        //die;

        if (static::TYPE == 'item' && file_exists(ROOT_WEB . $local)) {
            $iconSet = true;
            $this->data['icon'] = $local;
            $this->data['icon_hq'] = $local;
        }

        // check for a lodestone icon
        elseif (isset($this->data['icon_lodestone']) && $this->data['icon_lodestone'])
        {
            $iconSet = true;
            $iconLodestone = true;
            $this->data['icon'] = 'https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/'. $this->data['icon_lodestone'] .'.png?'. date('Ymd');
            $this->data['icon_hq'] = !empty($this->data['icon_lodestone_hq'])
                ? 'https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/'. $this->data['icon_lodestone_hq'] .'.png?'. date('Ymd')
                : null;
        }

        // is there a game file icon?
        elseif (isset($this->data['icon']) && $this->data['icon'])
        {
            $iconSet = true;
            $this->data['icon'] = $this->iconize($this->data['icon']);
            $this->data['icon_hq'] = isset($this->data['icon_hq'])
                ? $this->iconize($this->data['icon_hq'], true)
                : $this->data['icon'];
        }

        // if no icon set, set placeholder
        if (!$iconSet) {
            $this->data['icon'] = '/img/ui/noicon.png';
            $this->data['icon_hq'] = '/img/ui/noicon.png';
        }

        // enforce validation to null if nothing set.
        $this->data['icon'] = $this->data['icon'] ? $this->data['icon'] : null;
        $this->data['icon_hq'] = $this->data['icon_hq'] ? $this->data['icon_hq'] : null;

        // if not icon, append secure path.
        if (!$iconLodestone && $this->data['icon']) {
            $this->data['icon'] = SECURE . $this->data['icon'];
        }

        if (!$iconLodestone && $this->data['icon_hq']) {
            $this->data['icon_hq'] = SECURE . $this->data['icon_hq'];
        }

        return $this;
    }

    //
    // Attach content color
    //
    public function attachContentColor()
    {
        if (isset($this->data['rarity'])) {
            $this->data['color'] = ContentDB::$rarity[$this->data['rarity']];
        }

        return $this;
    }

    //
    // Fix colors, in descriptiosn and such
    //
    public function setDescriptionColours($index)
    {
        if (!isset($this->data[$index])) {
            return $this;
        }

        $replace =
        [
            '/stronggreen' => '/span',
            '/strongred' => '/span',
            '/strongyellow' => '/span',

            'stronggreen' => 'span class="lime"',
            'strongred' => 'span class="red"',
            'strongyellow' => 'span class="yellow"',
        ];

        $this->data[$index] = str_ireplace(array_keys($replace), $replace, $this->data[$index]);

        // change some of the text
        $this->data[$index] = str_ireplace('EXP Bonus:', "\n<em class=\"lime\">EXP Bonus:</em>", $this->data[$index]);
        $this->data[$index] = str_ireplace('Duration:', "\n<em class=\"lime\">Duration:</em>", $this->data[$index]);

        return $this;
    }

    //
    // Work out repair level
    //
    public function setRepairLevel()
    {
        // work out repair level
        if (isset($this->data['item_repair']) && $this->data['item_repair']) {
            $idx = $this->data['item_repair']['id'];
            $this->data['classjob_repair']['level']
                = isset(ContentDB::$repairLevels[$idx])
                ? ContentDB::$repairLevels[$idx]
                : null;
        }

        return $this;
    }

    //
    // Set the repair price
    //
    public function setRepairPrice()
    {
        if (isset($this->data['item_repair']) && $this->data['item_repair']) {
            $idx = $this->data['item_repair']['id'];
            $this->data['repair_cost']
                = isset(ContentDB::$repairCosts[$idx])
                ? ContentDB::$repairCosts[$idx]
                : null;
        }

        return $this;
    }

    //
    // Set class job
    //
    public function setClassJobIcons()
    {
        // fix class job icons
        if (isset($data['classjobs']) && $data['classjobs']) {
            foreach($data['classjobs'] as $i => $d) {
                $d['icon'] = '/img/classes/set1/'. $d['icon'] .'.png';
                $data['classjobs'][$i] = $d;
            }
        }

        return $this;
    }
}
