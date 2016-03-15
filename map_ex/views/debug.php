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

$debug_info = array();
$debug_warning = array();
$debug_error = array();

$isJoomla=false;
if ( (class_exists('JURI')) && (method_exists('JURI','base')) )
	$isJoomla=true;

if ($settings['debug_output']){
	if ($isJoomla)
		$CMS=getJoomlaVersion();
	else
		$CMS=getWPVersion();

	$wk_version=getWidgetkitVersion();
	$php_version=@phpversion();
	array_push($debug_info,'Processing widget '.$widget_name.' (version '.$widget_version.') on '.$CMS.' with Widgetkit '.$wk_version.' and PHP '.$php_version.'('.@php_sapi_name().')');
	if (version_compare('5.3',$php_version)>0)
		array_push($debug_error,'Your PHP is too old! Upgrade is strongly required! This widget may not work with your version of PHP.');
	else
		if (version_compare('5.6',$php_version)>0)
			array_push($debug_warning,'Your PHP is quite old. Although this widget can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.');
		
	if (version_compare('2.5.0',$wk_version)>0)
		array_push($debug_warning,"Your Widgetkit version is quite old. Although this widget may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit. Besides, you may experience some issues of missing options in the settings of this widget if you don't upgrade.");

	array_push($debug_info,'Host: '.@php_uname());
	$ipath=dirname(dirname(__FILE__));
	array_push($debug_info,'Widget installation path: '.$ipath);
	if ($isJoomla)
		if (preg_match_all('@.*\/administrator\/components\/com_widgetkit\/plugins\/widgets\/.+@',$ipath))
			array_push($debug_info,'Installation path is correct');
		else
			array_push($debug_error,'Installation path is not correct, please fix it. Read more in the Wiki.');
	else
		if (preg_match_all('@.*\/wp-content\/plugins\/widgetkit\/plugins\/widgets\/.+@',$ipath))
			array_push($debug_info,'Installation path is correct');
		else
			array_push($debug_warning,'Installation path is not correct, please fix it. Read more in the Wiki.');

	if ($isJoomla)
		array_push($debug_info,'Detected CMS: Joomla');
	else
		array_push($debug_info,'Detected CMS: WordPress');

	array_push($debug_info,'Widget settings: '.print_r($settings,true));	
}

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
	$s=str_replace("'","\'",str_replace("\\","\\\\",preg_replace("/\r\n|\r|\n/", ' ',$s)));
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
	if (preg_match_all("@.*public\s+\\\$wp_version\s*=\s*'.+';@",$f,$matches))
		$v.=explode("'",$matches[0][0],3)[1];
	return trim($v);
}

function getWidgetkitVersion(){
	$f=@file_get_contents(__DIR__ .'/../../../../config.php',false,null,0,1400);
	if ( (!$f) || (!preg_match_all("@.*'version'\s+=>\s+'.+',@",$f,$matches)) )
		return "";
	return explode("'",$matches[0][0],5)[3];
}
