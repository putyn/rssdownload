<?php
/*
	putyn@u-232	19/10/2013
  
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
*/

function deluge_authenticate() {
	$session_check = _request_json('auth.check_session');
	if(!$session_check['result']) {
		info('Trying to authenticate with web api...');
		$login = _request_json('auth.login',array(DELUGE_KEY));
		if($login['result'])
			goto auth_succesfull;
		else {
			info('Could not authenticate with web api, check password',0);
			exit;
		}
	} else {
	auth_succesfull:
		info('Successfully authenticated with web api!',2);
		$web_connect = _request_json('web.connect',array('61bd8dd60ac7a7aa46ae7f753ae7b652f8e77218'));
		$web_connected = _request_json('web.connected');
		
		if(!$web_connect['error'] && $web_connected['result']) {
			info('Successfully connected to daemon',2);
		} else {
			info('Could not connect to daemon, make sure deluged its open',0);
			exit;
		}
	}
} 

function deluge_download_from_url($url) {
	$url = str_replace('&amp;','&',$url);
	$request = _request_json('web.download_torrent_from_url',array($url));
	return !$request['error'] ? $request['result'] : false;
}

function deluge_add_torrent($torrent_file, $tvep_info) {
	
	$tmp_dir = dirname($torrent_file);
	$download_path = sprintf('%s\\%s\\Season %s\\',DOWNLOAD_PATH,$tvep_info['name'],$tvep_info['season']);
	if(!is_dir($download_path))
		mkdir($download_path,0,true);
	
	$torrent = array(array(array(
		'path'=>$torrent_file,
		'options'=>array(
			'add_paused'=>ADD_PAUSED, 
			'download_location'=>$download_path,
		))));
	$request = _request_json('web.add_torrents',$torrent);
	if($request['result']) {
		info('Torrent "'.$tvep_info['name_complete']. '" was added successfully!',2);
		if(is_dir($tmp_dir))
			exec(sprintf('rmdir /s /q %s',$tmp_dir));
	}
}
?>