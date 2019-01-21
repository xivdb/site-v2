<?php

namespace XIVDB\Apps\GameData;

//
// Handle process
//
trait GameDataProcess
{
    //
    // Process
    //
    public function process()
    {
        // if no table, do nothing
        if (!static::TABLE && !method_exists($this, 'manual')) {
            $this->log('! No table or manual method.');
            return $this->log;
        }

		// If truncate tables exist
		if (property_exists($this, 'truncate')) {
			$this->processTruncate($this->truncate);
		}

        // json and saint parses
        if (static::TABLE) {
            $this->parseJson();
            $this->parseSaint();
        }

        // is there a repeater method?
        if (property_exists($this, 'repeater')) {
            $this->log('- Running Repeater Extraction');
            $this->parseRepeated();
        }

        // is there manual parsing?
        if (method_exists($this, 'manual')) {
            $this->log('- Running Manual Extractions');
            $this->manual();
        }

        // is there chinese?
        if (IMPORT_SAINT && method_exists($this, 'chinese')) {
            $this->log('- Parsing Chinese');
            $this->chinese();
        }

        // are there chinese translations?
        if (property_exists($this, 'chinese')) {
            $this->parseChinese();
        }

        return $this->log;
    }

    //
    // Parse the json file
    //
    public function parseJson()
    {
        // json import
        if (IMPORT_JSON && method_exists($this, 'json'))
        {
            $this->log('- Import JSON');
            $insert = [];

            if ($jsondata = $this->getJsonFileData())
            {
                // loop through json file
                foreach($jsondata as $id => $line)
                {
                    $jsonLines = $this->json($line);

                    // ensure we trim everything.
                    foreach($jsonLines as $i => $string) {
                        $jsonLines[$i] = trim($string);
                    }

                    // merge json with id+patch
                    $insert[] = array_merge([
                        'id' => $id,
                        'patch' => $this->patch,
                    ], $jsonLines);
                }

                // insert
                $this->insert(static::TABLE, $insert);
            }
            else
            {
                $this->log('!!! JSON file missing');
            }
        }
    }

    //
    // Parse the saint file
    //
    public function parseSaint()
    {
        // saint coinach import
        if (IMPORT_SAINT &&
            (property_exists($this, 'real') ||
            property_exists($this, 'set') ||
            property_exists($this, 'repeater')))
        {
            $this->log('- Import CSV');
            $insert = [];

            if ($csvdata = $this->getCsvFileData())
            {
                // loop through csv file
                foreach($csvdata as $id => $line)
                {
                    // continue if not numeric
                    if (!is_numeric($id)) {
                        continue;
                    }

                    $arr = [
                        'id' => $id,
                        'patch' => $this->patch,
                    ];

                    if (property_exists($this, 'custom')) {
                        foreach($this->custom as $column => $value) {
                            $arr[$column] = trim($value);
                        }
                    }

                    // merge in offsets
                    if (property_exists($this, 'real') && $this->real) {
                        foreach($this->real as $offset => $column) {
                            // overide id
                            if ($offset == 'id') {
                                $id = $line[$offset];
                            }

                            $value = $line[$offset];
                            $arr[$column] = $value;
                        }
                    }

                    // if set
                    if (property_exists($this, 'set') && $this->set) {
                        foreach($this->set as $offset => $column) {
                            $value = $line[$offset];
                            $arr[$column] = $value;
                        }
                    }

                    // ensure we trim everything.
                    foreach($arr as $i => $string) {
                        $arr[$i] = trim($string);
                    }

                    // fix true/false
                    foreach($arr as $i => $value) {
                        $value = (strtolower($value) == 'true') ? 1 : $value;
                        $value = (strtolower($value) == 'false') ? 0 : $value;
                        $arr[$i] = $value;
                    }

                    $this->insertCount = 0;
                    $insert[$id] = $arr;
                }

                // insert
                $this->insert(static::TABLE, $insert);
            }
            else
            {
                $this->log('!!! CSV File missing');
            }
        }
    }

    //
    // Parse repeated values
    //
    public function parseRepeated()
    {
        if (IMPORT_SAINT && (property_exists($this, 'repeater')))
        {
            $this->log('- Import CSV Repeater');
            $insert = [];

            if ($csvdata = $this->getCsvFileData())
            {
                // loop through csv file
                foreach($csvdata as $id => $line)
                {
                    // set increment count to 0
                    $setCount = 1;
                    $incCount = 0;

                    // create initial array
                    $arr = [
                        'content_id' => $id,
                        'patch' => $this->patch,
                    ];

                    // go through each value
                    for ($offset = $this->repeater['start']; $offset <= $this->repeater['finish']; $offset++)
                    {
                        // get column name
                        $column = $this->repeater['columns'][$incCount];

                        // get value
                        $value = $line[$offset];

                        // populate array with column and value
                        $arr['setnum'] = $setCount;
                        $arr[$column] = $value;

                        // increment count
                        $incCount++;

                        // if count at length, reset and increment set count
                        if ($incCount == count($this->repeater['columns'])) {
                            $incCount = 0;
                            $setCount++;

                            // add arr to insert
                            $insert[] = $arr;
                        }
                    }
                }

                // insert
                $this->insert(static::TABLE .'_repeater', $insert);
            }
            else
            {
                $this->log('!!! CSV File missing');
            }
        }
    }

    //
    // Parse chinese values
    //
    public function parseChinese()
    {
        $this->log('- Importing Chinese');

        // type is used to name the content for exclusives
        $type = str_ireplace('XIVDB\Apps\GameData\ExtractClasses\\', '', get_class($this));

        // Get the chinese files (returns 2)
        $csvFiles = $this->getChineseFilenames();

        $cnsFile = $csvFiles['cns'] . $this->getSpecificFilename('saint');
        $cntFile = $csvFiles['cnt'] . $this->getSpecificFilename('saint');
        $koFile = $csvFiles['ko'] . $this->getSpecificFilename('saint');

        // if files don't exist, do nothing
        if (!file_exists($cnsFile) || !file_exists($cntFile)) {
            return;
        }

        // get both cns and cnt translations
        $cnsCsv = $this->getCsv($cnsFile);
        $cntCsv = $this->getCsv($cntFile);
        $koCsv = $this->getCsv($koFile);
        $insert = [];

        // go through chinese simplified
        foreach($cnsCsv as $id => $cns) {
            // get the chinese tranditional line
            $cnt = $cntCsv[$id];
            $ko = $koCsv[$id];

            // populate data
            $data = [
                'id' => $id,
            ];

            // insert data from offsets
            foreach($this->chinese as $offset => $column) {
                $data[$column .'_cns'] = $cns[$offset];
                $data[$column .'_cnt'] = $cnt[$offset];
                $data[$column .'_ko'] = $ko[$offset];
            }

            $insert[] = $data;
        }

        $this->insert(static::TABLE, $insert);
    }
}
