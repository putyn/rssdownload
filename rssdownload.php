<?php
/*
	putyn@u-232	19/10/2013

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
*/

require_once('include/functions.php');
require_once('include/deluge_functions.php');
require_once('include/db_functions.php');
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Bucharest');

define('DEBUG',true);
define('USE_ANSICON',true);
define('DELUGE_KEY','****');
define('DOWNLOAD_PATH','D:\\tveps');
define('ADD_PAUSED',false);
define('RSS_LINK','****');
$search_pattern = array('720p','HDTV');

db_open($db);
deluge_authenticate();
$torrents_list = get_rss_torrents_list();

foreach($torrents_list as $torrent) {
	if(preg_match_all('/('.join('|',$search_pattern).')/iU',$torrent['name_complete']) == 2) {
		info('Found torrent that matched the pattern "'.$torrent['name_complete'].'" ....',2);
		if(db_check_torrent($torrent) == 0) {
			$torrent_file = deluge_download_from_url($torrent['link']);
			deluge_add_torrent($torrent_file,$torrent);
			db_insert_torrent($torrent);
		} else
			info('Ignoring "'.$torrent['name_complete'].", already added!",3);
	} else 
		info('Ignoring "'.$torrent['name_complete'].'", did not match the pattern');
}

db_close($db);


?>
