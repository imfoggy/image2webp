#!/usr/bin/env php
<?php
require 'vendor/autoload.php';
require 'ImageConverter.php';
use QL\QueryList;

function is_cli(){
	return preg_match("/cli/i", php_sapi_name()) ? true : false;
}

if(!is_cli()){
	exit('please running it in CLI mode');
}
// 初始配置
ini_set ( 'memory_limit' , '2048M' );
set_time_limit ( 0 );
// 命令行提示
std:
fwrite (STDOUT,"\r\n please input a url address :");
$url = trim(fgets(STDIN));
$str="/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
if(!preg_match($str, $url)){  
    echo " \r\n URL is wrong, please input it again\r\n";
    goto std;
}else{
	echo "\r\n Fetching images, please wait...\r\n";
	sleep(2);
}
$parseUrl = parse_url($url);
//采集某页面所有的图片
$i = 1;
echo "\r\n ----------START----------\r\n";
$data = QueryList::get($url)->find('img')->map(function($item) use ($parseUrl){
	global $result, $i;
	$src = $item->attr('src');
	$name = pathinfo($src, PATHINFO_FILENAME);
	if(!stristr($src, 'http')){
		$httpUrl = $parseUrl['scheme'].'://'.$parseUrl['host'].'/'.ltrim($item->attr('src'), '/');
		$item->attr('src', $httpUrl);
	}

	$result = ImageConverter\convert($item->attr('src'), './webp/'.$name.'.webp', 100);
	if($result > 0){
		echo "\r\n the ".$i.' picture convert success';
		$i++;
	}
});
echo "\r\n";
echo "\r\n see the webp dirname";
echo "\r\n ----------END----------";
