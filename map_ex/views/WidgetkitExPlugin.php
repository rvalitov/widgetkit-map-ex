<?php
/*
This is a helper class that is suitable for use with any type of plugin for Widgetkit 2:
1) Widgets
2) Content provider plugins

In order to use this class in your plugin you need to rename the namespace of this class according to the name of your plugin, for example, for MapEx widget this class should be declared in namespace "WidgetkitEx\MapEx".
If you need extra unique functions that are plugin-specific, then you should declare your own class that extends this class, see usage example for the WidgetkitExMapPlugin in the MapEx widget.

Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

namespace WidgetkitEx\MapEx{

//Class describes file or directory
class WKDiskItem{
	public $name;
	public $fullname;
	public $relativename;
	public $is_writable;
	public $size;//Can be zero for directory
	public $hash;
	public $is_file;
	public $contents;//Other items inside the directory, empty if it's a file
	
	//$relpath - relative path of the root item, used for filling the $relativename fields
	//$githash - if true, then SHA1 hash of Git style is calculated, else SHA1 of the file.
	public function AnalyzeItem($fullname,$relpath="",$githash=true){
		$this->fullname=$fullname;
		$this->name=pathinfo($fullname,PATHINFO_BASENAME);
		$this->is_file=!is_dir($this->fullname);
		$this->is_writable=is_writable($this->fullname);
		$this->relativename=$this->name;
		if ($relpath)
			$this->relativename=$relpath.$this->name;
		$this->contents=null;
		if ($this->is_file){
			$this->size=filesize($this->fullname);
			if ($githash){
				$fc=file_get_contents ($this->fullname);
				if ($fc!==false)
					$this->hash=sha1('blob '.$this->size."\0".$fc);
			}
			else
				$this->hash=sha1_file($this->fullname);
		}
		else{
			$result=array();
			$cdir = scandir($fullname);
			foreach ($cdir as $key => $value)
				if (!in_array($value,array(".",".."))) {
					$item=new WKDiskItem();
					$item->AnalyzeItem($fullname . DIRECTORY_SEPARATOR . $value,$this->relativename. DIRECTORY_SEPARATOR);
					array_push($result,$item);
				}
			$this->contents=$result; 
		}
	}
	
	private static function printDiskItem($value){
		if (is_object(!$value))
			return '';
		$color;
		$list='';
		if ($value->is_writable)
			$color='<span class="uk-text-success"><i class="uk-icon-check uk-margin-small-right"></i>';
		else
			$color='<span class="uk-text-danger"><i class="uk-icon-warning uk-margin-small-right"></i>';
		if ($value->contents){
			//Folder
			$list.='<li>'.$color.'<i class="uk-icon-folder-open-o uk-margin-small-right"></i>'.htmlspecialchars($value->name).'</span>';
			$list.='<ul class="uk-list">';
			$list.=WKDiskItem::printStructureInt($value->contents,true);
			$list.='</li></ul>';
		}
		else{
			//File
			$list.='<li>'.$color.'<i class="uk-icon-file-o uk-margin-small-right"></i>'.htmlspecialchars($value->name).'</span></li>';
		}
		return $list;
	}
	
	//Makes a beautiful output of directory structure
	private static function printStructureInt($array,$nested=false){
		$list='';
		if (!$nested)
			$list.='<ul class="uk-list">';
		if (is_array($array))
			foreach ($array as $value)
				$list.=WKDiskItem::printDiskItem($value);
		else
			$list.=WKDiskItem::printDiskItem($array);
		if (!$nested)
			$list.='</ul>';
		return $list;
	}
	
	//Makes a beautiful output of directory structure
	public function printStructure(){
		return WKDiskItem::printStructureInt($this);
	}
	
	public function hasWriteAccessProblems(){
		if (!$this->is_writable)
			return true;
		if (is_array($this->contents))
			foreach ($this->contents as $value)
				if (!$value->is_writable)
					return true;
		return false;
	}
	
	public function toArrayItem(){
		if (!$this->is_file)
			return false;
		return array('name'=>$this->relativename,'size'=>$this->size,'hash'=>$this->hash);
	}
	
	//Returns all the information about files in a single array
	public function toArray(){
		$l=array();
		$i=$this->toArrayItem();
		if (is_array($i))
			return array($i);
		if (is_array($this->contents))
			foreach ($this->contents as $value){
				$i=$value->toArray();
				if (is_array($i))
					$l=array_merge($l,$i);
			}
		return $l;
	}
}

class WidgetkitExPlugin{
	
	private $plugin_info;
	
	private $isWidget=false;
	private $isContentProvider=false;
	
	//Below are the versions of PHP and Widgetkit that are OK
	const minPHPVersion='5.3';
	const stablePHPVersion='5.6';
	const minWKVersion='2.5.0';
	const stableWKVersion='2.6.0';
	const minUIkitVersion='2.20.0';

	//Unique id of the plugin, usually this id is used as HTML id
	private $id;
	
	//The 3 arrays below contain strings that will be used for console log (JS) output, see usage example in the MapEx widget.
	private $debug_info = array();
	private $debug_warning = array();
	private $debug_error = array();
	
	//true, if current CMS is Joomla
	private $isJoomla;
	
	//Version of CMS
	private $CMSVersion;
	
	private $CMS;
	
	//true or false if installation path is correct
	private $pathCorrect = false;
	
	//Use {wk} or uk prefix for CSS classes. Old Widgetkit uses uk prefix for UIkit, latest Widgetkits use {wk}
	private $useWKPrefix;
	
	//Version of UIkit installed
	private $UIkitVersion;
	
	public function __construct($appWK,$id=0){
		$this->id=$id;
		
		$this->isJoomla=self::IsJoomlaInstalled();
		
		$this->plugin_info=$this->getWKPluginInfo($appWK);
		
		if ($this->isJoomla){
			$this->CMSVersion=$this->getJoomlaVersion();
			$this->CMS="Joomla";
		}
		else{
			$this->CMSVersion=$this->getWPVersion();
			$this->CMS="WordPress";
		}
		
		$wk_version=$this->getWKVersion();
		$php_version=@phpversion();
		array_push($this->debug_info,'Processing widget '.$this->plugin_info['name'].' (version '.$this->plugin_info['version'].') on '.$this->CMS.' '.$this->CMSVersion.' with Widgetkit '.$wk_version.' and PHP '.$php_version.'('.@php_sapi_name().')');
		if (version_compare(self::minPHPVersion,$php_version)>0)
			array_push($this->debug_error,'Your PHP is too old! Upgrade is strongly required! This widget may not work with your version of PHP.');
		else
			if (version_compare(self::stablePHPVersion,$php_version)>0)
				array_push($this->debug_warning,'Your PHP is quite old. Although this widget can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.');
			
		if (version_compare(self::minWKVersion,$wk_version)>0)
			array_push($this->debug_warning,"Your Widgetkit version is quite old. Although this widget may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit. Besides, you may experience some issues of missing options in the settings of this widget if you don't upgrade.");

		array_push($this->debug_info,'Host: '.@php_uname());
		$ipath=$this->plugin_info['path'];
		array_push($this->debug_info,'Widget installation path: '.$ipath);
		$this->pathCorrect=false;
		if ($this->isJoomla)
			if (preg_match_all('@.*\/administrator\/components\/com_widgetkit\/plugins\/'.($this->isWidget ? 'widgets' : 'content').'\/.+@',$ipath))
			{
				array_push($this->debug_info,'Installation path is correct');
				$this->pathCorrect=true;
			}
			else
				array_push($this->debug_error,'Installation path is not correct, please fix it. Read more in the Wiki.');
		else
			if (preg_match_all('@.*\/wp-content\/plugins\/widgetkit\/plugins\/'.($this->isWidget ? 'widgets' : 'content').'\/.+@',$ipath))
			{
				array_push($this->debug_info,'Installation path is correct');
				$this->pathCorrect=true;
			}
			else
				array_push($this->debug_warning,'Installation path is not correct, please fix it. Read more in the Wiki.');

		if ($this->isJoomla)
			array_push($this->debug_info,'Detected CMS: Joomla');
		else
			array_push($this->debug_info,'Detected CMS: WordPress');
		
		$this->useWKPrefix=false;
		$this->UIkitVersion=null;
		if ($this->pathCorrect){
			$wkuikit = $ipath.'/../../../vendor/assets/wkuikit';
			if ( (file_exists($wkuikit)) && (is_dir($wkuikit)) ){
				$this->useWKPrefix=true;
				$wkuikit.='/js/uikit.min.js';
				$this->UIkitVersion = self::readUIKitVersion($wkuikit);
			}
			if ($this->UIkitVersion==''){
				$wkuikit = $ipath.'/../../../vendor/assets/uikit/js/uikit.min.js';
				$this->UIkitVersion = self::readUIKitVersion($wkuikit);
			}
		}
	}
	
	//Reads UIkit version from specified uikit.min.js file
	private static function readUIKitVersion($filename){
		if ( (!file_exists($filename)) || (!is_file($filename)) || (!is_readable($filename)) )
			return null;
		
		$file_contents=file_get_contents($filename,false,null,0,30);
		if ($file_contents===false)
			return null;
		/* Example of version format:
		/*! UIkit 2.27.2 |
		*/
		if ( (preg_match('@\/\*\!\s+UIkit\s+(?<version>\d+\.\d+\.\d+)\s+\|@',$file_contents,$matches)==1) && (isset($matches['version'])) && ($matches['version']!='') )
			return $matches['version'];
		else
			return null;
	}
	
	public static function getCSSPrefix($appWK){
		return $appWK['config']->get('theme.support') === 'noconflict' ? 'wk' : 'uk';
	}
	
	public function getUIkitVersion(){
		return ($this->UIkitVersion!='') ? $this->UIkitVersion : '2.26.3';
	}

	//If $firstName=true, then returns first name of the current user or empty string if the first name is unkown
	//If $firstName=false, then returns second name of the current user or empty string if the second name is unkown
	//$widgetkit_user - is parameter that must be set to $app['user'] upon call.
	private function extractWKUserName($widgetkit_user,$firstName=true){
		//$name=trim($widgetkit_user->getName());
		//There is a bug in Widgetkit - it doesn't get the name of the user, so the code above is obsolete
		if (!$this->isCMSJoomla()) {
			//For Wordpress:
			$current_user = wp_get_current_user();
			if (!$current_user)
				return "";
			if ($firstName)
				if ($current_user->user_firstname)
					return $current_user->user_firstname;
				else
					return $current_user->user_login;
			else
				return $current_user->user_lastname;
		}
		//For Joomla:
		$name;
		$user=\JFactory::getUser($widgetkit_user->getId());
		if ($user)
			$name=$user->name;
		else
			return "";

		$split_name=explode(' ',$name);
		if ($firstName)
			 return ((sizeof($split_name)>0)?$split_name[0]:$name);
		@array_shift($split_name);
		return ((sizeof($split_name)>0)?implode(' ',$split_name):'');
	}

	//true if Joomla is installed
	public function isCMSJoomla(){
		return $this->isJoomla;
	}
	
	public function isCMSWordPress(){
		return !$this->isJoomla;
	}
	
	//Returns CMS version
	public function getCMSVersion(){
		return $this->CMSVersion;
	}
	
	//Returns CMS name (Joomla or WordPress)
	public function getCMSName(){
		return $this->CMS;
	}

	//Returns true, if the current CMS is Joomla
	public static function IsJoomlaInstalled(){
		return ( (class_exists('JURI')) && (method_exists('JURI','base')) );
	}

	//Returns true, if it's a valid accessable URL
	public static function url_exists($url) {
		if (!$fp = curl_init($url)) return false;
		return true;
	}

	//Returns Joomla version or empty string if failed
	public function getJoomlaVersion(){
		if ($this->isCMSJoomla()){
			$jversion = new \JVersion;
			return $jversion->getShortVersion();
		}
		else
			return "";
	}

	//Returns WordPress version or empty string if failed
	public function getWPVersion(){
		if (!$this->isCMSWordPress())
			return "";
		$f=@file_get_contents($this->getRootDirectory().'/wp-includes/version.php',false,null,0,1400);
		if (!$f)
			return "";
		
		if (preg_match_all("@.*\\\$wp_version\s*=\s*'.+';@",$f,$matches))
			$v.=explode("'",$matches[0][0],3)[1];
		return trim($v);
	}

	//Returns Widgetkit version or empty string if failed
	public function getWKVersion(){
		$f=@file_get_contents($this->getWKDirectory().'/config.php',false,null,0,1400);
		if ( (!$f) || (!preg_match_all("@.*'version'\s+=>\s+'.+',@",$f,$matches)) )
			return "";
		return explode("'",$matches[0][0],5)[3];
	}

	public function getInfo($htmlencode=true){
		if (!$htmlencode)
			return $this->plugin_info;
		
		$result=array();
		foreach ($this->plugin_info as $key => $value)
			$result[$key]=htmlspecialchars($value);
		return $result;
	}
	
	public function isWidget(){
		return $this->isWidget;
	}
	
	public function isContentProvider(){
		return $this->isContentProvider;
	}
	
	public function isJoomla(){
		return $this->isJoomla;
	}

	public function getPluginDirectory(){
		return $this->plugin_info['path'];
	}
	
	public function getPluginURL(){
		return $this->plugin_info['url'];
	}
	
	public function getWebsiteRootURL(){
		return $this->plugin_info['root_url'];
	}
	
	public function getWKDirectory(){
		return $this->plugin_info['wk_path'];
	}
	
	public function getRootDirectory(){
		return $this->plugin_info['root'];
	}
	
	//Returns array with info about current plugin (no matter if it's a widget or a content provider). It works only for custom plugins that are created with updater.js file.
	//The array contains following fields:
	//name 			- the name of the plugin or empty string if unknown.
	//version 		- the version of the plugin or empty string if unknown.
	//codename 		- the name of the distro (codename) or empty string if unknown.
	//date 			- the release date of the plugin or empty string if unknown.
	//logo 			- the absolute URL of the logo of the plugin or empty string if unknown.
	//wiki 			- the absolute URL of wiki (manual) for the plugin or empty string if unknown.
	//website		- the absolute URL of home website (homepage) for the plugin or empty string if unknown.
	//root_url 		- the aboslute URL of the current website
	//path			- directory on the server where the plugin is located
	//relativepath	- relative path to the plugin from the Widgetkit directory
	//wk_path		- directory on the server where the Widgetkit is installed
	//root			- directory on the server where the website is located
	//url			- absolute URL to the directory where the plugin is located
	//safe_name		- unique safe name of the plugin, which can be used in CSS, HTML and JavaScript
	private function getWKPluginInfo($appWK){
		$info=[
			'name'=>'',
			'version'=>'',
			'codename'=>'',
			'version'=>'',
			'date'=>'',
			'logo'=>'',
			'wiki'=>'',
			'website'=>'',
			'root_url'=>'',
			'path'=>'',
			'relativepath'=>'',
			'wk_path'=>'',
			'root'=>'',
			'url'=>'',
			'safe_name'=>'',
		];
		
		//We perform a sequental scan of parent directories of the current script to find the plugin install directory
		$widgetkit_dir_name;
		$baseurl;

		if ($this->isCMSJoomla()){
			$widgetkit_dir_name=DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_widgetkit";
			$baseurl=\JURI::base();
		}
		else{
			$widgetkit_dir_name=DIRECTORY_SEPARATOR."wp-content".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."widgetkit";
			$baseurl=get_site_url();
		}
		if ( ($baseurl) && ($baseurl[strlen($baseurl)-1]!='/') )
			$baseurl.='/';
		$info['root_url']=$baseurl;
		
		$needle=$widgetkit_dir_name.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."widgets".DIRECTORY_SEPARATOR;
		$pos=strrpos(__DIR__,$needle);
		if (!$pos){
			$needle=$widgetkit_dir_name.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR;
			$pos=strrpos(__DIR__,$needle);
		}
		if ($pos){
			$info['root']=substr(__DIR__,0,$pos);
			$offset=$pos+strlen($needle);
			$pos2=strpos(__DIR__,DIRECTORY_SEPARATOR,$offset);
			if (!$pos2)
				$info['path']=__DIR__;
			else
				$info['path']=substr(__DIR__,0,$pos2);
			
			$pos=strrpos($info['path'],$widgetkit_dir_name);
			if ($pos)
				$info['relativepath']=substr($info['path'],$pos+strlen($widgetkit_dir_name));
		}
		if ($info['root']){
			$info['wk_path']=$info['root'].$widgetkit_dir_name;
		}
		
		if ($info['path']){
			$f=@file_get_contents($info['path'].DIRECTORY_SEPARATOR.'plugin.php',false,null,0,2400);
			if ( ($f) && (preg_match_all("@^\s*'config'\s*=>\s*array\s*\(.*$@m",$f,$matches,PREG_OFFSET_CAPTURE)) ){
				$offset=$matches[0][0][1];
				if (preg_match_all("@^\s*'label'\s*=>\s*'.*$@m",$f,$matches,PREG_PATTERN_ORDER,$offset)){
					$info['name']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'name'\s*=>\s*'.*$@m",$f,$matches,PREG_PATTERN_ORDER,$offset)){
					$info['codename']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'plugin_version'\s*=>\s*'.*$@m",$f,$matches)){
					$info['version']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'plugin_date'\s*=>\s*'.*$@m",$f,$matches)){
					$info['date']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'plugin_logo'\s*=>\s*'.*$@m",$f,$matches)){
					$info['logo']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'plugin_wiki'\s*=>\s*'.*$@m",$f,$matches)){
					$info['wiki']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'plugin_website'\s*=>\s*'.*$@m",$f,$matches)){
					$info['website']=explode("'",trim($matches[0][0]))[3];
				}
				if (preg_match_all("@^\s*'name'\s*=>\s*'.*$@m",$f,$matches)){
					$raw_name=explode("'",trim($matches[0][0]))[3];
					$this->isWidget = (substr( $raw_name, 0, 7 ) === "widget/");
					$this->isContentProvider = (substr( $raw_name, 0, 8 ) === "content/");
				}
			}
			$url=$appWK['url']->to('widgetkit');
			if ($url){
				if ($url[strlen($url)-1]!='/')
					$info['url']=$url;
				else
					$info['url']=substr($url,0,strlen($url)-1);
				$info['url'].=$info['relativepath'];
			}
		}
		$info['safe_name'] = preg_replace('/[^A-Za-z]/', '', $info['codename']);
		return $info;
	}
	
	//Prints information for the "About" section of the plugin
	//$appWK - is parameter that must be set to $app upon call.
	public function printAboutInfo($appWK){
		$versionWK=htmlspecialchars((isset($appWK['version']))?$appWK['version']:'Unknown');
		$versionDB=htmlspecialchars((isset($appWK['db_version']))?$appWK['db_version']:'Unknown');
		$php_version=htmlspecialchars(@phpversion());
		$phpinfo;
		if (version_compare(self::minPHPVersion,$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your PHP is too old! Upgrade is strongly recommended! This plugin may not work with your version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
		if (version_compare(self::stablePHPVersion,$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your PHP is quite old. Although this plugin can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
			$phpinfo='<span  data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your PHP version is OK.\' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i>'.$php_version.' ('.@php_sapi_name().')</span>';

		$wkinfo;
		if (version_compare(self::minWKVersion,$versionWK)>0)
			$wkinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is too old. Upgrade is strongly recommended. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i>'.$versionWK.'</span>';
		if (version_compare(self::stableWKVersion,$versionWK)>0)
			$wkinfo='<span  data-uk-tooltip class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is quite old. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i>'.$versionWK.'</span>';
		else
			$wkinfo='<span  data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is OK.\' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i>'.$versionWK.'</span>';
		
		$cmsinfo=$this->CMS.' '.$this->CMSVersion;
		
		$accessinfo;
		$accessok=false;
		$item=new WKDiskItem();
		$item->AnalyzeItem($this->plugin_info['path']);
		//Making it beautiful:
		$accesslist=$item->printStructure();
		if ($item->hasWriteAccessProblems())
			$accessinfo='<span class="uk-text-danger"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>'.$appWK['translator']->trans('Failure').'</span>';
		else
			$accessinfo='<span class="uk-text-success"><i class="uk-icon uk-icon-success uk-margin-small-right"></i>'.$appWK['translator']->trans('Ok').'</span>';
		$accessinfo.='<a href="#write-check-'.$this->plugin_info['safe_name'].'" data-uk-modal="{center:true}" class="uk-margin-small-left"><i class="uk-icon-info-circle"></i></a>';
		
		$files=json_encode($item->toArray());
		
		if ($this->pathCorrect)
			$installpath='<span class="uk-text-success" style="word-break:break-all"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>'.$this->plugin_info['path'].'</span>';
		else
			$installpath='<span class="uk-text-danger" style="word-break:break-all"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>'.$this->plugin_info['path'].'</span>';

		$YoothemeProCompatible=($this->useWKPrefix) ? '<span class="uk-text-success"><i class="uk-icon-check uk-margin-small-right"></i>{{ "Yes" |trans}}</span>' : '<span class="uk-text-success"><i class="uk-icon-check uk-margin-small-right"></i>{{ "No" |trans}}</span>';
		
		if (!isset($this->plugin_info['safe_name'])){
			echo <<< EOT
<div class="uk-panel uk-panel-box uk-alert uk-alert-danger"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'Failed to retrieve information' |trans}}</div>;
EOT;
			return;
		}
		
		$canverify=true;
		$filesintegrity='<button class="uk-button uk-button-small"';
		if (!$canverify)
			$filesintegrity.=' disabled';
		else
			$filesintegrity.=' onclick="verifyFiles'.$this->plugin_info['safe_name'].'()"';
		$filesintegrity.='>'.$appWK['translator']->trans('Verify files').'</button>';
	
		echo <<< EOT
<div id="write-check-{$this->plugin_info['safe_name']}" class="uk-modal">
    <div class="uk-modal-dialog">
		<div class="uk-modal-header">
			<h2>{{ 'Details' | trans}}</h2>
		</div>
        <div class="uk-overflow-container">
		{$accesslist}
		</div>
		<div class="uk-modal-footer">
			<div class="uk-text-center">
				<a class="uk-modal-close uk-button uk-button-primary">{{ 'Ok' |trans}}</a>
			</div>
		</div>
    </div>
</div>
<div class="uk-hidden" id="files-{$this->plugin_info['safe_name']}">
{$files}
</div>
<div class="uk-grid">
	<div class="uk-text-center uk-width-medium-1-3" id="logo-{$this->plugin_info['safe_name']}">
	</div>
	<div class="uk-width-medium-2-3">
		<table class="uk-table uk-table-striped">
			<tr>
				<td>
					{{ 'Plugin name' |trans}}
				</td>
				<td id="name-{$this->plugin_info['safe_name']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Plugin version' |trans}}
				</td>
				<td id="version-{$this->plugin_info['safe_name']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Plugin build date' |trans}}
				</td>
				<td id="build-{$this->plugin_info['safe_name']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'CMS' |trans}}
				</td>
				<td id="cms-{$this->plugin_info['safe_name']}">
					{$cmsinfo}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Widgetkit version' |trans}}
				</td>
				<td id="version-wk-{$this->plugin_info['safe_name']}">
					{$wkinfo}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Database version' |trans}}
				</td>
				<td id="version-db-{$this->plugin_info['safe_name']}">
					{$versionDB}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'jQuery version' |trans}}
				</td>
				<td id="version-jquery-{$this->plugin_info['safe_name']}">
					Unknown
				</td>
			</tr>
			<tr>
				<td>
					{{ 'UIkit version' |trans}}
				</td>
				<td>
					<span id="version-uikit-valid-{$this->plugin_info['safe_name']}" data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ 'Your UIkit version is OK.' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i><span class="version-uikit-{$this->plugin_info['safe_name']}">Unknown</span></span>
					<span id="version-uikit-invalid-{$this->plugin_info['safe_name']}" data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ 'Your UIkit version is too old, please upgrade your Widgetkit.' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i><span class="version-uikit-{$this->plugin_info['safe_name']}">Unknown</span></span>
				</td>
			</tr>
			<tr>
				<td>
					{{ 'UIkit version in Widgetkit bundle' |trans}}
				</td>
				<td>
					{$this->UIkitVersion}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Yootheme Pro compatible' |trans}}
				</td>
				<td>
					<span data-uk-tooltip style="margin-top: 5px;" title="{{ 'Widgetkit version 2.9.0 and later are compatible with Yootheme Pro.' |trans}}">
						{$YoothemeProCompatible}
					</span>
				</td>
			</tr>
			<tr>
				<td>
					{{ 'AngularJS version' | trans}}
				</td>
				<td id="version-angularjs-{$this->plugin_info['safe_name']}">
					Unknown
				</td>
			</tr>
			<tr>
				<td>
					{{ 'PHP version' | trans}}
				</td>
				<td>
					{$phpinfo}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Installation path' | trans}}
				</td>
				<td>
					{$installpath}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Write access check' | trans}}
				</td>
				<td>
					{$accessinfo}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Files integrity' | trans}}
				</td>
				<td>
					{$filesintegrity}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Author' |trans}}
				</td>
				<td>
					<a href="https://valitov.me" target="_blank">{{ 'Ramil Valitov' |trans}}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Website' |trans}}
				</td>
				<td id="website-{$this->plugin_info['safe_name']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Wiki and manuals' |trans}}
				</td>
				<td id="wiki-{$this->plugin_info['safe_name']}">
				</td>
			</tr>
		</table>
		<div id="update-{$this->plugin_info['safe_name']}">
			<div id="update-available-{$this->plugin_info['safe_name']}" class="uk-panel uk-panel-box uk-alert-danger uk-text-center update-info-{$this->plugin_info['safe_name']} uk-hidden">
				<h3 class="uk-text-center">
					<i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'This plugin is outdated!' |trans}}
				</h3>
				<h4 class="uk-text-center">
					{{ 'A new version is available. Please, update.' |trans}}
				</h4>
				<button type="button" class="uk-button uk-button-success" id="update-details-{$this->plugin_info['safe_name']}"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'Update details' |trans}}</button>
			</div>
			<div id="update-ok-{$this->plugin_info['safe_name']}" class="uk-panel uk-panel-box uk-alert-success uk-text-center update-info-{$this->plugin_info['safe_name']} uk-hidden">
				<i class="uk-icon uk-icon-check uk-margin-small-right"></i>{{ 'Your version of the plugin is up to date!' |trans}}
			</div>
			<div id="update-problem-{$this->plugin_info['safe_name']}" class="uk-panel uk-panel-box uk-alert-danger uk-text-center update-info-{$this->plugin_info['safe_name']} uk-hidden">
				<i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'Failed to retrieve information about available updates.' |trans}}
			</div>
		</div>
	</div>
