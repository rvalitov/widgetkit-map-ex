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
			var modal = UIkit.modal.blockUI("Loading, please wait...",{"center":true});
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
							if ( ('name' in data[i]) && ('info' in data[i]) ) {
								for (var k=1;k<=5;k++)
									if ( (!(('icon'+k) in data[i])) || (!(('color'+k) in data[i])) || (!(('width'+k) in data[i])) || (!(('height'+k) in data[i])) || (!(('size'+k) in data[i])) || (!(('icon_x'+k) in data[i])) || (!(('icon_y'+k) in data[i])) || (!(('label_x'+k) in data[i])) || (!(('label_y'+k) in data[i])) || (typeof data[i]['width'+k] !== 'number') || (typeof data[i]['height'+k] !== 'number') || (typeof data[i]['size'+k] !== 'number') || ((typeof data[i]['icon_x'+k] !== 'number')&&(data[i]['icon_x'+k]!='')) || ((typeof data[i]['icon_y'+k] !== 'number')&&(data[i]['icon_y'+k]!='')) || ((typeof data[i]['label_x'+k] !== 'number')&&(data[i]['label_x'+k]!='')) || ((typeof data[i]['label_y'+k] !== 'number')&&(data[i]['label_y'+k]!='')) || (typeof data[i]['size'+k] !== 'number') || (data[i]['size'+k]<1) || (data[i]['width'+k]<1) || (data[i]['height'+k]<1) || (!ValidURL(data[i]['icon'+k])) ){
										is_valid=false;
										break;
									}
							}
							else
								is_valid=false;
							
							if (is_valid)
							{
								var tags='<div><h4 class="uk-text-center">#'+(i+1)+'. '+safe_tags_replace(data[i].name);
								if ( (data[i]['info']) && (data[i]['info'].trim().length>0) )
									tags+='<i class="uk-icon uk-icon-info-circle uk-margin-small-left" style="color:#ffb105;cursor:pointer;" onclick="UIkit.modal.alert(\''+data[i]['info'].replace('"','&quot;').replace("'","&#39;")+'\',{\'center\':true});"></i>';
								tags+='</h4><div class="uk-grid uk-grid-width-1-5">';
								for (var k=1;k<=5;k++)
									tags+='<div class="uk-text-center"><div><img src="'+data[i]['icon'+k]+'"></div><small>Level '+k+'</small></div>';
								tags+='</div><div class="uk-text-center"><button onclick="UIkit.modal.alert(\'Selected collection was activated!\',{\'center\':true});';
								for (var k=1;k<=5;k++){
									tags+='jQuery(\'#cluster-'+k+'-color\').val(\''+data[i]['color'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-icon input\').val(\''+data[i]['icon'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-width\').val(\''+data[i]['width'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-height\').val(\''+data[i]['height'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-size\').val(\''+data[i]['size'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-icon_x\').val(\''+data[i]['icon_x'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-icon_y\').val(\''+data[i]['icon_y'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-label_x\').val(\''+data[i]['label_x'+k]+'\').trigger(\'change\');';
									tags+='jQuery(\'#cluster-'+k+'-label_y\').val(\''+data[i]['label_y'+k]+'\').trigger(\'change\');';
								}
								tags+='"><i class="uk-icon uk-icon-check uk-margin-small-right"></i>Activate Collection</button></div></div>';
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
								info_text+=error_list[k];
							}
							console.log(info_text);
						}
						UIkit.notify(info_text, {'timeout':3000,'pos':'top-center','status':'info'});
					}
					else
						UIkit.modal.alert("Failed to download a list of markers collections.",{"center":true});
				},
				error: function (jqXHR, textStatus, errorThrown ){
					modal.hide();
					UIkit.modal.alert("Failed to download a list of markers collections.",{"center":true});
				}
			});
	  });
	})(jQuery);
}