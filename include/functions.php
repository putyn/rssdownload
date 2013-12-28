<?php
/*
	putyn@u-232	19/10/2013

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
*/
function _request($request_type,$request_url,$request_data = array()) {
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);

  if($request_type == 'post') {
    curl_setopt($ch, CURLOPT_POST,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$request_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded'));
  }

  curl_setopt($ch, CURLOPT_URL,$request_url);
  curl_setopt($ch, CURLOPT_COOKIEJAR, 'data/.cookiejar');
  curl_setopt($ch, CURLOPT_COOKIEFILE, 'data/.cookiejar');
  curl_setopt($ch, CURLOPT_USERAGENT, 'rss_watch client v1.0'); 
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

function _request_json($method, $data = array()) {
	$params = json_encode(array('method'=>$method,'params'=>$data,'id'=>1),JSON_PRETTY_PRINT);
	if(DEBUG > 1)
		print("\n".$params."\n");
	$return = gzdecode(_request('post','http://localhost:8112/json',$params));
	if(DEBUG > 1)
		print("\n".$return."\n");
	return json_decode($return,true);
	
}

function get_rss_torrents_list() {

	$rss_xml = _request('get',RSS_LINK);
	preg_match_all('/\<item\>(.*?)\<\/item\>/s',$rss_xml,$tmp_items,PREG_SET_ORDER);
	$torrents_list = array();
	
	foreach($tmp_items as $tmp_tveps) {
	
		preg_match_all('/\<(link|title)\>(.*?)\<\/\1\>/s',$tmp_tveps[0],$tmp_tvep,PREG_SET_ORDER);
		if(preg_match('/^(.+)\.S(\d+)E(\d+)/',$tmp_tvep[0][2],$tmp_tvep_info))
			$torrents_list[] = array('name_complete'=>$tmp_tvep[0][2],'link'=>$tmp_tvep[1][2],'name'=>str_replace('.',' ',$tmp_tvep_info[1]),'season'=>$tmp_tvep_info[2],'episode'=>$tmp_tvep_info[3]);
	}
	return $torrents_list;
}

function info($text,$level = 1) {
  /*level 0 FAIL
	level 1 DEFAULT
	level 2 SUCCESS
	level 3 WARNING
  */
  if(USE_ANSICON) {
	$levels = array('[41m','[44m','[42m','[43m');
	print(chr(27).$levels[$level]. date("h:i, d-M") . " ][ " . $text . " ]\n".chr(27)."[0m");
  } else {
	print(date("h:i, d-M") . " ][ " . $text . " ]\n");
  }
  sleep(1);
}
?>
