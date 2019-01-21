<?php

namespace XIVDB\Apps\GameSetup;

class Connections extends \XIVDB\Apps\AppHandler
{
	use ConnectionsLinksTrait;

	const TABLE = 'content_connections';

	//
	// Get the size of content connections
	//
	public function getSize()
	{
		return count($this->connections);
	}

	//
	// Initialize a connection between two content pieces
	//
    public function init($num)
    {
		return $this->process($this->connections[$num]);
    }

	//
	// Process a content connection
	//
	public function process($link)
	{
		$type = $link[0];
		$desc = $link[1];
		$left = $link[2];
		$right = $link[3];

		// check if flag column exists
		$this->checkConnectionColumn($left, $type, $desc);

		// get database
		$dbs = $this->getModule('database');

		// setup SQL query
		$sql = [];
		$sql[] = 'SELECT {left}.{left_id} as left_id, {right}.{right_id} as right_id';
		$sql[] = 'FROM {right} LEFT JOIN {left} ON {left}.{left_link} = {right}.{right_link}';
		$sql[] = 'WHERE {right}.{right_link} != 0';
		//$sql[] = 'LIMIT 0,100';
		$sql = implode(' ', $sql);

		// replace some stuff in query
		$sql = str_ireplace(
			['{left}', '{left_link}', '{left_id}','{right}', '{right_link}', '{right_id}'],
			[$left[0], $left[1], $left[2], $right[0], $right[1], $right[2],
		], $sql);

		// get results
		$insert = $dbs->sql($sql);
		$update = [];

		// append table and setup insert query
		foreach($insert as $i => $r) {
			$insert[$i] = [
				'left_id' => $r['left_id'] ? $r['left_id'] : 0,
				'left_table' => $left[0],
				'right_id' => $r['right_id'] ? $r['right_id'] : 0,
				'right_table' => $right[0],
				'content_type' => $type,
				'content_desc' => $desc,
			];

			$update[$r['left_id']] = isset($update[$r['left_id']]) ? $update[$r['left_id']] + 1 : 1;
		}

		// insert and return count
		$this->insert($insert);
		$this->update($left[0], 'connect_'. $type, $update);

		return sprintf('Inserted: %s of type: %s', count($insert), $type);
	}

	//
    // Insert data
    //
    public function insert($data)
    {
        $insert = [];
        foreach($data as $entry)
        {
            $insert[] = $entry;

            // if hit
            if (count($insert) == MANAGER_INSERT_LIMIT)
            {
                $this->processInsertableData($insert);
                $insert = [];
            }
        }

        if (count($insert) > 0) {
            $this->processInsertableData($insert);
        }
    }

	//
	// Update connection counts
	//
	public function update($table, $column, $data)
	{
		$dbs = $this->getModule('database');

		// to avoid mass spam updates, we will pool each id into a group based on the count
		$list = [];
		foreach($data as $id => $count) {
			$list[$count][] = $id;
		}

		// now we have count > list of ID's we can update using "in"
		foreach($list as $count => $idList)
		{
			$idList = array_filter(array_values($idList));
			$idList = implode(',', $idList);

			if (!empty($idList)) {
				// generate query
				$sql = vsprintf('UPDATE %s SET %s = %s WHERE id IN (%s)', [
					$table,
					$column,
					$count,
					$idList
				]);

				$dbs->sql($sql);
			}
		}
	}

    //
    // Process some insertable data
    //
    private function processInsertableData($data)
    {
        $dbs = $this->getModule('database');
        $qb = $dbs->QueryBuilder;

        $columns = array_keys(reset($data));

        // build insert query
        $qb->reset()->insert(self::TABLE)->schema($columns)->duplicate();

        // pass in values
        foreach($data as $entry) {
            $qb->values($entry, true);
        }

        // execute
        $dbs->execute();
    }

	//
	// Check if the connection column exists
	//
	private function checkConnectionColumn($left, $type, $desc)
	{
		// get dbs
		$dbs = $this->getModule('database');

		// get columns
		$columns = $dbs->sql('SHOW COLUMNS FROM '. $left[0]);

		// link field
		$addLinkField = 'connect_'. $type;
		$hasLinkField = false;

		// check columns
		foreach($columns as $col) {
			if ($col['Field'] == $addLinkField) {
				$hasLinkField = true;
			}
		}

		// if link field not found, we need to add it
		if (!$hasLinkField) {
			// create add sql
			$sql = sprintf("ALTER TABLE `%s` ADD `%s` INT(12) NOT NULL DEFAULT '0' COMMENT '%s', ADD INDEX (`%s`);", $left[0], $addLinkField, $desc, $addLinkField);

			// add connection column
			$response = $dbs->sql($sql);
		}
	}
}
