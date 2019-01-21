<?php

namespace XIVDB\Apps\Content;

class SpecialShop extends Content
{
    const TYPE = 'special-shop';
	const ICON = '/img/ui/shop_special.png';

    // All columns
    public static $main =
    [
        'id',
        'name_ja',
        'name_en',
        'name_fr',
        'name_de',
        'name_cns',
        'quest_shop',
        'patch',
    ];

    // Basic columns
    public static $basic =
    [
        'id',
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
    ];

    // Order columns
    public static $order =
    [
        'id' => 'ID',
        'name_{lang}' => 'Name',
        'patch' => 'Patch',
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
            ->from(ContentDB::SPECIAL_SHOPS)
            ->addColumns([ ContentDB::SPECIAL_SHOPS => array_merge(
                $this->isFlagged('extended') ? SpecialShop::$basic : SpecialShop::$main,
                SpecialShop::$language)
            ])
            ->where(sprintf('%s.id = :id', ContentDB::SPECIAL_SHOPS))
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
            'icon' => '/img/ui/shop_special_icon.png',
        ];
    }

    //
    // Attach linked content
    //
    public function extended()
    {
        $dbs = $this->getModule('database');

        // stuff excluded from add<Content>
        if (!$this->isFlagged('extended'))
        {
            // Add special shop
            $dbs->QueryBuilder
                ->select('*', false)
                ->from(ContentDB::TO_SPECIAL_SHOP)
                ->where(['special_shop = :id', 'receive_item_1 != 0'])
                ->bind('id', $this->id);

            $this->data['storedata'] = null;
			$this->data['currency'] = [];

            foreach($dbs->get()->all() as $i => $sd)
            {
                $fields = ['receive_item_1', 'receive_item_2', 'cost_item_1', 'cost_item_2', 'cost_item_3'];
                foreach($fields as $idx) {
					$item = $this->addItem($sd[$idx]);
                    $sd[$idx] = $sd[$idx] ? $item : null;

					if (substr($idx, 0, 4) == 'cost' && $item['id']) {
						$this->data['currency'][$item['id']] = $item;
					}
                }

                $sd['quest_item_1'] = $sd['quest_item_1'] ? $this->addQuest($sd['quest_item_1']) : null;

                $this->data['storedata'][$i] = $sd;
            }
        }

        // If not extended and not tooltip
        if (!$this->isFlagged('extended') && !$this->isFlagged('tooltip'))
        {

        }
    }
}