</div>
EOT;
}

	//Prints information for the "Newsletter" section of the plugin with subscribe button
	//$appWK - is parameter that must be set to $app upon call.
	public function printNewsletterInfo($appWK){
		$firstName=htmlspecialchars($this->extractWKUserName($appWK['user']));
		$lastName=htmlspecialchars($this->extractWKUserName($appWK['user'],false));
		$email=htmlspecialchars($appWK['user']->getEmail());
		$cms=htmlspecialchars($this->getCMSName());
		$origin=htmlspecialchars($appWK['request']->getBaseUrl());
		$locale=htmlspecialchars($appWK['locale']);
		
		if (!isset($this->plugin_info['safe_name'])){
			echo <<< EOT
<div class="uk-panel uk-panel-box uk-alert uk-alert-danger"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'Failed to retrieve information' |trans}}</div>;
EOT;
			return;
		}
		
		echo <<< EOT
<div class="uk-panel uk-panel-box uk-alert">
	<p>
		<i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'We have different free products that extend functionality of the Widgetkit. Please, subscribe for a newsletter to get notifications about new releases of the current plugin, other widgets that we create, and news when a completely new product for the Widgetkit becomes available.' | trans}}
	</p>
</div>

<div class="uk-text-center">
<button class="uk-button uk-button-success" data-uk-modal="{target:'#{$this->plugin_info['safe_name']}-subscribe'}"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>{{ 'Subscribe' |trans}}</button>
</div>

