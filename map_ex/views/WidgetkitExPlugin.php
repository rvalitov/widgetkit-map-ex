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

class WidgetkitExPlugin{
	
	private $plugin_info;
	
	private $isWidget=false;
	private $isContentProvider=false;
	
	//Below are the versions of PHP and Widgetkit that are OK
	const minPHPVersion='5.3';
	const stablePHPVersion='5.6';
	const minWKVersion='2.5.0';
	const stableWKVersion='2.6.0';

	//Unique id of the plugin, usually this id is used as HTML id
	private $id;
	
	//The 3 arrays below contain strings that will be used for console log (JS) output, see usage example in the MapEx widget.
	private $debug_info = array();
	private $debug_warning = array();
	private $debug_error = array();
	
	//true, if current CMS is Joomla
	private $isJoomla;
	
	//Version of CMS
	private $CMS;
	
	public function __construct($appWK,$id=0){
		$this->plugin_info=$this->getWKPluginInfo($appWK);
		
		$this->id=$id;
		
		$this->isJoomla=WidgetkitExPlugin::IsJoomlaInstalled();
		if ($this->isJoomla)
			$this->CMS=WidgetkitExPlugin::getJoomlaVersion();
		else
			$this->CMS=WidgetkitExPlugin::getWPVersion();
		
		$wk_version=WidgetkitExPlugin::getWKVersion();
		$php_version=@phpversion();
		array_push($this->debug_info,'Processing widget '.$this->plugin_info['name'].' (version '.$this->plugin_info['version'].') on '.$CMS.' with Widgetkit '.$wk_version.' and PHP '.$php_version.'('.@php_sapi_name().')');
		if (version_compare($this->minPHPVersion,$php_version)>0)
			array_push($this->debug_error,'Your PHP is too old! Upgrade is strongly required! This widget may not work with your version of PHP.');
		else
			if (version_compare($this->stablePHPVersion,$php_version)>0)
				array_push($this->debug_warning,'Your PHP is quite old. Although this widget can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.');
			
		if (version_compare($this->minWKVersion,$wk_version)>0)
			array_push($this->debug_warning,"Your Widgetkit version is quite old. Although this widget may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit. Besides, you may experience some issues of missing options in the settings of this widget if you don't upgrade.");

		array_push($this->debug_info,'Host: '.@php_uname());
		$ipath=$this->plugin_info['path'];
		array_push($this->debug_info,'Widget installation path: '.$ipath);
		if ($this->isJoomla)
			if (preg_match_all('@.*\/administrator\/components\/com_widgetkit\/plugins\/widgets\/.+@',$ipath))
				array_push($this->debug_info,'Installation path is correct');
			else
				array_push($this->debug_error,'Installation path is not correct, please fix it. Read more in the Wiki.');
		else
			if (preg_match_all('@.*\/wp-content\/plugins\/widgetkit\/plugins\/widgets\/.+@',$ipath))
				array_push($this->debug_info,'Installation path is correct');
			else
				array_push($this->debug_warning,'Installation path is not correct, please fix it. Read more in the Wiki.');

		if ($this->isJoomla)
			array_push($this->debug_info,'Detected CMS: Joomla');
		else
			array_push($this->debug_info,'Detected CMS: WordPress');
	}

