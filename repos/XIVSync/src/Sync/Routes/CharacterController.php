<?php

namespace Sync\Routes;

use Symfony\Component\HttpFoundation\Request;

trait CharacterController
{
    /**
     * Characters
     */
    protected function _characterParse()
    {
        /**
         * Get a character
         */
        $this->route('/character/parse/{id}', 'GET', function(Request $request, $id)
        {
            $url = sprintf(LODESTONE_CHARACTERS_URL, $id);
            $parser = new \Sync\App\Characters();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Get a character friends
         */
        $this->route('/character/parse/{id}/friends', 'GET', function(Request $request, $id)
        {
            $params = [];

            // page is optional
            if ($page = $request->get('page')) {
                $params[] = sprintf('page=%s', intval($page));
            }

            $params = $params ? '?' . implode('&', $params) : '';
            $url = sprintf(LODESTONE_CHARACTERS_FRIENDS_URL, $id) . $params;
            $parser = new \Sync\App\CharacterFriends();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Get a character following
         */
        $this->route('/character/parse/{id}/following', 'GET', function(Request $request, $id)
        {
            $params = [];

            // page is optional
            if ($page = $request->get('page')) {
                $params[] = sprintf('page=%s', intval($page));
            }

            $params = $params ? '?' . implode('&', $params) : '';
            $url = sprintf(LODESTONE_CHARACTERS_FOLLOWING_URL, $id) . $params;
            $parser = new \Sync\App\CharacterFollowing();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Get characters achievements
         */
        $this->route('/character/parse/{id}/achievements', 'GET', function(Request $request, $id)
        {
            $kind = $request->get('kind');
            if (!$kind) {
                return $this->json([
                    'success' => false,
                    'data' => [
                        'message' => 'Please provide a ?kind=X parameter. ALL achievements parse not supported',
                    ]
                ]);
            }

            $url = sprintf(LODESTONE_ACHIEVEMENTS_URL, $id, $kind);
            $parser = new \Sync\App\Achievements();
            $data = $parser->requestFromLodestone($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Search characters
         */
        $this->route('/character/search', 'GET', function(Request $request)
        {
            $params = [];

            // q param is required, even if empty
            $params[] = sprintf('q=%s', ucwords($request->get('name')));

            // server is optional
            if ($server = $request->get('server')) {
                $params[] = sprintf('worldname=%s', ucwords($server));
            }

            // page is optional
            if ($page = $request->get('page')) {
                $params[] = sprintf('page=%s', intval($page));
            }

            // build request
            $params = $params ? '?' . implode('&', $params) : '';
            $url = LODESTONE_CHARACTERS_SEARCH_URL . $params;

            $search = new \Sync\App\Search();
            $data = $search->parseCharacterSearch($url);

            return $this->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        /**
         * Add a character
         */
        $this->route('/character/add/{id}', 'GET', function(Request $request, $id)
        {
            $Characters = new \Sync\App\Characters();
            $FreeCompany = new \Sync\App\FreeCompany();

            $url = sprintf(LODESTONE_CHARACTERS_URL, $id);
            $newData = $Characters->requestFromLodestone($url);
            if (!$newData) {
                die('No new data from lodestone, autoUpdate will mark this character as deleted.');
            }

            // manage character
            $newData = (new \Sync\App\CharactersRoles())->manage($newData);
            $newData = (new \Sync\App\CharactersPets())->manage($newData);
            $gcData = (new \Sync\App\CharactersGrandCompanies())->manage([], $newData);
            (new \Sync\App\CharactersGearsets())->manage($newData);

            // generate hash before we remove stuff
            $hash = $Characters->generateActiveHash($newData);

            // Add FC to pending
            if ($fcId = $newData['free_company']) {
                $FreeCompany->addToPending($fcId);
            }

            // remove stats and gear as I save them with gearsets
            unset($newData['stats']);
            unset($newData['gear']);

            // remove free company, it's handled internally
            unset($newData['free_company']);

            // Add character
            $Characters->add($newData, $gcData, $hash);

            return $this->json([
                'success' => true,
                'message' => 'Added: '. $newData['name'],
            ]);
        });

        /**
         * Update a character (Puts them to the front)
         */
        $this->route('/character/update/{id}', 'GET', function(Request $request, $id)
        {
            if (!$id || $id < 1) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid character id',
                ]);
            }

            // ID
            $id = intval($id);
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $id = substr($id, 0, 20);

            // get character
            $sql = 'SELECT * FROM characters WHERE lodestone_id = ? ORDER BY last_updated ASC LIMIT 0,1';
            $character = $this->Database()->fetchAssoc($sql, [ (int) $id ]);

            if (!$character) {
                return $this->json([
                    'success' => false,
                    'message' => 'Could not find character.'
                ]);
            }

            // sql
            $sql = 'UPDATE characters SET
				last_updated = 0,
				achievements_last_updated = 0
				WHERE lodestone_id = ?';

            // query
            $stmt = $this->Database()->prepare($sql);
            $stmt->bindValue(1, (int)$character['lodestone_id']);
            $stmt->execute();

            return $this->json([
                'success' => true,
                'message' => sprintf('%s has been placed at front of the queue', $character['name'])
            ]);
        });

        /**
         * Forcefully update a character
         * Requires KEY
         */
        $this->route('/character/update/{id}/force', 'GET', function(Request $request, $id)
        {
            $this->locked($request);

            $Characters = new \Sync\App\Characters();
            $FreeCompany = new \Sync\App\FreeCompany();

            $character = $Characters->getUpdatedEntry($id);
            if (!$character) {
                die('Character not found by the provided ID');
            }

            $url = sprintf(LODESTONE_CHARACTERS_URL, $id);
            $newData = $Characters->requestFromLodestone($url);
            if (!$newData) {
                die('No new data from lodestone, autoUpdate will mark this character as deleted.');
            }

            // manage character
            $newData = (new \Sync\App\CharactersRoles())->manage($newData);
            $newData = (new \Sync\App\CharactersPets())->manage($newData);

            // get old data
            $oldSavedData = $Characters->getOldData($id);
            $oldData = json_decode($oldSavedData['data'], true);
            $gcData = json_decode($oldSavedData['data_gc'], true);

            // tracking
            (new \Sync\App\CharactersEvents())->manage($oldData, $newData);
            (new \Sync\App\CharactersTracking())->manage($oldData, $newData);
            (new \Sync\App\CharactersGearsets())->manage($newData);
            $gcData = (new \Sync\App\CharactersGrandCompanies())->manage($gcData, $newData);

            // generate hash before we remove stuff
            $newHash = $Characters->generateActiveHash($newData);

            // Add FC to pending
            if ($fcId = $newData['free_company']) {
                $FreeCompany->addToPending($fcId);
            }

            // remove stats and gear as I save them with gear-sets
            unset($newData['stats']);
            unset($newData['gear']);

            // remove free company, it's handled internally
            unset($newData['free_company']);

            // Add character
            $Characters->update(
                $character,
                $newData,
                $gcData,
                $newHash
            );


            $data = [
                'newhash' => $newHash,
                'oldhash' => $character['data_hash'],
                'hashdata' => base64_encode($Characters->generateActiveHash($newData, true)),
                'newdata' => base64_encode(json_encode($newData)),
                'olddata' => base64_encode(json_encode($oldData)),
            ];


            return $this->json($data);;
        });
    }
}
