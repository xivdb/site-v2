<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\LibraData\LibraData,
    XIVDB\Apps\GameData\GameData,
    XIVDB\Apps\GameSetup\Connections,
    XIVDB\Apps\GameSetup\MapPositions,
    XIVDB\Apps\GameSetup\ParseFeedbackPositions;

//
// Home
//
trait AppLibraData
{
    protected function _libradata()
    {
        //
        // Show libra tables that can be imported
        //
        $this->route('/libra/import', 'GET|POST', function(Request $request)
        {
            $this->mustBeAdmin();

            return $this->respond('Dashboard/Libra/index.html.twig', [
                'filename' => FILE_LIBRA_SQL,
            ]);
        });

        //
        // Show libra tables that can be imported
        //
        $this->route('/libra/import/process', 'GET|POST', function(Request $request)
        {
            $this->mustBeAdmin();

            // Libra + database
            $libra = $this->getModule('libra');
            $dbs = $this->getModule('database');

            // get all items which haven't been processed through Lodestone
            $items = (new GameData())->getLodestoneParseCount();
            $items = $items['items'];

            // get items from libra
            $libraItems = $libra->select('Item');

            $lodestoneIds = [];
            foreach($items as $item) {
                if ($item['lodestone_id']) {
                    continue;
                }

                $id = $item['id'];
                $libraItem = $libraItems[$id];

                // get lodestone id
                $lodestoneId = explode('/', $libraItem['path'])[1];
                $lodestoneIds[$id] = $lodestoneId;
            }

            foreach($lodestoneIds as $itemId => $lodestoneId) {
                $dbs->QueryBuilder
                    ->update('xiv_items')
                    ->set('lodestone_id', ':lodestoneId')->bind('lodestoneId', $lodestoneId)
                    ->where('id = :id')->bind('id', $itemId);

                $dbs->execute();
            }

            $this->getModule('session')
                ->add('success', sprintf('Updated %s lodestone ids', count($lodestoneIds)));

            return $this->redirect('/libra/import');
        });
    }
}
