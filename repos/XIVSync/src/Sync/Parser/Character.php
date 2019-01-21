<?php

namespace Sync\Parser;

/**
 * Parse character data
 * Class Character
 * @package Sync\Parser
 */
class Character extends ParserHelper
{
    /**
     * @param $html
     * @return array|bool
     */
	public function parse($html)
	{
		$html = $this->trim($html, 'class="ldst__main"', 'class="ldst__side"');

		// check exists
        if ($this->is404($html)) {
			return false;
		}

        $this->setInitialDocument($html);

		$started = microtime(true);
		$this->parseProfile();
		$this->parseClassJobs();
		$this->parseAttributes();
		$this->parseCollectables();
		$this->parseEquipGear();
        $this->parseActiveClass();
		output('PARSE DURATION: %s ms', [ round(microtime(true) - $started, 3) ]);

		//show($this->data);die;

		return $this->data;
	}

    /**
     * Parse main profile bits
     */
	private function parseProfile()
	{

        printtime(__FUNCTION__.'#'.__LINE__);
		$box = $this->getDocumentFromRange('class="frame__chara__link"', 'class="parts__connect--state js__toggle_trigger"');
        printtime(__FUNCTION__.'#'.__LINE__);

		// id
		$data = explode('/', $box->find('.frame__chara__link', 0)->getAttribute('href'))[3];
		$this->add('id', trim($data));
        printtime(__FUNCTION__.'#'.__LINE__);

		// name
		$data = $box->find('.frame__chara__name', 0)->plaintext;
		$this->add('name', trim($data));
        printtime(__FUNCTION__.'#'.__LINE__);

		// server
		$data = $box->find('.frame__chara__world', 0)->plaintext;
		$this->add('server', trim($data));
        printtime(__FUNCTION__.'#'.__LINE__);

		// title
		$this->add('title', null);
		if ($title = $box->find('.frame__chara__title', 0)) {
			$this->add('title', trim($title->plaintext));
		}
        printtime(__FUNCTION__.'#'.__LINE__);

		// avatar
		$data = $box->find('.frame__chara__face', 0)->find('img', 0)->src;
		$data = trim(explode('?', $data)[0]);
		$this->add('avatar', $data);
		$this->add('portrait', str_ireplace('c0_96x96', 'l0_640x873', $data));
        printtime(__FUNCTION__.'#'.__LINE__);

		// biography
        $box = $this->getDocumentFromRange('class="character__selfintroduction"', 'class="btn__comment"');
		$data = trim($box->plaintext);
		$this->add('biography', $data);

        printtime(__FUNCTION__.'#'.__LINE__);

		// ----------------------
        // move to character profile detail
        $box = $this->getDocumentFromRange('class="character__profile__data__detail"', 'class="btn__comment"');
		// ----------------------

        printtime(__FUNCTION__.'#'.__LINE__);

		// race, clan, gender
        $data = $box->find('.character-block', 0)->find('.character-block__name')->innerHtml();
		list($race, $data) = explode('<br>', html_entity_decode(trim($data)));
        list($clan, $gender) = explode('/', $data);
		$this->add('race', strip_tags(trim($race)));
		$this->add('clan', strip_tags(trim($clan)));
		$this->add('gender', strip_tags(trim($gender)) == '♀' ? 'female' : 'male');

        printtime(__FUNCTION__.'#'.__LINE__);

		// nameday
		$node = $box->find('.character-block', 1);
		$data = $node->find('.character-block__birth', 0)->plaintext;
		$this->add('nameday', $data);

        printtime(__FUNCTION__.'#'.__LINE__);

		$this->add('guardian', [
			'icon' => explode('?', $node->find('img', 0)->src)[0],
			'name' => $node->find('.character-block__name', 0)->plaintext,
		]);

        printtime(__FUNCTION__.'#'.__LINE__);

		// city
        $box = $this->getDocumentFromRangeCustom(42,47);
		$this->add('city', [
            'icon' => explode('?', $box->find('img', 0)->src)[0],
            'name' => $box->find('.character-block__name', 0)->plaintext,
		]);

        printtime(__FUNCTION__.'#'.__LINE__);

		// grand company (and sometimes FC if they're in an FC but not in a GC)
		$this->add('grand_company', null);
        $this->add('free_company', null);

        $box = $this->getDocumentFromRangeCustom(48,64);
        if ($box)
        {
            // Grand Company
            if ($gcNode = $box->find('.character-block__name', 0)) {
                list($name, $rank) = explode('/', $gcNode->plaintext);
                $this->add('grand_company', [
                    'icon' => explode('?', $box->find('img', 0)->src)[0],
                    'name' => trim($name),
                    'rank' => trim($rank),
                ]);
            }

            // Free Company
            if ($node = $box->find('.character__freecompany__name', 0))
            {
                $id = explode('/', $node->find('a', 0)->href)[3];
                $this->add('free_company', $id);
            }
        }

        printtime(__FUNCTION__.'#'.__LINE__);

        unset($box);
		unset($node);
	}