<div id="{$this->plugin_info['safe_name']}-subscribe" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-overflow-container">
			<div class="uk-panel uk-panel-box uk-alert">
			<i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'Please, fill in all the fields below, then click Submit button' |trans}}
			</div>
			<form class="uk-form uk-form-horizontal" action="https://valitov.us11.list-manage.com/subscribe/post?u=13280b8048b58d2be207f1dd5&amp;id=52d79713c6" method="post" id="form-{$this->plugin_info['safe_name']}-subscribe" target="_blank">
				<fieldset data-uk-margin>
					<legend>{{ 'Subscription form' |trans}}</legend>
					<div class="uk-form-row">
						<label class="uk-form-label" for="form-first-name">{{ 'First name' |trans}}</label>
						<div class="uk-form-controls">
							<input type="text" id="form-first-name" name="FNAME" value="{$firstName}" required="required">
						</div>
					</div>
					<div class="uk-form-row">
						<label class="uk-form-label" for="form-last-name">{{ 'Last name' |trans}}</label>
						<div class="uk-form-controls">
							<input type="text" id="form-last-name" name="LNAME" value="{$lastName}" required="required">
						</div>
					</div>
					<div class="uk-form-row">
						<label class="uk-form-label" for="form-email">{{ 'E-mail' |trans}}</label>
						<div class="uk-form-controls">
							<input type="email" id="form-email" name="EMAIL" value="{$email}" required="required">
						</div>
					</div>
					<div style="position: absolute; left: -5000px;" class="uk-hidden">
						<input type="text" name="b_13280b8048b58d2be207f1dd5_52d79713c6" tabindex="-1" value="">
						<input type="text" name="CMS" value="{$cms}">
						<input type="text" name="ORIGIN" value="{$origin}">
						<input type="text" name="PRODUCT" value="{$this->plugin_info['name']}">
						<input type="text" name="LOCALE" value="{$locale}">
						<input type="text" id="country_code-{$this->plugin_info['safe_name']}" name="COUNTRYID" value="">
						<input type="text" id="country_name-{$this->plugin_info['safe_name']}" name="COUNTRY" value="">
						<input type="text" id="region_code-{$this->plugin_info['safe_name']}" name="REGIONID" value="">
						<input type="text" id="region_name-{$this->plugin_info['safe_name']}" name="REGION" value="">
						<input type="text" id="city-{$this->plugin_info['safe_name']}" name="CITY" value="">
						<input type="text" id="time_zone-{$this->plugin_info['safe_name']}" name="TIMEZONE" value="">
					</div>
				</fieldset>
				<div class="uk-text-right uk-margin">
					<button type="button" class="uk-button uk-modal-close">{{'Close'|trans}}</button>
					<button type="submit" class="uk-button uk-button-primary validate">{{'Subscribe'|trans}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	jQuery('#form-{$this->plugin_info['safe_name']}-subscribe').formchimp();
	
	/*Geolocation if nessesary*/
	jQuery('#{$this->plugin_info['safe_name']}-subscribe').on({
		'show.uk.modal': function(){
			if (!jQuery("#country_code-{$this->plugin_info['safe_name']}").val()){
				jQuery.ajax({
						'url': 'http://ip-api.com/json',
						'type' : "GET",
						'dataType' : 'json',
						success: function (data, textStatus, jqXHR){
							if (data){
								jQuery("#country_code-{$this->plugin_info['safe_name']}").val(data.countryCode);
								jQuery("#country_name-{$this->plugin_info['safe_name']}").val(data.country);
								jQuery("#region_code-{$this->plugin_info['safe_name']}").val(data.region);
								jQuery("#region_name-{$this->plugin_info['safe_name']}").val(data.regionName);
								jQuery("#city-{$this->plugin_info['safe_name']}").val(data.city);
								jQuery("#time_zone-{$this->plugin_info['safe_name']}").val(data.timezone);
							}
						}
				});
			}
		}
	});
</script>

EOT;
	}

	//Prints information for the "Donate" section of the plugin
	//$appWK - is parameter that must be set to $app upon call.
	public function printDonationInfo($appWK){
		echo <<< EOT
<div class="uk-panel uk-panel-box uk-alert">
	<p>
		<i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'If you like this module, please, donate. It will help to support the project and improve it. You can choose any suitable payment method and donate any amount. Thank you!' | trans}}
	</p>