	//If $firstName=true, then returns first name of the current user or empty string if the first name is unkown
	//If $firstName=false, then returns second name of the current user or empty string if the second name is unkown
	//$widgetkit_user - is parameter that must be set to $app['user'] upon call.
	private static function extractWKUserName($widgetkit_user,$firstName=true){
		$name=trim($widgetkit_user->getName());
		//There is a bug in Widgetkit - it doesn't get the name of the user
		if (!$name){
			//For Joomla:
			if (WidgetkitExPlugin::IsJoomlaInstalled()) {
				$user=\JFactory::getUser($widgetkit_user->getId());
				if ($user)
					$name=$user->name;
			}
			//TODO: add equivalent approach for WP
		}
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
	
	//Returns CMS version
	public function getCMSVersion(){
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
	public static function getJoomlaVersion(){
		$f=@file_get_contents(__DIR__ .'/../../../../../../../libraries/cms/version/version.php',false,null,0,3400);
		if (!$f)
			return "";

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

	//Returns WordPress version or empty string if failed
	public static function getWPVersion(){
		$f=@file_get_contents(__DIR__ .'/../../../../../../../wp-includes/version.php',false,null,0,1400);
		if (!$f)
			return "";
		
		if (preg_match_all("@.*\\\$wp_version\s*=\s*'.+';@",$f,$matches))
			$v.=explode("'",$matches[0][0],3)[1];
		return trim($v);
	}

	//Returns Widgetkit version or empty string if failed
	public static function getWKVersion(){
		$f=@file_get_contents(__DIR__ .'/../../../../config.php',false,null,0,1400);
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
	
	//Returns array with info about current plugin (no matter if it's a widget or a content provider). It works only for custom plugins that are created with updater.js file.
	//The array contains following fields:
	//name - the name of the plugin or empty string if unknown.
	//version - the version of the plugin or empty string if unknown.
	//codename - the name of the distro (codename) or empty string if unknown.
	//date - the release date of the plugin or empty string if unknown.
	//logo - the absolute URL of the logo of the plugin or empty string if unknown.
	//wiki - the absolute URL of wiki (manual) for the plugin or empty string if unknown.
	//website - the absolute URL of website for the plugin or empty string if unknown.
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
			'path'=>'',
			'relativepath'=>'',
			'url'=>''
		];
		
		//We perform a sequental scan of parent directories of the current script to find the plugin install directory
		$needle=DIRECTORY_SEPARATOR."com_widgetkit".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."widgets".DIRECTORY_SEPARATOR;
		$pos=strrpos(__DIR__,$needle);
		$this->isWidget=(boolean)$pos;
		if (!$pos){
			$this->isWidget=false;
			$needle=DIRECTORY_SEPARATOR."com_widgetkit".DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR;
			$pos=strrpos(__DIR__,$needle);
			$this->isContentProvider=(boolean)$pos;
		}
		if ($pos){
			$offset=$pos+strlen($needle);
			$pos2=strpos(__DIR__,DIRECTORY_SEPARATOR,$offset);
			if (!$pos2)
				$info['path']=__DIR__;
			else
				$info['path']=substr(__DIR__,0,$pos2);
			$needle=DIRECTORY_SEPARATOR."com_widgetkit".DIRECTORY_SEPARATOR;
			$info['relativepath']=substr(__DIR__,$pos+strlen($needle),$pos2-($pos+strlen($needle)));
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
			}
			$url=$appWK['url']->to('widgetkit');
			if ($url)
				$info['url']=$url.'/'.$info['relativepath'];
		}
		return $info;
	}

	//Prints information for the "About" section of the plugin
	//$appWK - is parameter that must be set to $app upon call.
	public function printAboutInfo($appWK){
		$versionWK=htmlspecialchars((isset($appWK['version']))?$appWK['version']:'Unknown');
		$versionDB=htmlspecialchars((isset($appWK['db_version']))?$appWK['db_version']:'Unknown');
		$php_version=htmlspecialchars(@phpversion());
		$phpinfo;
		if (version_compare($this->minPHPVersion,$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your PHP is too old! Upgrade is strongly recommended! This plugin may not work with your version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
		if (version_compare($this->stablePHPVersion,$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your PHP is quite old. Although this plugin can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
			$phpinfo='<span  data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your PHP version is OK.\' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i>'.$php_version.' ('.@php_sapi_name().')</span>';

		$wkinfo;
		if (version_compare($this->minWKVersion,$versionWK)>0)
			$wkinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is too old. Upgrade is strongly recommended. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i>'.$versionWK.'</span>';
		if (version_compare($this->stableWKVersion,$versionWK)>0)
			$wkinfo='<span  data-uk-tooltip class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is quite old. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i>'.$versionWK.'</span>';
		else
			$wkinfo='<span  data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is OK.\' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i>'.$versionWK.'</span>';
		
		if (!isset($this->plugin_info['codename'])){
			echo <<< EOT
<div class="uk-panel uk-panel-box uk-alert uk-alert-danger"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'Failed to retrieve information' |trans}}</div>;
EOT;
			return;
		}
	
		echo <<< EOT
<div class="uk-grid">
	<div class="uk-text-center uk-width-medium-1-3" id="logo-{$this->plugin_info['codename']}">
	</div>
	<div class="uk-width-medium-2-3">
		<table class="uk-table uk-table-striped">
			<tr>
				<td>
					{{ 'Plugin name' |trans}}
				</td>
				<td id="name-{$this->plugin_info['codename']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Plugin version' |trans}}
				</td>
				<td id="version-{$this->plugin_info['codename']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Plugin build date' |trans}}
				</td>
				<td id="build-{$this->plugin_info['codename']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Widgetkit version' |trans}}
				</td>
				<td id="version-wk-{$this->plugin_info['codename']}">
					{$wkinfo}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Database version' |trans}}
				</td>
				<td id="version-db-{$this->plugin_info['codename']}">
					{$versionDB}
				</td>
			</tr>
			<tr>
				<td>
					{{ 'jQuery version' |trans}}
				</td>
				<td id="version-jquery-{$this->plugin_info['codename']}">
					Unknown
				</td>
			</tr>
			<tr>
				<td>
					{{ 'UIkit version' |trans}}
				</td>
				<td id="version-uikit-{$this->plugin_info['codename']}">
					Unknown
				</td>
			</tr>
			<tr>
				<td>
					{{ 'AngularJS version' | trans}}
				</td>
				<td id="version-angularjs-{$this->plugin_info['codename']}">
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
				<td id="website-{$this->plugin_info['codename']}">
				</td>
			</tr>
			<tr>
				<td>
					{{ 'Wiki and manuals' |trans}}
				</td>
				<td id="wiki-{$this->plugin_info['codename']}">
				</td>
			</tr>
		</table>
		<div id="update-{$this->plugin_info['codename']}">
			<div id="update-available-{$this->plugin_info['codename']}" class="uk-panel uk-panel-box uk-alert-danger uk-text-center update-info-{$this->plugin_info['codename']} uk-hidden">
				<h3 class="uk-text-center">
					<i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'This plugin is outdated!' |trans}}
				</h3>
				<h4 class="uk-text-center">
					{{ 'A new version is available. Please, update.' |trans}}
				</h4>
				<button type="button" class="uk-button uk-button-success" id="update-details-{$this->plugin_info['codename']}"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'Update details' |trans}}</button>
			</div>
			<div id="update-ok-{$this->plugin_info['codename']}" class="uk-panel uk-panel-box uk-alert-success uk-text-center update-info-{$this->plugin_info['codename']} uk-hidden">
				<i class="uk-icon uk-icon-check uk-margin-small-right"></i>{{ 'Your version of the plugin is up to date!' |trans}}
			</div>
			<div id="update-problem-{$this->plugin_info['codename']}" class="uk-panel uk-panel-box uk-alert-danger uk-text-center update-info-{$this->plugin_info['codename']} uk-hidden">
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
		$firstName=htmlspecialchars(WidgetkitExPlugin::extractWKUserName($appWK['user']));
		$lastName=htmlspecialchars(WidgetkitExPlugin::extractWKUserName($appWK['user'],false));
		$email=htmlspecialchars($appWK['user']->getEmail());
		$cms=htmlspecialchars((WidgetkitExPlugin::IsJoomlaInstalled())?'Joomla':'WordPress');
		$origin=htmlspecialchars($appWK['request']->getBaseUrl());
		$locale=htmlspecialchars($appWK['locale']);
		