    /**
     * Parse the characters class/jobs levels and exp.
     */
	private function parseClassJobs()
	{
        printtime(__FUNCTION__.'#'.__LINE__);
        $box = $this->getSpecial__ClassJobs();

		// class jobs
		$cj = [];
		foreach($box->find('.character__job') as $node)
		{
            printtime(__FUNCTION__.'#'.__LINE__);
			$node = $this->getDocumentFromHtml($node->outertext);

			foreach($node->find('li') as $li)
			{
			    // class name
				$name = trim($li->find('.character__job__name', 0)->plaintext);
				$nameIndex = strtolower($name);

				// ignore scholar
				if ($nameIndex == 'scholar') {
				    continue;
                }

				// level
				$level = trim($li->find('.character__job__level', 0)->plaintext);
				$level = ($level == '-') ? 0 : intval($level);

				// current exp
				list($current, $max) = explode('/', $li->find('.character__job__exp', 0)->plaintext);
				$current = ($current == '-') ? 0 : intval($current);
				$max = ($max == '-') ? 0 : intval($max);

				// store
				$cj[$nameIndex] = [
					'name' => $name,
					'level' => $level,
					'exp' => [
						'current' => $current,
						'max' => $max,
					],
				];
			}
            printtime(__FUNCTION__.'#'.__LINE__);
		}

        printtime(__FUNCTION__.'#'.__LINE__);

		$this->add('classjobs', $cj);
		unset($box);
	}

    /**
     * Parse the characters active class/job
     * THIS HAS TO RUN AFTER GEAR AS IT NEEDS
     * TO LOOK FOR SOUL CRYSTAL EQUIPPED
     */
	private function parseActiveClass()
	{
        printtime(__FUNCTION__.'#'.__LINE__);

		$xivdb = new \Sync\Modules\XIVDBApi();
		$box = $this->getDocumentFromClassname('.character__profile__detail', 0);
        printtime(__FUNCTION__.'#'.__LINE__);

		$level = trim($box->find('.character__class__data p', 0)->plaintext);
		$level = filter_var($level, FILTER_SANITIZE_NUMBER_INT);
        printtime(__FUNCTION__.'#'.__LINE__);

		$name = $box->find('.db-tooltip__item__category', 0)->plaintext;
		$name = explode("'", $name)[0];
		$name = str_ireplace(['Two-handed', 'One-handed'], null, $name);
		$name = trim($name);
        printtime(__FUNCTION__.'#'.__LINE__);

		$id = $xivdb->getRoleId($name);

		// Handle jobs
		$gear = $this->get('gear');
		$soulcrystal = isset($gear['soulcrystal']) ? $gear['soulcrystal']['id'] : false;
		if ($soulcrystal) {

            $soulArray = [
                '67fd81c209e' => 19, // pld
                'a03321484cc' => 20, // mnk
                '2b81316eeed' => 21, // war
                'f6720135c8b' => 22, // drg
                '3e5b5adfe7b' => 23, // brd
                '9cca5eb0fd2' => 24, // whm
                'a4302cc8e2f' => 25, // blm
                'e1570c3d994' => 27, // smn
                'eb511e3871f' => 28, // sch
                'ec798591c4e' => 30, // nin
                'b95eca0caf9' => 31, // mch
                'b57f6b930d5' => 32, // drk
                'fe184c7b6e2' => 33, // ast
            ];

            $jobId = array_key_exists($soulcrystal, $soulArray);

            if ($jobId) {
                $jobId = $soulArray[$soulcrystal];
                $name = $xivdb->get('classjobs')[$jobId]['name_en'];
                $id = $jobId;
            }
		}
        printtime(__FUNCTION__.'#'.__LINE__);

		$this->add('active_class', [
			'id' => $id,
			'level' => $level,
			'name' => $name,
		]);

		unset($box);
	}

