<?php

/**
 * Get stuff from lodestone
 */

namespace XIVDB\Apps\GameSetup;

use \Moment\Moment;

class GameSetupLodestone extends \XIVDB\Apps\AppHandler
{
    private $id;
    private $name;
	private $lodestoneId;

    public function init($id, $name, $count, $total, $lodestoneId = false)
    {
        $start = microtime(true);

		if (!$lodestoneId)
		{
	        if ($id < 2 || ($id > 23 & $id < 100)) {
	            return [$id, $name, 0, null];
	        }
		}

        $this->id = trim($id);
        $this->name = trim($name);
		$this->lodestoneId = trim($lodestoneId);

        $response = $this->process();

        $finish = microtime(true);
        $duration = round($finish - $start, 3);

        $estimated = ceil($duration * $count);
        $endtime = time() + $estimated;

        $moment = new Moment('@'. $endtime);

        $fromnow = $moment->fromNow()->getRelative() .' (%s)';
        $fromnow = sprintf($fromnow, $moment->format('H:i:s'));

        $time = [
            'duration' => $duration,
            'estimation' => $estimated,
            'endtime' => $endtime,
            'fromnow' => $fromnow,
        ];

        return [$id, $name, $time, $response];
    }

    //
    // Process a lodestone search
    //
    private function process()
    {
        $xivsync = $this->getModule('xivsync');

		if (!$this->lodestoneId)
		{
	        if (!$search = $xivsync->searchItem($this->name)) {
	            return sprintf('(1) %s search not found', $this->name);
	        }

	        if (!$search['results']) {
	            return sprintf('(2) %s search not found', $this->name);
	        }

	        // find the correct one
	        foreach($search['results'] as $res) {
	            if (strtolower($this->name) == strtolower($res['name'])) {
	                $this->lodestoneId = $res['id'];
	                break;
	            }
	        }
		}

		$id = $this->lodestoneId;

        if (!$id) {
            return sprintf('(3) %s search not found', $this->name);
        }

        if (!$item = $xivsync->getItem($id)) {
            return sprintf('(4) %s item data not found', $this->name);
        }

        if (!$this->lodestoneId) {
            if (trim($item['name']) !== trim(urldecode($this->name))) {
                return 'name different: '. $item['name'] .' != '. urldecode($this->name);
            }
        }

        $item['icon'] = str_ireplace('https://img.finalfantasyxiv.com/lds/pc/global/images/itemicon/', null, $item['icon']);
        $item['icon'] = explode('.', $item['icon'])[0];

        $data = [
            'lodestone_id' => $id,
            'lodestone_type' => 'item',
            'icon_lodestone' => $item['icon'],

            'is_unique' => ($item['is_unique']) ? $item['is_unique'] : '0',
            'is_untradable' => ($item['is_untradable']) ? $item['is_untradable'] : '0',
            'is_crest_worthy' => ($item['is_crestable']) ? $item['is_crestable'] : '0',
            'is_desynthesizable' => ($item['is_desynthesizable']) ? $item['is_desynthesizable'] : '0',
            'is_projectable' =>( $item['is_projectable']) ?  $item['is_projectable'] : '0',
            'is_dyeable' => ($item['is_dyeable']) ? $item['is_dyeable'] : '0',
            'is_convertible' => ($item['is_convertible']) ? $item['is_convertible'] : '0',

            'price_sell' => $item['sell_nq'],
            'price_sell_hq' => $item['sell_hq'],
            'parsed_lodestone' => '1',
            'parsed_lodestone_time' => date('Y-m-d H:i:s'),
        ];

        $dbs = $this->getModule('database');
        $dbs->QueryBuilder->update('xiv_items')->where('id = '. $this->id);

        foreach($data as $column => $val) {
            $bind = ':a'. mt_rand(0, 999999);
            $dbs->QueryBuilder
                ->set($column, $bind)
                ->bind($bind, $val);
        }

        $dbs->execute();

        return $item;
    }
}