		if (!isset($this->plugin_info['codename'])){
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

<button class="uk-button uk-button-success" data-uk-modal="{target:'#{$this->plugin_info['codename']}-subscribe'}"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>{{ 'Subscribe' |trans}}</button>

<div id="{$this->plugin_info['codename']}-subscribe" class="uk-modal">
	<div class="uk-modal-dialog">
		<a class="uk-modal-close uk-close"></a>
		<div class="uk-overflow-container">
			<div class="uk-panel uk-panel-box uk-alert">
			<i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'Please, fill in all the fields below, then click Submit button' |trans}}
			</div>
			<form class="uk-form uk-form-horizontal" action="http://valitov.us11.list-manage.com/subscribe/post?u=13280b8048b58d2be207f1dd5&amp;id=52d79713c6" method="post" id="form-{$this->plugin_info['codename']}-subscribe" target="_blank">
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
						<input type="text" id="country_code-{$this->plugin_info['codename']}" name="COUNTRYID" value="">
						<input type="text" id="country_name-{$this->plugin_info['codename']}" name="COUNTRY" value="">
						<input type="text" id="region_code-{$this->plugin_info['codename']}" name="REGIONID" value="">
						<input type="text" id="region_name-{$this->plugin_info['codename']}" name="REGION" value="">
						<input type="text" id="city-{$this->plugin_info['codename']}" name="CITY" value="">
						<input type="text" id="time_zone-{$this->plugin_info['codename']}" name="TIMEZONE" value="">
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
	jQuery.validate({
		form: '#form-{$this->plugin_info['codename']}-subscribe',
		modules : 'html5',
		errorElementClass: 'uk-form-danger',
		errorMessageClass: 'uk-text-danger',
		validateOnBlur : true,
		scrollToTopOnError : false
	});
	jQuery('#form-{$this->plugin_info['codename']}-subscribe').formchimp();
	
	/*Geolocation if nessesary*/
	jQuery('#{$this->plugin_info['codename']}-subscribe').on({
		'show.uk.modal': function(){
			if (!jQuery("#country_code-{$this->plugin_info['codename']}").val()){
				jQuery.ajax({
						'url': 'http://ip-api.com/json',
						'type' : "GET",
						'dataType' : 'json',
						success: function (data, textStatus, jqXHR){
							if (data){
								jQuery("#country_code-{$this->plugin_info['codename']}").val(data.countryCode);
								jQuery("#country_name-{$this->plugin_info['codename']}").val(data.country);
								jQuery("#region_code-{$this->plugin_info['codename']}").val(data.region);
								jQuery("#region_name-{$this->plugin_info['codename']}").val(data.regionName);
								jQuery("#city-{$this->plugin_info['codename']}").val(data.city);
								jQuery("#time_zone-{$this->plugin_info['codename']}").val(data.timezone);
							}
						}
				});
			}
		}
	});
