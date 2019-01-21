<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\LibraData\LibraData,
    XIVDB\Apps\GameData\GameData,
    XIVDB\Apps\GameSetup\Connections,
    XIVDB\Apps\GameSetup\MapPositions,
    XIVDB\Apps\GameSetup\ParseFeedbackPositions,
    XIVDB\Apps\GameSetup\LeveToItems,
    XIVDB\Apps\GameSetup\GameSetupLodestone;

//
// Home
//
trait AppGameData
{
    protected function _gamedata()
    {
		//
		// Game data dashboard
		//
        $this->route('/gamedata', 'GET', function(Request $request)
        {
            $this->mustBeAdmin();

            $gd = new GameData();
            $gsc = new Connections();

            return $this->respond('Dashboard/GameData/index.html.twig', [
                'patch' => $gd->getLatestPatch(),
                'lodestone' => $gd->getLodestoneParseCount(),
                'connections' => $gsc->getSize(),
            ]);
        });

        //
		// game data import list
		//
        $this->route('/gamedata/import', 'GET', function(Request $request)
        {
            $this->mustBeAdmin();
            $gd = new GameData();

            return $this->respond('Dashboard/GameData/import.html.twig', [
                'names' => $gd->getDataNames(),
                'patch' => $gd->getLatestPatch(),
            ]);
        });

        //
		// game data import view
		//
        $this->route('/gamedata/import/{name}', 'GET', function(Request $request, $name)
        {
            // permissions
            $this->mustBeAdmin();

            // new game data
            $class = (new GameData())->getGameExtractClass($name);
            $class->setName($name)
                  ->setId($request->get('id'));

            // response
            return $this->respond('Dashboard/GameData/view.html.twig', [
                'name' => $name,
                'patch' => $class->getLatestPatch(),
                'current_patch_json_entry' => $class->getSelectedJsonEntry(),
                'current_patch_csv_entry' => $class->getSelectedCsvEntry(),
                'current_patch_offsets' => $class->getOffsets(),
                'current_patch_set_offsets' => $class->getSetOffsets() + $class->getGroupOffsets(),
                'current_patch_repeater_offsets' => $class->getRepeaterOffsets(),
                'previous_patch_csv_entry' => $class->getSelectedCsvEntryPreviousPatch(),
                'filenames' => $class->getFilenames(),
                'sql' => $class->getCreateSql(),
                'table' => $class::TABLE,
                'actions' => $class->getActions(),
            ]);
        });

        //
		// game data import process
		//
        $this->route('/gamedata/import/{name}/process', 'GET', function(Request $request, $name)
        {
            // permissions
            $this->mustBeAdmin();
            ##$this->mustBeDev();

            // new game data
            $class = (new GameData())->getGameExtractClass($name);
            $class->setName($name)
                  ->setId($request->get('id'));

            // process
            $class->process();

            // return log
            return $this->json($class->log);
        });

        //
		// remove null entries
		//
        $this->route('/gamedata/nulls', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            //$this->mustBeDev();

            // new game data
            (new GameData())->removenulls();

            $this->getModule('session')->add('success', 'Null entries have been removed.');
            return $this->redirect('/gamedata');
        });

