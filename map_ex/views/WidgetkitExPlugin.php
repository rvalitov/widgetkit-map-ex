<?php
/*
Helper functions for Widgetkit 2 plugins.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

namespace WidgetkitEx\MapEx{

class WidgetkitExPlugin{
	
	private $plugin_info;

	public function __construct(){
		$this->plugin_info=$this->getWKPluginInfo();
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

	//Returns array with info about current plugin (no matter if it's a widget or a content provider). It works only for custom plugins that are created with updater.js file.
	//The array contains following fields:
	//name - the name of the plugin or empty string if unknown.
	//version - the version of the plugin or empty string if unknown.
	//codename - the name of the distro (codename) or empty string if unknown.
	//date - the release date of the plugin or empty string if unknown.
	//logo - the absolute URL of the logo of the plugin or empty string if unknown.
	//wiki - the absolute URL of wiki (manual) for the plugin or empty string if unknown.
	//website - the absolute URL of website for the plugin or empty string if unknown.
	private function getWKPluginInfo(){
		$info=[
			'name'=>'',
			'version'=>'',
			'codename'=>'',
			'version'=>'',
			'date'=>'',
			'logo'=>'',
			'wiki'=>'',
			'website'=>''
		];
		
		$f=@file_get_contents(__DIR__ .'/../plugin.php',false,null,0,2400);
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
		return $info;
	}

	//Prints information for the "About" section of the plugin
	//$appWK - is parameter that must be set to $app upon call.
	public function printAboutInfo($appWK){
		$versionWK=htmlspecialchars((isset($appWK['version']))?$appWK['version']:'Unknown');
		$versionDB=htmlspecialchars((isset($appWK['db_version']))?$appWK['db_version']:'Unknown');
		$php_version=htmlspecialchars(@phpversion());
		$phpinfo;
		if (version_compare('5.3',$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your PHP is too old! Upgrade is strongly recommended! This plugin may not work with your version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
		if (version_compare('5.6',$php_version)>0)
			$phpinfo='<span  data-uk-tooltip class="uk-text-warning" style="margin-top: 5px;" title="{{ \'Your PHP is quite old. Although this plugin can work with your version of PHP, upgrade is recommended to the latest stable version of PHP.\' |trans}}"><i class="uk-icon-warning  uk-margin-small-right"></i>'.$php_version.'</span>';
		else
			$phpinfo='<span  data-uk-tooltip class="uk-text-success" style="margin-top: 5px;" title="{{ \'Your PHP version is OK.\' |trans}}"><i class="uk-icon-check uk-margin-small-right"></i>'.$php_version.' ('.@php_sapi_name().')</span>';

		$wkinfo;
		if (version_compare('2.5.0',$versionWK)>0)
			$wkinfo='<span  data-uk-tooltip class="uk-text-danger" style="margin-top: 5px;" title="{{ \'Your Widgetkit version is too old. Upgrade is strongly recommended. Although this plugin may work with your version of Widgetkit, upgrade is recommended to the latest stable version of Widgetkit.\' |trans}}"><i class="uk-icon-warning uk-margin-small-right"></i>'.$versionWK.'</span>';
		if (version_compare('2.6.0',$versionWK)>0)
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
		$js = <<< EOT
jQuery(document).ready(function(\$){
	(function (\$) {
		/**
		* @function
		* @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
		* @param {function} handler A function to execute at the time when the element is inserted
		* @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
		* @example \$(selector).waitUntilExists(function);
		*/

		\$.fn.waitUntilExists    = function (handler, shouldRunHandlerOnce, isChild) {
			var found       = 'found';
			var \$this       = \$(this.selector);
			var \$elements   = \$this.not(function () { return \$(this).data(found); }).each(handler).data(found, true);

			if (!isChild)
			{
				(window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
					window.setInterval(function () { \$this.waitUntilExists(handler, shouldRunHandlerOnce, true); }, 500);
			}
			else if (shouldRunHandlerOnce && \$elements.length)
			{
				window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
			}

			return \$this;
		};
	}(jQuery));
	
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
	});
	
	function isNewVersionAvailable(vCurrent,vRemote){
		if (typeof vCurrent + typeof vRemote != 'stringstring')
			return false;
	
		left=vCurrent.replace(/^\D/,'');
		right=vRemote.replace(/^\D/,'');
		var a = left.split('.')
		,   b = right.split('.')
		,   i = 0, len = Math.max(a.length, b.length);
			
		for (; i < len; i++) {
			if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
				return false;
			} else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
				return true;
			}
		}
		return false;
	}

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
					if (isNewVersionAvailable('{$settings['version']}',data.tag_name)){
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
	
	public function generateClusterCollectionJS($appWK){
		$js = <<< EOT
function showMapExInfo(caption,text){
	var id='mapex-dialog-'+ jQuery.now();
	jQuery('#'+id).empty();
	jQuery(document.body).prepend('<div class="uk-modal" id="'+id+'"><div class="uk-modal-dialog"><div class="uk-modal-header"><h3>'+caption+'</h3></div><div class="uk-overflow-container">'+text+'</div><div class="uk-modal-footer"><button class="uk-button uk-modal-close">{$appWK['translator']->trans('Ok')}</button></div></div></div>');
	
	/*We force to open links in new window*/
	jQuery('#'+id+' a').attr('target','_blank');
	
	jQuery('#'+id).on({
		'hide.uk.modal': function(){
			jQuery('#'+id).remove();
		}
	});

	var modal = UIkit.modal('#'+id);
	
	if ( !modal.isActive() )
		modal.show();
}
	
function loadClusterCollections(){	
	var tagsToReplace = {
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;'
	};

	function replaceTag(tag) {
		return tagsToReplace[tag] || tag;
	}

	function safe_tags_replace(str) {
		if (str)
			return str.replace(/[&<>]/g, replaceTag);
		else
			return str;
	}
	
	function ValidURL(str) {
		var urlregex = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
		return urlregex.test(str);
	}

	(function( $ ) {
	  $(function() {
			// More code using $ as alias to jQuery
			var modal = UIkit.modal.blockUI("{$appWK['translator']->trans('Loading, please wait...')}",{"center":true});
			$.ajax({
				'url': 'https://raw.githubusercontent.com/rvalitov/cluster-markers/master/config/config.json',
				'type' : "GET",
				'dataType' : 'json',
				'cache' : false,
				success: function (data, textStatus, jqXHR){
					modal.hide();
					if (data){
						$('#cluster-collection').empty();
						var error_list=[];
						for (var i=0;i<data.length;i++){
							/*Validation test*/
							var is_valid=true;
							if ( ('name' in data[i]) && ('levels' in data[i]) && ('info' in data[i]) && (typeof data[i]['name'] === 'string') && (typeof data[i]['levels'] === 'object') && (typeof data[i]['info'] === 'string') && (data[i]['info'].length<=2000) && (data[i]['levels'].length>=1) ) {
								for (var k=0;k<data[i]['levels'].length;k++)
									if ( (!('icon' in data[i]['levels'][k])) || (!('color' in data[i]['levels'][k])) || (!('width' in data[i]['levels'][k])) || (!('height' in data[i]['levels'][k])) || (!('size' in data[i]['levels'][k])) || (!('icon_x' in data[i]['levels'][k])) || (!('icon_y' in data[i]['levels'][k])) || (!('label_x' in data[i]['levels'][k])) || (!('label_y' in data[i]['levels'][k])) || (typeof data[i]['levels'][k]['width'] !== 'number') || (typeof data[i]['levels'][k]['height'] !== 'number') || (typeof data[i]['levels'][k]['size'] !== 'number') || ((typeof data[i]['levels'][k]['icon_x'] !== 'number')&&(data[i]['levels'][k]['icon_x']!='')) || ((typeof data[i]['levels'][k]['icon_y'] !== 'number')&&(data[i]['levels'][k]['icon_y']!='')) || ((typeof data[i]['levels'][k]['label_x'] !== 'number')&&(data[i]['levels'][k]['label_x']!='')) || ((typeof data[i]['levels'][k]['label_y'] !== 'number')&&(data[i]['levels'][k]['label_y']!='')) || (typeof data[i]['levels'][k]['size'] !== 'number') || (data[i]['levels'][k]['size']<1) || (data[i]['levels'][k]['width']<1) || (data[i]['levels'][k]['height']<1) || (!ValidURL(data[i]['levels'][k]['icon'])) ){
										is_valid=false;
										break;
									}
							}
							else
								is_valid=false;
							
							if (is_valid)
							{
								var name;
								if (data[i].name.length>64)
									name=data[i].name.substring(0,61)+'...';
								else
									name=data[i].name;
								var tags='<div class="uk-panel uk-panel-box"><h4 class="uk-text-center">#'+(i+1)+'. '+safe_tags_replace(name);
								if ( (data[i]['info']) && (data[i]['info'].trim().length>0) )
									tags+='<i class="uk-icon uk-icon-info-circle uk-margin-small-left" style="color:#ffb105;cursor:pointer;" onclick="showMapExInfo(\''+name.replace(/"/g,'&quot;').replace(/'/g,'&#39;')+'\',\''+data[i]['info'].replace(/"/g,'&quot;').replace(/'/g,"\\'")+'\');"></i>';
								tags+='</h4><div class="uk-grid uk-grid-width-1-'+Math.min(5,data[i]['levels'].length)+'">';
								for (var k=0;k<data[i]['levels'].length;k++)
									tags+='<div class="uk-text-center"><div><img src="'+data[i]['levels'][k]['icon']+'"></div><small>{$appWK['translator']->trans('Level')} '+(k+1)+'</small></div>';
								/*
								It's quite difficult to mess with angularjs when you add code with ng-click that must be compiled dynamically in the scope. So, it's better to emulate user input in to fill in the data when a collection is activated.
								*/
								tags+='</div><div class="uk-text-center"><button class="uk-button uk-button-success" onclick="UIkit.modal.alert(\'{$appWK['translator']->trans('Selected collection was activated!')}\',{\'center\':true});';
								tags+='jQuery(\'#mapex-clear-levels\').click();';
								for (var k=0;k<data[i]['levels'].length;k++){
									tags+='jQuery(\'#mapex-add-level\').click();';
									var id=k+1;
									tags+='jQuery(\'#cluster-'+id+'-color\').val(\''+data[i]['levels'][k]['color']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-icon input\').val(\''+data[i]['levels'][k]['icon']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-width\').val(\''+data[i]['levels'][k]['width']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-height\').val(\''+data[i]['levels'][k]['height']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-size\').val(\''+data[i]['levels'][k]['size']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-icon_x\').val(\''+data[i]['levels'][k]['icon_x']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-icon_y\').val(\''+data[i]['levels'][k]['icon_y']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-label_x\').val(\''+data[i]['levels'][k]['label_x']+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+id+'-label_y\').val(\''+data[i]['levels'][k]['label_y']+'\').trigger(\'change\');';
								}
								tags+='"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>{$appWK['translator']->trans('Activate Collection')}</button></div></div>';
								$('#cluster-collection').append(tags);
							}
							else
								error_list.push(i);
						}
						var info_text='Downloaded information about '+data.length+' items.';
						if (error_list.length>0){
							info_text+=' Failed to parse '+error_list.length+' items: ';
							for (var k=0; k<error_list.length; k++){
								if (k>0)
									info_text+=', ';
								info_text+='#'+error_list[k];
							}
							console.log(info_text);
						}
						UIkit.notify(info_text, {'timeout':3000,'pos':'top-center','status':'info'});
					}
					else
						UIkit.modal.alert("{$appWK['translator']->trans('Failed to download a list of markers collections.')}",{"center":true});
				},
				error: function (jqXHR, textStatus, errorThrown ){
					modal.hide();
					UIkit.modal.alert("{$appWK['translator']->trans('Failed to download a list of markers collections.')}",{"center":true});
				}
			});
	  });
	})(jQuery);
}
EOT;
	return $js;
	}

}

}
?>