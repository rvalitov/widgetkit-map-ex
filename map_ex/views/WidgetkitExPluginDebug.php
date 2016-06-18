<?php
/*
Debug module for Widgetkit 2 plugins.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

require_once(__DIR__.'/helper.php');

namespace WidgetkitEx\MapEx{

class WidgetkitExPluginDebug extends WidgetkitExPlugin{
	private $id;
	
	private $debug_info = array();
	private $debug_warning = array();
	private $debug_error = array();
	private $isJoomla=WidgetkitExPlugin::IsJoomlaInstalled();
	private $CMS;
	
	public function __construct($id){
		$this->id=$id;
		
		if ($isJoomla)
			$CMS=WidgetkitExPlugin::getJoomlaVersion();
		else
			$CMS=WidgetkitExPlugin::getWPVersion();
		
		$wk_version=WidgetkitExPlugin::getWidgetkitVersion();
		$php_version=@phpversion();
		array_push($this->debug_info,'Processing widget '.$this->plugin_info['name'].' (version '.$this->plugin_info['version'].') on '.$CMS.' with Widgetkit '.$wk_version.' and PHP '.$php_version.'('.@php_sapi_name().')');
		if (version_compare('5.3',$php_version)>0)
			array_push($this->debug_error,'Your PHP is too old! Upgrade is strongly required! This widget may not work with your version of PHP.');
		else
			if (version_compare('5.6',$php_version)>0)
				array_push($this->debug_warning,'Your PHP is quite old. Although this widget can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.');
			
		if (version_compare('2.5.0',$wk_version)>0)
			array_push($this->debug_warning,"Your Widgetkit version is quite old. Although this widget may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit. Besides, you may experience some issues of missing options in the settings of this widget if you don't upgrade.");

		array_push($this->debug_info,'Host: '.@php_uname());
		$ipath=dirname(dirname(__FILE__));
		array_push($this->debug_info,'Widget installation path: '.$ipath);
		if ($isJoomla)
			if (preg_match_all('@.*\/administrator\/components\/com_widgetkit\/plugins\/widgets\/.+@',$ipath))
				array_push($this->debug_info,'Installation path is correct');
			else
				array_push($this->debug_error,'Installation path is not correct, please fix it. Read more in the Wiki.');
		else
			if (preg_match_all('@.*\/wp-content\/plugins\/widgetkit\/plugins\/widgets\/.+@',$ipath))
				array_push($this->debug_info,'Installation path is correct');
			else
				array_push($this->debug_warning,'Installation path is not correct, please fix it. Read more in the Wiki.');

		if ($isJoomla)
			array_push($this->debug_info,'Detected CMS: Joomla');
		else
			array_push($this->debug_info,'Detected CMS: WordPress');
	}
	
	public function addInfoString($s){
		array_push($this->debug_info,$s);
	}
	
	public function addWarningString($s){
		array_push($this->debug_warning,$s);
	}
	
	public function addErrorString($s){
		array_push($this->debug_error,$s);
	}
	
	public function printDebugStrings(){
		$this->printJSDebugText($this->debug_info,1);
		$this->printJSDebugText($this->debug_warning,2);
		$this->printJSDebugText($this->debug_error,3);
	}
	
	/*
	Prints debug info string for JS console output
	$typeid defines the log warning level
	*/
	private function printJSDebugString($s, $typeid=1){
		$prefix='['.$this->plugin_info['name'].' #'.$this->$id.'] ';
		$s=addslashes($s);
		$s=preg_replace("/\r\n|\r|\n/", "\\n",$s);
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
	private function printJSDebugText($arrayStrings, $typeid=1){
		foreach ($arrayStrings as $s){
			printJSDebugString($s,$typeid);
		}
	}
}

}