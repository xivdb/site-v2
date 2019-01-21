<?php

namespace XIVDB\Apps\Content;

use Symfony\Component\HttpFoundation\Request;
use XIVDB\Apps\Content\ContentDB;

class Content extends \XIVDB\Apps\AppHandler
{
    use ContentProcess;
    use ContentMaps;
    use ContentData;
    use ContentTooltips;

    public $id;
    public $cid;
    public $type;
    public $timestamp;
    public $patch;
    public $data;
    public $quantity = 1;
    public $getAll = false;
    public $maxConnections = 250;
    public $minConnectionId = 20;

    public $flags = [
        'search' => false,
        'tooltip' => false,
        'extended' => false,
        'cart' => false,
    ];

    function __construct($getAll = false, $quantity = 1)
    {
        // timestamp and patch
        $this->timestamp = $this->getDate();
		$this->patch = $this->getGameData('patch');
        $this->getAll = $getAll;
        $this->quantity = $quantity;

        if (defined('static::TYPE')) {
            $this->cid = ContentDB::$contentIds[static::TYPE];
        }
    }

    //
    // Get the content that was requested
    //
    public function get()
    {
        // get content data
        if (!$this->data) {
            $this->getContentData();
        }

        // if no id
        if (static::TYPE == 'character' || !isset($this->data['id'])) {
            // if character
            if (isset($this->data['lodestone_id'])) {
                $this->unsetAction();
                $this->replaceAction();
                $this->manual();
            }

            return $this->data;
        }

        // Attach website urls
        $this->attachWebsiteUrls();

        // Attach content color
        $this->attachContentColor();

        // Attach linked content
        if (method_exists($this, 'extended')) {
            $this->extended();
        }

        // if manual modifications
        if (method_exists($this, 'manual')) {
            $this->manual();
        }

        // Attach correct icons
        $this->attachGameIcons();

        // if game data method
        if (method_exists($this, 'setGameData')) {
            $this->setGameData();
        }

        // add patch
        if (isset($this->data['patch'])) {
            $this->data['patch'] = $this->addPatch($this->data['patch']);
        }

		// if this is a search request and a search reduce method exists
        if (property_exists($this, 'unset') && $this->isFlagged('search')) {
            $this->unsetAction();
        }

        // If there is anything to replace
        if (property_exists($this, 'replace') && $this->isFlagged('search')) {
            $this->replaceAction();
        }

        // remove search stuff
        if ($this->isFlagged('search')) {
            unset($this->data['name_en']);
            unset($this->data['name_de']);
            unset($this->data['name_fr']);
            unset($this->data['name_ja']);
            unset($this->data['name_cns']);
        }

		// sort entire array A-Z
        $this->ksortRecursive($this->data);

        // return data
        return $this->data;
    }

    //
    // Set the ID
    //
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    //
    // Set the CID
    //
    public function setCid($cid)
    {
        $this->cid = $cid;
        return $this;
    }

    //
    // Set the type of content
    //
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    //
    // Preset data (avoids query)
    //
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    //
    // Set flags
    //
    public function setFlag($flag, $value)
    {
        $this->flags[$flag] = $value;
        return $this;
    }

    //
    // Set Quantity
    //
    public function setQuantity($qty)
    {
        $this->quantity = $qty;
        return $this;
    }

    //
    // is a flag set?
    //
    public function isFlagged($flag)
    {
        return isset($this->flags[$flag]) ? $this->flags[$flag] : false;
    }

    //
    // Get the data
    //
    public function getData()
    {
        return $this->data;
    }

    //
    // Get content id
    //
    public function getContentId($id = null)
    {
        if ($id) {
            return ContentDB::$contentIds[$id];
        }

        return $this->cid;
    }

    //
    // Get content type
    //
    public function getContentType($id)
    {
        $contentIdList = array_flip(ContentDB::$contentIds);
        return $contentIdList[$id];
    }
}
