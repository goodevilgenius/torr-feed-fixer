<?php

$name = @$_GET['name'];

function quit() {
  header("HTTP/1.1 400 Bad Request");
  exit;
}

if (empty($name)) quit();

switch($name) {
case "fulldls":
  $url = "http://www.fulldls.com/rssfortv.php";
  $path = @$_GET['path'];
  if (!empty($path)) $url = "http://www.fulldls.com/$path";

  $data = @file_get_contents($url);
  $h = $http_response_header;

  if ($data === false) quit();
  if (empty($data)) {
	header("HTTP/1.1 503 Service Unavailable");
	exit;
  }
  
  $xml = new simplexmlelement($data);
  foreach($xml->channel->item as $i) {
	$i->addChild('enclosure');
	$i->enclosure->addAttribute('type','application/x-bittorrent');
	$i->enclosure->addAttribute('url', str_replace(array('torrent-tv','.html'),array('download-tv',''),$i->link) . '-' . urlencode(str_replace('-','',$i->title)) . '.torrent');
	$i->enclosure->addAttribute('length', 0);
  }
  
  header("Content-type: application/rss+xml");
  echo $xml->asXML();
  break;
default:
  quit();
}

