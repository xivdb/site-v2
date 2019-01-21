<?php

namespace XIVDB\Routes\AppFrontend;

use Symfony\Component\HttpFoundation\Request;

//
// AppToolLoreFinder
//
trait AppToolLoreFinder
{
    protected function _toolLoreFinder()
    {
        //
        // Shopping cart showcase/about page
        //
        $this->route('/lore-finder', 'GET|POST|OPTIONS', function(Request $request)
        {
            $this->setLastUrl($request);

            $dbs = $this->getModule('database');
            $results = false;
            $total = false;

            // setup module
            $LoreFinder = $this->getModule('lorefinder');
            $LoreFinder->setString($request->get('loresearch'));

            // get totals
            $totals = [
                'xiv_quests_to_text' => $LoreFinder->getTotal('xiv_quests_to_text'),
                'xiv_items' => $LoreFinder->getTotal('xiv_items'),
                'xiv_leves' => $LoreFinder->getTotal('xiv_leves'),
                'xiv_fates' => $LoreFinder->getTotal('xiv_fates'),
                'xiv_balloons' => $LoreFinder->getTotal('xiv_balloons'),
                'xiv_contents_description' => $LoreFinder->getTotal('xiv_contents_description'),
                'xiv_instances_text_data' =>$LoreFinder->getTotal('xiv_instances_text_data'),
                'xiv_npc_yells' => $LoreFinder->getTotal('xiv_npc_yells'),
                'xiv_public_content_text_data' => $LoreFinder->getTotal('xiv_public_content_text_data'),
            ];

            // get results
            if ($request->get('loresearch')) {
                $results = [
                    'xiv_quests_to_text' => $LoreFinder->searchQuests(),
                    'xiv_items' => $LoreFinder->searchItemDescriptions(),
                    'xiv_leves' => $LoreFinder->searchLeveDescriptions(),
                    'xiv_fates' => $LoreFinder->searchFateDescriptions(),
                    'xiv_balloons' => $LoreFinder->searchBalloons(),
                    'xiv_contents_description' => $LoreFinder->searchContentDescription(),
                    'xiv_instances_text_data' => $LoreFinder->searchInstanceTextData(),
                    'xiv_npc_yells' => $LoreFinder->searchNpcYells(),
                    'xiv_public_content_text_data' => $LoreFinder->searchPublicContentTextData(),
                ];
            }

            $response = [
                'string' => strlen($request->get('loresearch')) > 0 ? $request->get('loresearch') : false,
                'totals' => $totals,
                'results' => $results,
            ];

            // if this is a json request
            if ($this->isApiRequest($request)) {
                return $this->json($response);
            }

            // response
            return $this->respond('Tools/LoreFinder/index.html.twig', $response);
        });
    }
}