        //
		// copy icons to game folder
		//
        $this->route('/gamedata/copyicons', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            $extracted = ROOT_EXTRACTS . CURRENT_PATCH . '/saint/ui/icon/*';
            $webpath = ROOT_WEB . '/img/game/';

            // copy all new files (Shell used because its a zillion times faster)
            shell_exec("cp -r $extracted $webpath");
            //$this->copyDirectory($extracted, $webpath);

            // copy "no icon"
            $noIconCustom = ROOT_WEB .'/img/ui/noicon.png';
            $noIconPath = ROOT_WEB .'/img/game/000000/000000.png';
            copy($noIconCustom, $noIconPath);

            $this->getModule('session')->add('success', 'Game icons have been copied.');
            return $this->redirect('/gamedata');
        });

        //
        // Game patch list
        //
        $this->route('/gamedata/patches', 'GET|POST', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            $dbs = $this->getModule('database');

            // if saving an existing patch
            if ($request->get('edit') && $request->get('save'))
            {
                #$this->mustBeDev();
                $dbs->QueryBuilder
                    ->update('db_patch')
                    ->set('command', ':command')->bind('command', $request->get('command'))
                    ->set('patch_url', ':patch_url')->bind('patch_url', $request->get('patch_url'))
                    ->set('name_ja', ':name_ja')->bind('name_ja', $request->get('name_ja'))
                    ->set('name_en', ':name_en')->bind('name_en', $request->get('name_en'))
                    ->set('name_fr', ':name_fr')->bind('name_fr', $request->get('name_fr'))
                    ->set('name_de', ':name_de')->bind('name_de', $request->get('name_de'))
                    ->set('banner', ':banner')->bind('banner', $request->get('banner'))
                    ->set('is_expansion', ':is_expansion')->bind('is_expansion', $request->get('is_expansion'))
                    ->set('date', ':date')->bind('date', $request->get('date'))
                    ->set('folder', ':folder')->bind('folder', $request->get('folder'))
                    ->where('patch = :id')->bind('id', $request->get('edit'));

                $this->getModule('session')->add('success', sprintf('Patch: %s has been updated', $request->get('name_en')));
                $dbs->execute();
            }
            // if saving a new patch
            else if ($request->get('save'))
            {
                #$this->mustBeDev();
                $dbs->QueryBuilder
                    ->insert('db_patch')
                    ->schema(['command', 'patch_url', 'name_ja', 'name_en', 'name_fr', 'name_de', 'banner', 'is_expansion', 'date', 'folder'])
                    ->values([':command', ':patch_url', ':name_ja', ':name_en', ':name_fr', ':name_de', ':banner', ':is_expansion', ':date', ':folder'])
                    ->bind('command', $request->get('command'))
                    ->bind('patch_url', $request->get('patch_url'))
                    ->bind('name_ja', $request->get('name_ja'))
                    ->bind('name_en', $request->get('name_en'))
                    ->bind('name_fr', $request->get('name_fr'))
                    ->bind('name_de', $request->get('name_de'))
                    ->bind('banner', $request->get('banner'))
                    ->bind('is_expansion', $request->get('is_expansion'))
                    ->bind('folder', $request->get('folder'))
                    ->bind('date', $request->get('date'));

                $this->getModule('session')->add('success', sprintf('Patch: %s has been saved', $request->get('name_en')));

                $id = $dbs->get()->id();
                return $this->redirect('/gamedata/patches?edit='. $id);
            }

            $gamedata = (new GameData());

            // if editing
            if ($edit = $request->get('edit')) {
                $editing = $gamedata->getPatch($edit);

                // ensure patch folder has been created
                if (DEV && $editing['folder']) {
                    $gamedata->createPatchFolder($editing);
                }
            }

            return $this->respond('Dashboard/GamePatches/index.html.twig', [
                'list' => $gamedata->getPatchList(),
                'editing' => isset($editing) ? $editing : false,
            ]);
        });

        //
		// Parse the lodestone using the ID and Name and get some information
		// about the item (rare/ex/flags)
		//
        $this->route('/gamedata/lodestone/{id}/{name}', 'GET', function(Request $request, $id, $name)
        {
            // permissions
            $this->mustBeAdmin();

            // count and total
            $count = $request->get('count');
            $total = $request->get('total');
            $lodestoneId = $request->get('lsid');

            // process
            $data = (new GameSetupLodestone())->init($id, $name, $count, $total, $lodestoneId);
            return $this->json($data);
        });

        //
		// Performs connections between data so that I don't wildy do queries
		// - May consider removing this ...
		//
        $this->route('/gamedata/gamesetup/connections/{num}', 'GET', function($num)
        {
            // permissions
            $this->mustBeAdmin();

            return (new Connections())->init($num);
        });

        //
        // Parse Saint YAML offsets to see what has changed
        //
        $this->route('/misc/saint/yaml', 'GET', function(Request $request)
        {
            $this->mustBeAdmin();

            $yaml = new GameYaml();
            $yaml = $yaml->parse();

            if ($request->get('json')) {
                $yaml = json_encode($yaml->getFormatted(), JSON_PRETTY_PRINT);
                die(show($yaml));
            }

            if ($request->get('code')) {
                die(show($yaml->getAsCode()));
            }

            die(show($yaml->getFormatted()));
        });

        //
        // Convert python CSV, fixes the CSVs!
        //
        $this->route('/misc/pythoncsv', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            // path for files
            $path = ROOT_EXTRACTS . EXTRACT_PATH . EXTRACT_EXD;
            if (!is_dir($path)) {
                die(sprintf('Path does not exist: %s', $path));
            }

            $files = $this->getFiles($path);

            // fix exds
            foreach($files as $file)
            {
                if (stripos($file, '.exd') === false) {
                    continue;
                }

                $data = file_get_contents($file);
                if (!$data) {
                    continue;
                }

                $fix = [
                    '"\\' => '',
                    'b"' => '"',
                    "b'" => "'",
                ];

                $data = str_replace(array_keys($fix), $fix, $data);
                $data = explode("\n", $data);

                foreach($data as $i => $line)
                {
                    $line = trim($line);
                    $line = str_ireplace("###''", '###', $line);
                    $line = explode(',###', $line);

                    foreach($line as $j => $l)
                    {
                        $l = trim($l);

                        if (isset($l[0]) && $l[0] == "'") {
                            $l[0] = '"';
                            $l[ strlen($l) - 1 ] = '"';
                        }

                        $line[$j] = $l;
                    }

                    $line = implode(",", $line);
                    $data[$i] = $line;
                }

                $data = array_values(array_filter($data));
                flush();

                $data = implode("\n", $data);
                file_put_contents($file, $data);
            }

            // rename exd's to csv
            foreach($files as $file)
            {
                $newfile = str_ireplace('.exd', '.csv', $file);
                rename($file, $newfile);
            }

            $this->getModule('session')->add('success', 'CSVs have been converted');
            return $this->redirect('/gamedata');
        });

        //
        // Add python string table to the database, provides code to add to python files.
        //
        $this->route('/misc/pythonstrings', 'GET|POST', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            $result = false;

            if ($request->get('id'))
            {
                $id = $request->get('id');
                $name = $request->get('name');
                $table = $request->get('table');

                // setup result
                $result = [
                    'python' => sprintf(file_get_contents(__DIR__.'/code/python'), $name, $id),
                    'sql' => sprintf(file_get_contents(__DIR__.'/code/sql'), $table, $name, $table, $name, $table, $name, $table, $name, $table, $name),
                    'php' => sprintf(file_get_contents(__DIR__.'/code/php'), $name,$name,$name,$name,$name,$name,$name,$name),
                ];
            }

            return $this->respond('Dashboard/GameData/python_strings.html.twig', [
                'result' => $result,
            ]);
        });

        //
        // Create a new table for data
        //
        $this->route('/misc/sql/create/table', 'GET|POST', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            if ($request->get('name'))
            {
                // get module
                $dbs = $this->getModule('database');

                // get table
                $table = $request->get('name');

                // create queries
                $sql = sprintf(file_get_contents(__DIR__.'/code/create'), $table, $table);
                $sql = explode("\n", $sql);
                $sql = array_filter(array_values($sql));

                // run sql queries
                foreach($sql as $sqlQuery) {
                    if (strlen($sqlQuery) > 3) {
                        $dbs->sql($sqlQuery);
                    }
                }

                // create repeater
                if ($request->get('repeater')) {
                    // create queries
                    $sql = sprintf(file_get_contents(__DIR__.'/code/create_repeater'), $table, $table);
                    $sql = explode("\n", $sql);
                    $sql = array_filter(array_values($sql));

                    // run sql queries
                    foreach($sql as $sqlQuery) {
                        if (strlen($sqlQuery) > 3) {
                            $dbs->sql($sqlQuery);
                        }
                    }
                }

                $this->getModule('session')->add('success', sprintf('Table "%s" Created', $table));
            }

            return $this->respond('Dashboard/GameData/create_table.html.twig');
        });

        //
		// Import map positions
		//
        $this->route('/gamedata/maps', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            // new game data
            (new MapPositions())->init();

            $this->getModule('session')->add('success', 'Map Positions Inserted');
            return $this->redirect('/gamedata');
        });

        //
		// Import map positions from feedback
		//
        $this->route('/gamedata/maps/feedback', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            // new game data
            (new ParseFeedbackPositions())->init();

            $this->getModule('session')->add('success', 'Feedback Map Positions Inserted');
            return $this->redirect('/gamedata');
        });

        //
        // Arrange items into leves for linking
        //
        $this->route('/gamedata/fixleveitems', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            // new game data
            (new LeveToItems())->init();

            $this->getModule('session')->add('success', 'Leves to Items have been populated');
            return $this->redirect('/gamedata');
        });

        //
        // Handle Chinese
        //
        $this->route('/gamedata/chinese', 'GET', function(Request $request)
        {
            // permissions
            $this->mustBeAdmin();
            #$this->mustBeDev();

            return $this->respond('Dashboard/Chinese/index.html.twig');
        });

        //
        // Handle Chinese
        //
        $this->route('/gamedata/chinese/{type}', 'GET', function(Request $request, $type)
        {
            // permissions
            $this->mustBeAdmin();
            $this->mustBeDev();

            $directories =  array_diff(scandir(ROOT_CHINESE), ['..', '.']);
            $extractFolder = null;

            // Find the extract folder
            foreach($directories as $folder) {
                if (stripos($folder, date('Y')) !== false) {
                    $extractFolder = $folder; break;
                }
            }

            $source = ROOT_CHINESE . $extractFolder .'/exd/';
            $dest = ROOT_EXTRACTS . EXTRACT_PATH . CHINESE_EXD . $type;
            if (strlen($source) == 0 || strlen($dest) == 0) {
                die('Failed');
            }

            // copy
            $this->copyDirectory($source, $dest);
            $this->getModule('session')->add('success',
                sprintf('Chinese/korean translatiosn for %s copied.', $type)
            );

            // delete folder
            $this->deleteDirectory($source);

            return $this->redirect('/gamedata/chinese');
        });
    }
}