    /**
     * Parse stats
     */
	private function parseAttributes()
	{
        printtime(__FUNCTION__.'#'.__LINE__);
        $box = $this->getSpecial__AttributesPart1();
		//$box = $this->getDocumentFromClassname('.character__content', 1);

		$stats = [];

		// attributes
        printtime(__FUNCTION__.'#'.__LINE__);
		foreach($box->find('.character__param__list', 0)->find('tr') as $node) {
			$name = $node->find('th')->plaintext;
			$value = intval($node->find('td')->plaintext);
			$stats['attributes'][$name] = $value;
		}

        // offensive properties
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('.character__param__list', 1)->find('tr') as $node) {
            $name = $node->find('th')->plaintext;
            $value = intval($node->find('td')->plaintext);
            $stats['offensive'][$name] = $value;
        }

        // defensive properties
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('.character__param__list', 2)->find('tr') as $node) {
            $name = $node->find('th')->plaintext;
            $value = intval($node->find('td')->plaintext);
            $stats['defensive'][$name] = $value;
        }

        // physical properties
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('.character__param__list', 3)->find('tr') as $node) {
            $name = $node->find('th')->plaintext;
            $value = intval($node->find('td')->plaintext);
            $stats['physical'][$name] = $value;
        }

        // mental properties
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('.character__param__list', 4)->find('tr') as $node) {
            $name = $node->find('th')->plaintext;
            $value = intval($node->find('td')->plaintext);
            $stats['mental'][$name] = $value;
        }

        /*
        $box = $this->getSpecial__AttributesPart2();

        // status resistances
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('.character__param__list', 0)->find('tr') as $node) {
            $name = $node->find('th')->plaintext;
            $value = intval($node->find('td')->plaintext);
            $stats['resistances'][$name] = $value;
        }
        */

        $box = $this->getSpecial__AttributesPart3();

        // hp, mp, tp, cp, gp etc
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('li') as $node) {
            $name = $node->find('.character__param__text')->plaintext;
            $value = intval($node->find('span')->plaintext);
            $stats['core'][$name] = $value;
        }

        /*
        $box = $this->getSpecial__AttributesPart4();

        // elementals
        printtime(__FUNCTION__.'#'.__LINE__);
        foreach($box->find('li') as $node) {
            $name = explode('__', $node->innerHtml())[1];
            $name = explode(' ', $name)[0];
            $value = intval($node->plaintext);
            $stats['elemental'][$name] = $value;
        }
        */

        printtime(__FUNCTION__.'#'.__LINE__);
		$this->add('stats', $stats);
		unset($box);
	}

    /**
     * Minions and Mounts
     */
	private function parseCollectables()
	{
        printtime(__FUNCTION__.'#'.__LINE__);
        $box = $this->getSpecial__Collectables();
		if (!$box) {
		    return;
        }

        printtime(__FUNCTION__.'#'.__LINE__);
		if (!$box->find('.character__mounts', 0) || !$box->find('.character__minion', 0)) {
		    return;
        }

		// get mounts
		$mounts = [];
        printtime(__FUNCTION__.'#'.__LINE__);
		foreach($box->find('.character__mounts ul li') as $node) {
			$mounts[] = trim($node->find('.character__item_icon', 0)->getAttribute('data-tooltip'));
		}

		$this->add('mounts', $mounts);

		// get minions
		$minions = [];
        printtime(__FUNCTION__.'#'.__LINE__);
		foreach($box->find('.character__minion ul li') as $node) {
			$minions[] = trim($node->find('.character__item_icon', 0)->getAttribute('data-tooltip'));
		}

		$this->add('minions', $minions);

		// fin
		unset($box);
	}

    /**
     * Gear
     */
	private function parseEquipGear()
	{
        printtime(__FUNCTION__.'#'.__LINE__);
        $box = $this->getSpecial__EquipGear();
		//$box = $this->getDocumentFromClassname('.character__content', 0);

		$gear = [];
		foreach($box->find('.item_detail_box') as $i => $node) {
            printtime(__FUNCTION__.'#'.__LINE__);
            $name = $node->find('.db-tooltip__item__name')->plaintext;
			$id = explode('/', $node->find('.db-tooltip__bt_item_detail', 0)->find('a', 0)->getAttribute('href'))[5];

			// add mirage
            $mirageId = false;
            printtime(__FUNCTION__.'#'.__LINE__);
            $mirageNode = $node->find('.db-tooltip__item__mirage');
            if ($mirageNode) {
                $mirageNode = $mirageNode->find('a', 0);
                if ($mirageNode) {
                    $mirageId = explode('/', $mirageNode->getAttribute('href'))[5];
                }
            }

            // add creator
            $creatorId = false;
            printtime(__FUNCTION__.'#'.__LINE__);
            $creatorNode = $node->find('.db-tooltip__signature-character');
            if ($creatorNode) {
                $creatorNode = $creatorNode->find('a',0);
                if ($creatorNode) {
                    $creatorId = explode('/', $creatorNode->getAttribute('href'))[3];
                }
            }

            // add dye
            $dyeId = false;
            printtime(__FUNCTION__.'#'.__LINE__);
            $dyeNode = $node->find('.stain');
            if ($dyeNode) {
                $dyeNode = $dyeNode->find('a',0);
                if ($dyeNode) {
                    $dyeId = explode('/', $dyeNode->getAttribute('href'))[5];
                }
            }

            // add materia
            $materia = [];
            $materiaNodes = $node->find('.db-tooltip__materia',0);
            if ($materiaNodes) {
                if ($materiaNodes = $materiaNodes->find('li')) {
                    foreach ($materiaNodes as $mnode) {
                        $mhtml = $mnode->find('.db-tooltip__materia__txt')->innerHtml();
                        if (!$mhtml) {
                            continue;
                        }

                        list($mname, $mvalue) = explode('<br>', html_entity_decode($mhtml));

                        $materia[] = [
                            'name' => trim(strip_tags($mname)),
                            'value' => trim(strip_tags($mvalue)),
                        ];
                    }
                }
            }

			// slot conditions, based on category
            printtime(__FUNCTION__.'#'.__LINE__);
			$slot = $node->find('.db-tooltip__item__category', 0)->plaintext;

			// if this is first item, its main-hand
			$slot = ($i == 0) ? 'mainhand' : strtolower($slot);

			// if item is secondary tool or shield, its off-hand
			$slot = (stripos($slot, 'secondary tool') !== false) ? 'offhand' : $slot;
			$slot = ($slot == 'shield') ? 'offhand' : $slot;

			// if item is a ring, check if its ring 1 or 2
			if ($slot == 'ring') {
				$slot = isset($gear['ring1']) ? 'ring2' : 'ring1';
			}

			$slot = str_ireplace(' ', '', $slot);
			$gear[$slot] = [
			    'id' => $id,
                'name' => $name,
                'mirage_id' => $mirageId,
                'creator_id' => $creatorId,
                'dye_id' => $dyeId,
                'materia' => $materia,
            ];
		}

		$this->add('gear', $gear);
		unset($box);
	}
}
