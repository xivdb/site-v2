<?php

namespace XIVDB\Apps\GameSetup;

class ParseFeedbackPositions extends \XIVDB\Apps\AppHandler
{
	use \XIVDB\Apps\GameData\GameDatabase;

	const TABLE = 'content_maps';

	public function init()
	{
		$this->parse();

		die;
	}

	//
	// Parse
	//
	public function parse()
	{
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder->select('*')->from('site_feedback')->where("category = 'Map Position'")->order('id', 'desc');

		$nameToId = [
			'[ENEMY]' => 12,
			'[FATE]' => 7,
			'[NPC]' => 11,
		];

		$insert = [];
		foreach($dbs->get()->all() as $i => $data)
		{
			$msg = explode("\n", $data['message']);

			if (!isset($msg[5])) {
				continue;
			}

			$npc = explode(' ', $msg[3]);
			$map = explode(' ', $msg[5]);

			// sort out mpc
			$type = $npc[0];
			$id = preg_replace("/[^0-9]/",null,$npc[1]);

			// sort out placename id
			$placename = preg_replace("/[^0-9]/",null,$map[1]);

			$pos = trim(str_ireplace(['XY:', '`', 'x', 'y', ':', 'z'], null, $msg[7]));
			$pos = str_ireplace('), ', '),', $pos);
			$pos = str_ireplace('),', ')|', $pos);
			$pos = str_ireplace(' )', ')', $pos);
			$pos = str_ireplace('( ', '(', $pos);
			$pos = str_ireplace(') (', ')|(', $pos);
			$pos = str_ireplace(' ,', ',', $pos);
			$pos = str_ireplace(', ', ',', $pos);
			$pos = str_ireplace(', ', ',', $pos);
			$pos = str_ireplace(';', ',', $pos);
			$pos = str_ireplace(',1', null, $pos);
			$pos = str_ireplace(' , ', ',', $pos);
			$pos = str_ireplace(')(', ')|(', $pos);
			$pos = str_ireplace(' ,', ',', $pos);
			$pos = str_ireplace('  ', ',', $pos);

			$pos = str_ireplace([
				' Eastern La Noscea,Wineport Aethernet,Agelss Wise',
				' Z 2.5',
				'(108)',
				' Fate',
				' it\'s the monster in the M Bab Green',
				' prior to Titan',
				' Seasong Grotto',
				' Providence,Panse de l\'ogre Fr',
				' in front of the Spirithold gate',
				' amongst others!',
			], null, $pos);

			if (isset($pos[0]) && $pos[0] == '(' && stripos($pos, ','))
			{
				if (stripos($pos, '-')) {
					$pos = str_ireplace(',', '|', $pos);
					$pos = str_ireplace('-', ',', $pos);
				}
				$pos = str_ireplace(['(', ')'], null, $pos);
				$pos = explode('|', $pos);
				foreach($pos as $i => $p) {
					$d = explode(',', $p);

					if (count($d) != 2) {
						unset($pos[$i]);
						continue;
					}

					if ($d[0] < 0 || $d[1] < 0 || $d[0] > 60 || $d[1] > 60) {
						unset($pos[$i]);
						continue;
					}
				}

				if ($pos)
				{
					foreach($pos as $coords)
					{
						$coords = explode(',', $coords);
						$cid = isset($nameToId[$type]) ? $nameToId[$type] : null;

						if (!$cid) {
							continue;
						}

						$insert[] = [
							'type_id' => $cid,
							'content_id' => $id,
							'placename' => $placename,
							'x' => $coords[0],
							'z' => $coords[1],
						];
					}
				}
			}
		}

		$this->insert(self::TABLE, $insert);
	}
}
