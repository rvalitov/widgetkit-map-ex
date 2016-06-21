<?php
/*
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
*/

namespace WidgetkitEx\MapEx{

require_once(__DIR__.'/WidgetkitExPlugin.php');

class WidgetkitExMapPlugin extends WidgetkitExPlugin{
	//$appWK - is parameter that must be set to $app upon call.
	public function generateMapExJS($appWK){
		$js = <<< EOT
jQuery(document).ready(function(\$){
	\$('div.uk-form-controls > field input[ng-model="latlng.marker"]').waitUntilExists(function(){
		\$(this).prop('disabled', true);
		\$(this).attr('placeholder',"{$appWK['translator']->trans('Field disabled by MapEx widget')}");
		\$(this).attr('title',"{$appWK['translator']->trans('Field disabled by MapEx widget')}");
	});
});

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