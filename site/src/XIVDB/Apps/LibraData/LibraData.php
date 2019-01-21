<?php

namespace XIVDB\Apps\LibraData;

class LibraData extends \XIVDB\Apps\AppHandler
{
    use LibraDatabase;
    use LibraDatabaseHandler;

    function __construct()
    {
		// require an sqlite file
        if (!file_exists(FILE_LIBRA_SQL)) {
            die('Libra file does not exist for this patch, get it!');
        }

        // connect to libra
        $this->connect(FILE_LIBRA_SQL);
    }

	//
	// Get the data for a named entity
	//
	public function getData($name)
	{
		return $this->selectAll($name);
	}
}
