<?php
/*
Debug module for Widgetkit 2 plugins.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

global $widget_id;
global $widget_name;
global $widget_version;

$widget_name='';
$widget_version='';
$widget_id=$map_id;

ExtractWidgetInfo();

function ExtractWidgetInfo(){
	global $widget_name;
	global $widget_version;

	$widget_name='Unknown Widget';
	$widget_version='Unknown Widget';
	
	$f=@file_get_contents(__DIR__ .'/../assets/updater.js',false,null,0,1400);
	if ($f){
		if (preg_match_all("@.*var\s+widget_name\s*=\s*'.+';@",$f,$matches))
			$widget_name=explode("'",$matches[0][0],3)[1];
		if (preg_match_all("@.*var\s+widget_version\s*=\s*'.+';@",$f,$matches))
			$widget_version=explode("'",$matches[0][0],3)[1];
	}
}

/*
Prints debug info string for JS console output
$typeid defines the log warning level
*/
function printJSDebugString($s, $typeid=1){
	global $widget_id;
	global $widget_name;
	
	$prefix='['.$widget_name.' #'.$widget_id.'] ';	
	$s=str_replace("'","\'",preg_replace("/\r\n|\r|\n/", ' ',$s));
	switch($typeid){
		case 1:
			echo "console.info('".$prefix.$s."');";
			break;
		case 2:
			echo "console.warn('".$prefix.$s."');";
			break;
		case 3:
			echo "console.error('".$prefix.$s."');";
			break;
		default:
			echo "console.log('".$prefix.$s."');";
			break;
	}
}

/*
Prints debug info strings (array) for JS console output
$typeid defines the log warning level
*/
function printJSDebugText($arrayStrings, $typeid=1){
	foreach ($arrayStrings as $s){
		printJSDebugString($s,$typeid);
	}
}

function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
}

function getJoomlaVersion(){
	$f=@file_get_contents(__DIR__ .'/../../../../../../../libraries/cms/version/version.php',false,null,0,3400);
	if (!$f)
		return "";

	$v='Joomla ';
	if (preg_match_all("@.*public\s+\\\$RELEASE\s*=\s*'.+';@",$f,$matches))
		$v.=explode("'",$matches[0][0],3)[1];
	if (preg_match_all("@.*public\s+\\\$DEV_LEVEL\s*=\s*'.+';@",$f,$matches))
		$v.='.'.explode("'",$matches[0][0],3)[1];
	if (preg_match_all("@.*public\s+\\\$DEV_STATUS\s*=\s*'.+';@",$f,$matches))
		$v.=' '.explode("'",$matches[0][0],3)[1];
	if (preg_match_all("@.*public\s+\\\$CODENAME\s*=\s*'.+';@",$f,$matches))
		$v.=' '.explode("'",$matches[0][0],3)[1];
	return trim($v);
}
function getWPVersion(){
	$f=@file_get_contents(__DIR__ .'/../../../../../../../wp-includes/version.php',false,null,0,1400);
	if (!$f)
		return "";
	
	$v='WordPress ';
	if (preg_match_all("@.*public\s+\$wp_version\s*=\s*'.+';@",$f,$matches))
		$v=explode("'",$matches[0][0],3)[1];
	return trim($v);
}