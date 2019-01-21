<?php
if (!file_exists(XIVDB_FILE))
{
	$xivdb = (new \Sync\Modules\XIVDBApi());
	$XIVDBDATA = $xivdb->getData();
	output('Obtained new XIVDB Data');
}
else
{
	$XIVDBDATA = require XIVDB_FILE;
	$XIVDBDATA = base64_decode($XIVDBDATA);
	$XIVDBDATA = json_decode($XIVDBDATA, true);
    output('Preloaded XIVDB Data');
}