</script>

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
		if ( (!isset($this->plugin_info['name'])) || (!isset($this->plugin_info['version'])) || (!isset($this->plugin_info['codename'])) )
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
		$js = <<< EOT
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
	\$('#name-{$this->plugin_info['codename']}').waitUntilExists(function(){
		\$('#name-{$this->plugin_info['codename']}').empty();
		\$('#name-{$this->plugin_info['codename']}').append('{$settings['name']}');
		
		\$('#build-{$this->plugin_info['codename']}').empty();
		\$('#build-{$this->plugin_info['codename']}').append('{$settings['date']}');
		
		\$('#website-{$this->plugin_info['codename']}').empty();
		\$('#website-{$this->plugin_info['codename']}').append('<a href="{$settings['website']}" target="_blank">{$settings['website']}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>');
		
		\$('#version-{$this->plugin_info['codename']}').empty();
		\$('#version-{$this->plugin_info['codename']}').append('{$settings['version']}');
		
		\$('#logo-{$this->plugin_info['codename']}').empty();
		\$('#logo-{$this->plugin_info['codename']}').append('<img class="uk-width-1-1" src="{$settings['logo']}" style="max-width:300px;">');
		
		\$('#wiki-{$this->plugin_info['codename']}').empty();
		\$('#wiki-{$this->plugin_info['codename']}').append('<a href="{$settings['wiki']}" target="_blank">{$settings['wiki']}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>');
		
		\$('#version-jquery-{$this->plugin_info['codename']}').empty();
		\$('#version-jquery-{$this->plugin_info['codename']}').append(\$.fn.jquery);
		
		if (UIkit && UIkit.version){
			\$('#version-uikit-{$this->plugin_info['codename']}').empty();
			\$('#version-uikit-{$this->plugin_info['codename']}').append(UIkit.version);
		}
		
		if (angular && angular.version && angular.version.full){
			\$('#version-angularjs-{$this->plugin_info['codename']}').empty();
			\$('#version-angularjs-{$this->plugin_info['codename']}').append(angular.version.full);
		}
		
		var configfile="{$configfile}";
		\$.ajax({
			'url': configfile,
			'type' : "GET",
			'dataType' : 'json',
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
			\$('div.update-info-{$this->plugin_info['codename']}').addClass('uk-hidden');
			\$('#update-problem-{$this->plugin_info['codename']}').removeClass('uk-hidden');
		});
	}
	
	/*We only show check for updates on the Widgetkit page*/
	if (!( (window.location.href.indexOf('com_widgetkit')>0) || (window.location.href.indexOf('page=widgetkit')>0) ))
		return;
	
	\$.ajax({
			'url': '{$settings['api']}{$settings['distr_name']}/releases/latest',
			'type' : "GET",
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
						var infotext='<p class="uk-margin-remove"><i class="uk-icon-info-circle uk-margin-small-right"></i>{$appWK['translator']->trans('New release of plugin %name% is available!',array('%name%' => $settings['name']))} {$appWK['translator']->trans('Version')} '+data.tag_name+'.</p><p class="uk-text-center uk-margin-remove"><button class="uk-button uk-button-mini uk-button-success" id="info-{$settings['distr_name']}">{$appWK['translator']->trans('Update details')}</button></p>';
						
						UIkit.notify(infotext, {'timeout':{$settings['infotimeout']},'pos':'top-center','status':'warning'});
						\$('#info-{$settings['distr_name']}').click(function(){
								showUpdateInfo(data.html_url,date_remote,data.tag_name,marked(data.body));
						});
						\$('#update-{$this->plugin_info['codename']}').waitUntilExists(function(){
							\$('div.update-info-{$this->plugin_info['codename']}').addClass('uk-hidden');
							\$('#update-available-{$this->plugin_info['codename']}').removeClass('uk-hidden');
							
							\$('#version-local-{$this->plugin_info['codename']}').empty();
							\$('#version-local-{$this->plugin_info['codename']}').append('{$settings['version']}');
							
							\$('#version-remote-{$this->plugin_info['codename']}').empty();
							\$('#version-remote-{$this->plugin_info['codename']}').append(data.tag_name);
							
							\$('#date-local-{$this->plugin_info['codename']}').empty();
							\$('#date-local-{$this->plugin_info['codename']}').append('{$settings['date']}');
							
							\$('#date-remote-{$this->plugin_info['codename']}').empty();
							if (date_remote.length)
								\$('#date-remote-{$this->plugin_info['codename']}').append(date_remote);
							
							\$('#release-info-{$this->plugin_info['codename']}').empty();
							\$('#release-info-{$this->plugin_info['codename']}').append(marked(data.body));
							
							\$('#update-logo-{$this->plugin_info['codename']}').attr('src','{$settings['logo']}');
							
							\$('#download-{$this->plugin_info['codename']}').attr('href',data.html_url);
							
							\$('#instructions-{$this->plugin_info['codename']}').attr('href','{$settings['wiki']}');
							
							\$('#update-details-{$this->plugin_info['codename']}').click(function(){
								showUpdateInfo(data.html_url,date_remote,data.tag_name,marked(data.body));
							});
						});
					}
					else{
						\$('#update-{$this->plugin_info['codename']}').waitUntilExists(function(){
							\$('div.update-info-{$this->plugin_info['codename']}').addClass('uk-hidden');
							\$('#update-ok-{$this->plugin_info['codename']}').removeClass('uk-hidden');
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
			$output = method_exists($var, 'export') ? $var->export() : WidgetkitExPlugin::features_var_export((array) $var, '', FALSE, $count+1);
		}
		else if (is_array($var)) {
			if (empty($var)) {
				$output = 'array()';
			}
			else {
			$output = "array(\n";
			foreach ($var as $key => $value) {
				// Using normal var_export on the key to ensure correct quoting.
				$output .= "  " . var_export($key, TRUE) . " => " . WidgetkitExPlugin::features_var_export($value, '  ', FALSE, $count+1) . ",\n";
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
		$this->printJSDebugText($this->debug_info,1);
		$this->printJSDebugText($this->debug_warning,2);
		$this->printJSDebugText($this->debug_error,3);
	}
	
	/*
	Prints debug info string for JS console output
	$typeid defines the log warning level
	*/
	private function printJSDebugString($s, $typeid=1){
		$prefix='['.$this->plugin_info['name'].' #'.$this->id.'] ';
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
			$this->printJSDebugString($s,$typeid);
		}
	}
}

}
?>