<?php

namespace XIVDB\Apps\Tools\Maps;

/**
 * Class Maps
 * @package XIVDB\Apps\Tools\Maps
 */
class Maps extends \XIVDB\Apps\AppHandler
{
    use ListTrait;
    use MarkersTrait;

    /**
     * Get map layers based on a specific index
     *
     * @param $type
     * @param bool $id
     * @return array
     */
	public function getLayersForType($type, $id = false)
	{
		// get maps
		$dbs = $this->getModule('database');
		$dbs->QueryBuilder
			->select('*')
			->from('xiv_placenames_maps');

        if ($id) {
            $dbs->QueryBuilder
                ->where(sprintf('%s = :id', $type))
                ->bind(':id', $id);
        }

		// get data
		$data = $dbs->get()->all();

		// restructure on type
		$newList = [];
		foreach($data as $map) {
			$id = $map[$type];

			// get image
			$map['image'] = $this->getMapImage($map);
            $map['layer_count'] = 0;

            // get layer count
            if ($map['territory_id']) {
                $dbs->QueryBuilder->count()->from('xiv_placenames_maps')->where('territory_id = '. $map['territory_id']);

                // if placename, restrict
                if ($map['placename']) {
                    $dbs->QueryBuilder->where('placename = '. $map['placename']);
                }

                $map['layer_count'] = $dbs->get()->total();
            }

			// get placename
			if ($map['placename']) {
				$dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_placenames')->where('id = '. $map['placename'])->limit(0,1);
                $map['placename_id'] = $map['placename'];
				$map['placename'] = $dbs->get()->one()['name'];
			}

			// get zone
			if ($map['zone'] > 0) {
				$dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_placenames')->where('id = '. $map['zone'])->limit(0,1);
                $map['zone_id'] = $map['zone'];
				$map['zone'] = $dbs->get()->one()['name'];
			}

			// get region
			if ($map['region'] > 0) {
				$dbs->QueryBuilder->select(['name_{lang} as name'])->from('xiv_placenames')->where('id = '. $map['region'])->limit(0,1);
                $map['region_id'] = $map['region'];
				$map['region'] = $dbs->get()->one()['name'];
			}

			$newList[$id][] = $map;
		}

		return $newList;
	}

    /**
     * Get a map image from an xiv_placenames_maps entry
     *
     * @param $entry
     * @return string
     */
	public function getMapImage($entry)
	{
		$folderData = explode('/', $entry['folder']);
		$folder = $folderData[0];
		$layer = $folderData[1];

		return SECURE . sprintf('/img/maps/%s/%s.%s.jpg', $folder, $folder, $layer);
	}

    /**
     * Get map layers
     * @return array
     */
    public function getLayers()
    {
        $maps = $this->getList();
        $layers = [];
        foreach($maps as $region) {
            foreach($region['placenames'] as $place) {
                $layers[$place['id']] = $place['layers'];
            }
        }

        return $layers;
    }

    /**
     * Get map placenames
     * @return array
     */
    public function getPlacenames()
    {
        $placenames = [];
        foreach($this->getList() as $region) {
            foreach($region['placenames'] as $place) {
                $placenames[$place['id']] = $place;
            }
        }

        return $placenames;
    }
}