</div>

<div class="uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4 uk-margin-top" data-uk-grid-match="{target:'.uk-panel'}">
	<div>
		<div class="uk-panel uk-panel-box uk-text-center uk-margin-bottom">
			<p class="uk-panel-title">{{ 'Euro' |trans}} <i class="uk-icon uk-icon-euro"></i></p>
			<p>{{ 'Payment methods:' |trans}}</p>
			<ul style="list-style-type: none;">
				<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BJJF3E6DBRYHA" target="_blank"><i class="uk-icon uk-icon-credit-card"></i> {{ 'Bank card' |trans}}</a></li>
				<li><a href="https://www.paypal.me/valitov/0eur" target="_blank"><i class="uk-icon uk-icon-paypal"></i> {{ 'PayPal' |trans}}</a></li>
			</ul>
		</div>
	</div>
	<div>
		<div class="uk-panel uk-panel-box uk-text-center uk-margin-bottom">
			<p class="uk-panel-title">{{ 'USD' |trans}} <i class="uk-icon uk-icon-usd"></i></p>
			<p>{{ 'Payment methods:' |trans}}</p>
			<ul style="list-style-type: none;">
				<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B8VMNU7SEAU8J" target="_blank"><i class="uk-icon uk-icon-credit-card"></i> {{ 'Bank card' |trans}}</a></li>
				<li><a href="https://www.paypal.me/valitov/0usd" target="_blank"><i class="uk-icon uk-icon-paypal"></i> {{ 'PayPal' |trans}}</a></li>
			</ul>
		</div>
	</div>
	<div>
		<div class="uk-panel uk-panel-box uk-text-center uk-margin-bottom">
			<p class="uk-panel-title">{{ 'Russian ruble' |trans}} <i class="uk-icon uk-icon-rouble"></i></p>
			<p>{{ 'Payment methods:' |trans}}</p>
			<ul style="list-style-type: none;">
				<li><a href="https://money.yandex.ru/to/410011424143476" target="_blank"><i class="uk-icon uk-icon-credit-card"></i> {{ 'Bank card' |trans}}</a></li>
				<li><a href="https://www.paypal.me/valitov/0rub" target="_blank"><i class="uk-icon uk-icon-paypal"></i> {{ 'PayPal' |trans}}</a></li>
				<li><a href="https://money.yandex.ru/to/410011424143476" target="_blank">{{ 'Yandex Money' |trans}}</a></li>
			</ul>
		</div>
	</div>
	<div>
		<div class="uk-panel uk-panel-box uk-text-center uk-margin-bottom">
			<p class="uk-panel-title">{{ 'Other currencies' |trans}}</p>
			<p>{{ 'Payment methods:' |trans}}</p>
			<ul style="list-style-type: none;">
				<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BJJF3E6DBRYHA" target="_blank"><i class="uk-icon uk-icon-credit-card"></i> {{ 'Bank card' |trans}}</a></li>
				<li><a href="https://www.paypal.me/valitov" target="_blank"><i class="uk-icon uk-icon-paypal"></i> {{ 'PayPal' |trans}}</a></li>
			</ul>
		</div>
	</div>
