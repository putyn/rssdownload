<?php
/*
	putyn@u-232	19/10/2013

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
*/

define('DB_FILE','data/database.sqlite3');
define('DB_NAME','tveps');

function db_open(&$db) {

	$db = new SQLite3(DB_FILE);
	$count = $db->querySingle(sprintf('SELECT count(name) FROM sqlite_master WHERE name = \'%s\'',DB_NAME));
	
	if(!$count)
		$db->exec(sprintf('CREATE TABLE %s (name STRING, season INTEGER, episode INTEGER)',DB_NAME));
		
	if($db->lastErrorCode()) {
		info('Database error: '.$db->lastErrorMsg(),0);
		exit;
	} else
		info('Database initialized!');
}

function db_close(&$db) {

	$db->close();
}

function db_check_torrent($torrent) {
	GLOBAL $db;
	
	$count = $db->querySingle(sprintf('SELECT count(name) as count FROM %s WHERE name = \'%s\' AND season = %d AND episode = %d', DB_NAME, $torrent['name'], $torrent['season'], $torrent['episode']));
	return $count;
}

function db_insert_torrent($torrent) {
	GLOBAL $db;
	
	$db->exec(sprintf('INSERT INTO %s (name,season,episode) VALUES(\'%s\',%d,%d)',DB_NAME,$torrent['name'],$torrent['season'],$torrent['episode']));
}
?>