</div>

EOT;
	}
	
	//Generates and returns Javascript code (without <script> tags) used for checking updates
	//$appWK - is parameter that must be set to $app upon call.
	//$settings - array that contains info about the installed plugin. Meaning of the keys:
	//git - URL to the Git repository
	//api - URL to the Git Api
	//infotimeout - timeout of visibilty of the update notification alert
	//name - name of the plugin
	//version - version of the plugin
	//distr_name - distr name (codename)
	//date - build date of the plugin
	//logo - absolute URL to logo of the plugin
	//wiki - absolute URL to wiki of the plugin
	//website - absolute URL to website of the plugin
	//If some fields are missing, then this function tries to detect them
	public function generateUpdaterJS($appWK,$settings=array()){
		if (!is_array($settings))
			$settings=array();
		if (!isset($settings['git']))
			$settings['git']='https://github.com/rvalitov/';
		else
			$settings['git']=htmlspecialchars($settings['git']);
		if (!isset($settings['api']))
			$settings['api']='https://api.github.com/repos/rvalitov/';
		else
			$settings['api']=htmlspecialchars($settings['api']);
		if ( (!isset($settings['infotimeout'])) || (!is_integer($settings['infotimeout'])) )
			$settings['infotimeout']=5000;
		//Checking for minimum set of required fields:
		if ( (!isset($this->plugin_info['name'])) || (!isset($this->plugin_info['version'])) || (!isset($this->plugin_info['codename'])) || ($this->plugin_info['safe_name']=='') )
			return '';
		if (!isset($settings['name']))
			$settings['name']=$this->plugin_info['name'];
		else
			$settings['name']=htmlspecialchars($settings['name']);
		if (!isset($settings['version']))
			$settings['version']=$this->plugin_info['version'];
		else
			$settings['version']=htmlspecialchars($settings['version']);
		if (!isset($settings['distr_name']))
			$settings['distr_name']='widgetkit-'.str_replace('_','-',$this->plugin_info['codename']);
		else
			$settings['distr_name']=htmlspecialchars($settings['distr_name']);
		if (!isset($settings['date']))
			$settings['date']=$this->plugin_info['date'];
		else
			$settings['date']=htmlspecialchars($settings['date']);
		if (!isset($settings['logo']))
			if (!isset($this->plugin_info['logo']))
				$settings['logo']='https://raw.githubusercontent.com/wiki/rvalitov/'.$settings['distr_name'].'/images/logo.jpg';
			else
				$settings['logo']=htmlspecialchars($this->plugin_info['logo']);
		else
			$settings['logo']=htmlspecialchars($settings['logo']);
		if (!isset($settings['wiki']))
			$settings['wiki']=$settings['git'].$settings['distr_name'].'/wiki';
		else
			$settings['wiki']=htmlspecialchars($settings['wiki']);
		if (!isset($settings['website']))
			$settings['website']=$settings['git'].$settings['distr_name'];
		else
			$settings['website']=htmlspecialchars($settings['website']);
		
		$plugin_update_tag='#update-'.$settings['distr_name'];
		
		//For JS we must espace single quote character:
		$modal=addcslashes($this->generateUpdateInfoDialog($appWK),"'");
		
		$configfile=$this->getPluginURL().'/config.json';
		$minUIkitVersion=self::minUIkitVersion;
		$js = <<< EOT
	function verifyFiles{$this->plugin_info['safe_name']}(){
		var modal = UIkit.modal.blockUI('<h2 class="uk-text-center uk-text-muted">{$appWK['translator']->trans('Please, wait...')}<i class="uk-icon-spinner uk-margin-left uk-icon-spin uk-icon-medium"></h2>',{'center':true});
		jQuery.ajax({
			'url': '{$settings['api']}{$settings['distr_name']}/tags',
			'dataType' : 'json',
			'type' : "GET",
			success: function (data, textStatus, jqXHR){
				if (data){
					var found=false;
					jQuery.each(data, function (index, value){
						if (value.name=='{$settings['version']}'){
							var filesTree='{$settings['api']}{$settings['distr_name']}/git/trees/'+value.commit.sha+'?recursive=1';
							found=true;
							jQuery.ajax({
								'url': filesTree,
								'dataType' : 'json',
								'type' : "GET",
								success: function (data, textStatus, jqXHR){
									if (data){
										var error_list='';
										try {
											var localfiles=JSON.parse(jQuery('#files-{$this->plugin_info['safe_name']}').html());
										}
										catch(err) {
											UIkit.modal.alert('{$appWK['translator']->trans('Failed to parse JSON')}',{'center':true});
											return;
										}
										jQuery.each(data.tree, function (index, value){
											if ( (value.type=='blob') && (value.path.indexOf('{$this->plugin_info['safe_name']}/')==0) ){
												var isvalid=false;
												var isfound=false;
												var localsha='';
												var localsize=0;
												
												jQuery.each(localfiles, function (indexfile, valuefile){
													if (valuefile.name==value.path){
														isfound=true;
														localsha=valuefile.hash;
														localsize=valuefile.size;
													}														
												});
												if (isfound){
													if ( (localsize!=value.size) || (localsha!=value.sha) )
														error_list+='<tr><td>'+value.path+'</td><td>{$appWK['translator']->trans('File is altered')}</td></tr>';
												}
												else
													error_list+='<tr><td>'+value.path+'</td><td>{$appWK['translator']->trans('File is missing')}</td></tr>';
											}
										});
										modal.hide();
										if (error_list)
											UIkit.modal.alert('<div class="uk-overflow-container"><table class="uk-table"><thead><tr><th>{$appWK['translator']->trans('File')}</th><th>{$appWK['translator']->trans('Problem')}</th></tr></thead><tbody>'+error_list+'</tbody></table></div>',{'center':true});
										else
											UIkit.modal.alert('{$appWK['translator']->trans('No problems detected')}',{'center':true});
									}
									else{
										modal.hide();
										UIkit.modal.alert("{$appWK['translator']->trans('Couldn\'t retrieve information about files of your release')}",{'center':true});
									}
								},
								error: function (jqXHR, textStatus, errorThrown ){
									modal.hide();
									UIkit.modal.alert("{$appWK['translator']->trans('Failed to get information from server')}",{'center':true});
								}
							});
						}
					});
					if (!found){
						modal.hide();
						UIkit.modal.alert("{$appWK['translator']->trans('Information about your release is not available. The files can\'t be verified.')}",{'center':true});
					}
				}
				else{
					modal.hide();
					UIkit.modal.alert("{$appWK['translator']->trans('Failed to get information from server')}",{'center':true});
				}
			},
			error: function (jqXHR, textStatus, errorThrown ){
				modal.hide();
				UIkit.modal.alert("{$appWK['translator']->trans('Failed to get information from server')}",{'center':true});
			}
		});
	}
	
jQuery(document).ready(function(\$){
	/* Display modal dialog with update info */
	function showUpdateInfo(urlDownload,buildDate,buildVersion,releaseInfo){
		var modaltext='{$modal}';
		modaltext=modaltext.replace('%URL_DOWNLOAD%',urlDownload);
		modaltext=modaltext.replace('%DATE_REMOTE%',buildDate);
		modaltext=modaltext.replace('%VERSION_REMOTE%',buildVersion);
		modaltext=modaltext.replace('%RELEASE_INFO%',releaseInfo);
		modaltext=modaltext.replace(/(\\r|\\n)/gm,'');
		UIkit.modal.alert(modaltext,{"center":true}); 
	}
	
	/* Filling the about info */
	\$('#name-{$this->plugin_info['safe_name']}').waitUntilExists(function(){
		\$('#name-{$this->plugin_info['safe_name']}').empty();
		\$('#name-{$this->plugin_info['safe_name']}').append('{$settings['name']}');
		
		\$('#build-{$this->plugin_info['safe_name']}').empty();
		\$('#build-{$this->plugin_info['safe_name']}').append('{$settings['date']}');
		
		\$('#website-{$this->plugin_info['safe_name']}').empty();
		\$('#website-{$this->plugin_info['safe_name']}').append('<a href="{$settings['website']}" target="_blank">{$settings['website']}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>');
		
		\$('#version-{$this->plugin_info['safe_name']}').empty();
		\$('#version-{$this->plugin_info['safe_name']}').append('{$settings['version']}');
		
		\$('#logo-{$this->plugin_info['safe_name']}').empty();
		\$('#logo-{$this->plugin_info['safe_name']}').append('<img class="uk-width-1-1" src="{$settings['logo']}" style="max-width:300px;">');
		
		\$('#wiki-{$this->plugin_info['safe_name']}').empty();
		\$('#wiki-{$this->plugin_info['safe_name']}').append('<a href="{$settings['wiki']}" target="_blank">{$settings['wiki']}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>');
		
		\$('#version-jquery-{$this->plugin_info['safe_name']}').empty();
		\$('#version-jquery-{$this->plugin_info['safe_name']}').append(\$.fn.jquery);
		
		if (UIkit && UIkit.version){
			\$('.version-uikit-{$this->plugin_info['safe_name']}').empty();
			\$('.version-uikit-{$this->plugin_info['safe_name']}').append(UIkit.version);
			\$('#version-uikit-valid-{$this->plugin_info['safe_name']}').removeClass("uk-hidden");
			\$('#version-uikit-invalid-{$this->plugin_info['safe_name']}').removeClass("uk-hidden");
			if (versioncompare(UIkit.version,"{$minUIkitVersion}")<0)
				\$('#version-uikit-valid-{$this->plugin_info['safe_name']}').addClass("uk-hidden");
			else
				\$('#version-uikit-invalid-{$this->plugin_info['safe_name']}').addClass("uk-hidden");
		}
		
		if (angular && angular.version && angular.version.full){
			\$('#version-angularjs-{$this->plugin_info['safe_name']}').empty();
			\$('#version-angularjs-{$this->plugin_info['safe_name']}').append(angular.version.full);
		}
		
		var configfile="{$configfile}";
		\$.ajax({
			'url': configfile,
			'type' : "GET",
			'dataType' : 'json',
			'cache': false,
			success: function (data, textStatus, jqXHR){
				if (data){
					/*Update all elements*/
					\$.each(data, function (index, value){
						var e=\$('[ng-model="widget.data.global[\''+index+'\']"]');
						console.info("Updated global settings for option "+index+" ("+e.length+" items)");
						e.val(value);
						e.trigger( "change" );
					});
				}
			}
		});
	});

	function printNiceDate(MyDate,dateSeparator){
		if (typeof dateSeparator!='string'){
			dateSeparator='/';
		}
		return ('0' + MyDate.getDate()).slice(-2) + dateSeparator + ('0' + (MyDate.getMonth()+1)).slice(-2) + dateSeparator + MyDate.getFullYear();
	}
	function failedToUpdate(){
		$(widget_update_tag).waitUntilExists(function(){
			\$('div.update-info-{$this->plugin_info['safe_name']}').addClass('uk-hidden');
			\$('#update-problem-{$this->plugin_info['safe_name']}').removeClass('uk-hidden');
		});
	}
	
	/*We only show check for updates on the Widgetkit page*/
	if (!( (window.location.href.indexOf('com_widgetkit')>0) || (window.location.href.indexOf('page=widgetkit')>0) ))
		return;
	
	\$.ajax({
			'url': '{$settings['api']}{$settings['distr_name']}/releases/latest',
			'type' : "GET",
			'cache':false,
			'dataType' : 'json',
			success: function (data, textStatus, jqXHR){
				if (data){
					if (versioncompare('{$settings['version']}',data.tag_name)<0){
						var date_remote = Date.parse(data.published_at);
						if (date_remote>0){
							date_remote=printNiceDate(new Date(date_remote));
						}
						else {
							date_remote='';
						}
						var infotext='<div class="wk-noconflict"><p class="uk-margin-remove"><i class="uk-icon-info-circle uk-margin-small-right"></i>{$appWK['translator']->trans('New release of plugin %name% is available!',array('%name%' => $settings['name']))} {$appWK['translator']->trans('Version')} '+data.tag_name+'.</p><p class="uk-text-center uk-margin-remove"><button class="uk-button uk-button-mini uk-button-success" id="info-{$settings['distr_name']}">{$appWK['translator']->trans('Update details')}</button></p></div>';
						
						UIkit.notify(infotext, {'timeout':{$settings['infotimeout']},'pos':'top-center','status':'warning'});
						\$('#info-{$settings['distr_name']}').click(function(){
								showUpdateInfo(data.html_url,date_remote,data.tag_name,marked(data.body));
						});
						\$('#update-{$this->plugin_info['safe_name']}').waitUntilExists(function(){
							\$('div.update-info-{$this->plugin_info['safe_name']}').addClass('uk-hidden');
							\$('#update-available-{$this->plugin_info['safe_name']}').removeClass('uk-hidden');
							
							\$('#version-local-{$this->plugin_info['safe_name']}').empty();
							\$('#version-local-{$this->plugin_info['safe_name']}').append('{$settings['version']}');
							
							\$('#version-remote-{$this->plugin_info['safe_name']}').empty();
							\$('#version-remote-{$this->plugin_info['safe_name']}').append(data.tag_name);
							
							\$('#date-local-{$this->plugin_info['safe_name']}').empty();
							\$('#date-local-{$this->plugin_info['safe_name']}').append('{$settings['date']}');
							
							\$('#date-remote-{$this->plugin_info['safe_name']}').empty();
							if (date_remote.length)
								\$('#date-remote-{$this->plugin_info['safe_name']}').append(date_remote);
							
							\$('#release-info-{$this->plugin_info['safe_name']}').empty();
							\$('#release-info-{$this->plugin_info['safe_name']}').append(marked(data.body));
							
							\$('#update-logo-{$this->plugin_info['safe_name']}').attr('src','{$settings['logo']}');
							
							\$('#download-{$this->plugin_info['safe_name']}').attr('href',data.html_url);
							
							\$('#instructions-{$this->plugin_info['safe_name']}').attr('href','{$settings['wiki']}');
							
							\$('#update-details-{$this->plugin_info['safe_name']}').click(function(){
								showUpdateInfo(data.html_url,date_remote,data.tag_name,marked(data.body));
							});
						});
					}
					else{
						\$('#update-{$this->plugin_info['safe_name']}').waitUntilExists(function(){
							\$('div.update-info-{$this->plugin_info['safe_name']}').addClass('uk-hidden');
							\$('#update-ok-{$this->plugin_info['safe_name']}').removeClass('uk-hidden');
						});
					}
				}
				else{
					failedToUpdate();
				}
			},
			error: function (jqXHR, textStatus, errorThrown ){
				failedToUpdate();
			}
		});
});
EOT;
		return $js;
	}

	//Generates code of modal dialog that shows information about available update of the plugin
	//$appWK - is parameter that must be set to $app upon call.
	//$name - name of the plugin
	//$version - version of the installed plugin
	//$date - build date of the installed plugin
	//$wiki - absolute URL to wiki of the installed plugin
	//$logo - absolute URL to logo of the installed plugin
	private function generateUpdateInfoDialog($appWK){
		$js = <<< EOT
<div class="uk-modal-header">
	<h1>{$appWK['translator']->trans('%name% plugin update details',array('%name%' => $this->plugin_info['name']))}</h1>
</div>
<div class="uk-overflow-container">
	<div class="uk-grid">
		<div class="uk-width-1-3 uk-text-center">
			<img src="{$this->plugin_info['logo']}">
		</div>
		<div class="uk-width-2-3">
			<table class="uk-table">
				<tr>
					<th>
						&nbsp;
					</th>
					<th>
						{$appWK['translator']->trans('Installed')}
					</th>
					<th>
						{$appWK['translator']->trans('Available')}
					</th>
				</tr>
				<tr>
					<td>
						{$appWK['translator']->trans('Version')}
					</td>
					<td>
						{$this->plugin_info['version']}
					</td>
					<td>
						%VERSION_REMOTE%
					</td>
				</tr>
				<tr>
					<td>
						{$appWK['translator']->trans('Build date')}
					</td>
					<td>
						{$this->plugin_info['date']}
					</td>
					<td>
						%DATE_REMOTE%
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<hr>
	<h2>
		{$appWK['translator']->trans('Release information')}
	</h2>
	<div>
		%RELEASE_INFO%
	</div>
	<hr>
	<h2>
		{$appWK['translator']->trans('How to update')}
	</h2>
	<div class="uk-grid uk-grid-width-1-2">
		<div class="uk-text-center">
			<a class="uk-button uk-button-success" target="_blank" href="%URL_DOWNLOAD%"><i class="uk-icon uk-icon-external-link uk-margin-small-right"></i>{$appWK['translator']->trans('Download page')}</a>
		</div>
		<div class="uk-text-center">
			<a class="uk-button" target="_blank" href="{$this->plugin_info['wiki']}"><i class="uk-icon uk-icon-external-link uk-margin-small-right"></i>{$appWK['translator']->trans('Instructions')}</a>
		</div>
	</div>
</div>
EOT;
		return str_replace(array("\r","\n"),"",$js);
	}

	//Returns a string that contains dumb of the $var.
	//This function is better than print_r because it controls the depth ($max_count), and doesn't cause reaching the memory limit error.
	public static function features_var_export($var, $prefix = '', $init = TRUE, $count = 0, $max_count=5) {
		if ($count > $max_count) {
			// Recursion depth reached.
			return '...';
		}

		if (is_object($var)) {
			$output = method_exists($var, 'export') ? $var->export() : self::features_var_export((array) $var, '', FALSE, $count+1);
		}
		else if (is_array($var)) {
			if (empty($var)) {
				$output = 'array()';
			}
			else {
			$output = "array(\n";
			foreach ($var as $key => $value) {
				// Using normal var_export on the key to ensure correct quoting.
				$output .= "  " . var_export($key, TRUE) . " => " . self::features_var_export($value, '  ', FALSE, $count+1) . ",\n";
			}
			$output .= ')';
			}
		}
		else if (is_bool($var)) {
			$output = $var ? 'TRUE' : 'FALSE';
		}
		else if (is_int($var)) {
			$output = intval($var);
		}
		else if (is_numeric($var)) {
			$floatval = floatval($var);
			if (is_string($var) && ((string) $floatval !== $var)) {
			  // Do not convert a string to a number if the string
			  // representation of that number is not identical to the
			  // original value.
			  $output = var_export($var, TRUE);
			}
			else {
			$output = $floatval;
			}
		}
		else if (is_string($var) && strpos($var, "\n") !== FALSE) {
			// Replace line breaks in strings with a token for replacement
			// at the very end. This protects whitespace in strings from
			// unintentional indentation.
			$var = str_replace("\n", "***BREAK***", $var);
			$output = var_export($var, TRUE);
		}
		else {
			$output = var_export($var, TRUE);
		}

		if ($prefix) {
			$output = str_replace("\n", "\n$prefix", $output);
		}

		if ($init) {
			$output = str_replace("***BREAK***", "\n", $output);
		}

		return $output;
	}
	
	//Returns an array with items from $array that have keys listed in $list.
	public static function intersectArrayItems($array,$list){
		if (!is_array($list))
			return '';
		$s=array();
		for ($i=0; $i<sizeof($list); $i++)
			$s[$list[$i]]=(isset($array[$list[$i]])?($array[$list[$i]]):null);
		return $s;
	}
	
	//Reads global settings for this plugin
	public function readGlobalSettings(){
		$path=$this->getPluginDirectory();
		if (!$path)
			return array();
		$name=$path.DIRECTORY_SEPARATOR."config.json";
		if (!file_exists($name))
			return array();
		$data=@file_get_contents($name);
		if ($data===false)
			return array();
		$data=@json_decode($data,true);
		if (!$data)
			return array();
		return $data;
	}
	
	//Saves global settings for this plugin
	public function saveGlobalSettings($settings){
		if (!is_array($settings))
			return false;
		$path=$this->getPluginDirectory();
		if (!$path)
			return false;
		$name=$path.DIRECTORY_SEPARATOR."config.json";
		$data=@json_encode($settings);
		if (!$data)
			return false;
		return (@file_put_contents($name,$data)!==false);
	}
	
	//Adds a string to the list of debug strings with "info" debug level
	public function addInfoString($s){
		array_push($this->debug_info,$s);
	}
	
	//Adds a string to the list of debug strings with "warning" debug level
	public function addWarningString($s){
		array_push($this->debug_warning,$s);
	}
	
	//Adds a string to the list of debug strings with "error" debug level
	public function addErrorString($s){
		array_push($this->debug_error,$s);
	}
	
	public function printDebugStrings(){
echo <<< EOT
if (typeof console.groupCollapsed === "function")
	console.groupCollapsed('{$this->plugin_info['name']} #{$this->id}');
else if (typeof console.group === "function")
	console.group('{$this->plugin_info['name']} #{$this->id}');
EOT;
		$this->printJSDebugText($this->debug_info,1);
		$this->printJSDebugText($this->debug_warning,2);
		$this->printJSDebugText($this->debug_error,3);
echo <<< EOT
if (typeof console.groupEnd === "function")
	console.groupEnd();
EOT;
	}
	
	/*
	Returns true, if the data is suitable for output as a table. Used for debug, see the console.table command.
	*/
	public static function isDataForTable($array){
		if ( (!is_array($array)) || (sizeof($array)<1) )
			return false;
		$count=-1;
		foreach ($array as $value){
			if (!is_array($value))
				return false;
			if ($count<0)
				$count=sizeof($value);
			else
				if ($count!=sizeof($value))
					return false;
		}
		return true;
	}
	
	/*
	Converts the contents of $value into JSON format that can be later parsed by the browser using Javascript
	*/
	public static function EncodeDataJson($value){
		$result=json_encode($value,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP);
		if (!$result){
			error_log("Failed to JSON encode data, error code ".json_last_error());
			return '';
		}
		if (is_string($result)){
			$result=addslashes($result);
			$result=preg_replace("/\r\n|\r|\n/", "\\n",$result);
		}
		return $result;
	}
	
	/*
	Prints debug info string for JS console output
	$typeid defines the log warning level
	*/
	private function printJSDebugString($s, $typeid=1){
		//We don't use prefix anymore, because good browsers can collapse output in groups.
		//$prefix='['.$this->plugin_info['name'].' #'.$this->id.'] ';
		$prefix='';
		$datatable=self::isDataForTable($s);
		if ($datatable){
		echo <<< EOT
if (typeof console.table === "function"){
	var data_list=[];
EOT;
		foreach ($s as $value){
			echo "try { data_list.push(JSON.parse('".self::EncodeDataJson($value)."')); } catch(err) { console.error('Failed to parse JSON: '+err); ";
			$this->printJSDebugString(self::features_var_export($value, 3));
			echo "}";
		}
		echo <<< EOT
	console.table(data_list);
}
else {
EOT;
		}
		if (is_string($s)){
			$s=addslashes($s);
			$s=preg_replace("/\r\n|\r|\n/", "\\n",$s);
		}
		switch($typeid){
			case 1:
				if (is_string($s))
					echo "console.info('".$prefix.$s."');";
				else{
					echo "try {console.info(JSON.parse('".self::EncodeDataJson($s)."')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
					$this->printJSDebugString(self::features_var_export($s), 3);
					echo "}";
				}
				break;
			case 2:
				if (is_string($s))
					echo "console.warn('".$prefix.$s."');";
				else{
					echo "try {console.warn(JSON.parse('".self::EncodeDataJson($s)."')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
					$this->printJSDebugString(self::features_var_export($s), 3);
					echo "}";
				}
				break;
			case 3:
				if (is_string($s))
					echo "console.error('".$prefix.$s."');";
				else{
					echo "try {console.error(JSON.parse('".self::EncodeDataJson($s)."')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
					$this->printJSDebugString(self::features_var_export($s), 3);
					echo "}";
				}
				break;
			default:
				if (is_string($s))
					echo "console.log('".$prefix.$s."');";
				else{
					echo "try {console.log(JSON.parse('".self::EncodeDataJson($s)."')); } catch (err) { console.error('Failed to parse JSON: '+err); ";
					$this->printJSDebugString(self::features_var_export($s), 3);
					echo "}";
				}
				break;
		}
		if ($datatable)
			echo '}';
	}

	/*
	Prints debug info strings (array) for JS console output
	$typeid defines the log warning level
	*/
	private function printJSDebugText($arrayStrings, $typeid=1){
		foreach ($arrayStrings as $s){
			$this->printJSDebugString($s,$typeid);
		}
	}
	
	//UTF8 safe basename function
	public static function mb_basename($path, $suffix = null) {
		$split = preg_split('/\\'.DIRECTORY_SEPARATOR.'/', rtrim($path, DIRECTORY_SEPARATOR.' '));
		return substr(basename('X' . $split[count($split) - 1], $suffix), 1);
	}
}

}
?>