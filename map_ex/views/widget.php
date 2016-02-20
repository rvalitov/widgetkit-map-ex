<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

$map_id  = uniqid('wk-map-ex');

require_once(__DIR__.'/debug.php');

$markers = array();
$width   = $settings['width']  == 'auto' ? 'auto'  : ((int)$settings['width']).'px';
$height  = $settings['height'] == 'auto' ? '300px' : ((int)$settings['height']).'px';

// Markers
$item_id=0;
foreach ($items as $i => $item) {
	$item_id++;
    if (isset($item['location']) && $item['location']) {
        $marker = array(
            'lat'     => $item['location']['lat'],
            'lng'     => $item['location']['lng'],
            'title'   => $item['title'],
            'content' => '',
			'pin'	=> '',
			'anchor_x'	=> '',
			'anchor_y'	=> ''
        );

        if (($item['title'] && $settings['title']) ||
            ($item['content'] && $settings['content']) ||
            ($item['media'] && $settings['media'])) {
                $marker['content'] = $app->convertUrls($this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('item', 'settings')));
        }
		if ($settings['pin_type']==''){
			$pinoverride=false;
			if (strlen($item['custom_pin_path'])>0){
				$marker['pin']=trim($item['custom_pin_path']);
				$pinoverride=true;
				array_push($debug_info,'Unique custom pin image provided for item#'.$item_id.': '.$marker['pin']);
			}
			else{
				$marker['pin']=trim($settings['custom_pin_path']);
				array_push($debug_info,'Global custom pin image will be used for item#'.$item_id.': '.$marker['pin']);
			}

			if (strlen($marker['pin'])>0){
				//Checking for absolute URL
				if ( (substr($marker['pin'], 0, 7) != 'http://') && (substr($marker['pin'], 0, 8) != 'https://') && (substr($marker['pin'], 0, 2) != '//') && (strlen($marker['pin'])>2) )
					if ($isJoomla){
						//We must remove the starting '/' if it exists, because JURI::base() already has it set.
						if (substr($marker['pin'], 0, 1) != '/')
							$marker['pin']=JURI::base().$marker['pin'];
						else
							$marker['pin']=JURI::base().substr($marker['pin'], 1);
					}
					else
						array_push($debug_warning,'Relative URLs for WordPress are not supported in this version of the widget. Please, specify a full URL manually for '.$marker['pin'].' - this is done in the settings of the widget.');

				array_push($debug_info,'The final URL for the custom pin of the item#'.$item_id.' is '.$marker['pin']);
				if ($settings['debug_output'])
					if (url_exists($marker['pin']))
						array_push($debug_info,'The URL '.$marker['pin'].' is valid.');
					else
						array_push($debug_error,'Failed to check the URL '.$marker['pin']." - it doesn't exist?");
				if ( ($pinoverride) && (is_numeric($item['custom_pin_anchor_x'])) && (is_numeric($item['custom_pin_anchor_y'])) )
				{
					$marker['anchor_x']=intval($item['custom_pin_anchor_x']);
					$marker['anchor_y']=intval($item['custom_pin_anchor_y']);
				}
				else
					if ( (!$pinoverride) && (is_numeric($settings['custom_pin_anchor_x'])) && (is_numeric($settings['custom_pin_anchor_y'])) )
					{
						$marker['anchor_x']=intval($settings['custom_pin_anchor_x']);
						$marker['anchor_y']=intval($settings['custom_pin_anchor_y']);
					}
			}
			else
				array_push($debug_warning,'The custom image path is empty for item#'.$item_id.'. The deafult pin image will be used.');
		}
		else{
			array_push($debug_info,'The configuration is set to use a default pin image for item#'.$item_id);
			if (strlen($item['custom_pin_path'])>0)
				array_push($debug_warning,'You have defined a custom unique pin image for item#'.$item_id.". However, this image will be ignored. To use custom images you must select 'Custom' as the 'Marker Pin Icon' option in the widget's settings. Ignore this message if the widget works as you expect.");
		}

        $markers[] = $marker;
    }
	else
		array_push($debug_warning,'The location is missing for item#'.$item_id.'. This item will be ignored.');
}

$settings['markers'] = $markers;
$settings['map_id'] = $map_id;
if (!empty($settings['map_center'])){
	$center=explode(',',$settings['map_center']);
	if ( (sizeof($center)==2) && (is_numeric($center[0])) && (is_numeric($center[1])) ){
		$settings['center_lat'] = $center[0];
		$settings['center_lng'] = $center[1];
	}
}
?>

<script type="widgetkit/mapex" data-id="<?php echo $map_id;?>" data-class="<?php echo $settings['class']; ?> uk-img-preserve" data-style="width:<?php echo $width?>;height:<?php echo $height?>;">
    <?php echo json_encode($settings) ?>
</script>

<?php if ( ($settings['responsive']) || (!empty($settings['map_center'])) || ($settings['modal_fix']) ):?>
<script>
jQuery(document).ready(function($){
	function checkWidgetkitMaps() {
		var item=getWidgetkitMap("<?php echo $settings['map_id']?>");
		if (item) {
			<?php if ($settings['responsive']):?>
			google.maps.event.addDomListener(window, 'resize', function() {

				<?php if (!empty($settings['map_center'])):?>
				item.panTo(new google.maps.LatLng(<?php echo $settings['map_center']?>));
				<?php if ($settings['debug_output'])
					printJSDebugString('Auto pan performed to '.$settings['map_center']);
				?>
				<?php endif;?>

				item.setZoom(<?php echo $settings['zoom']?>);
				<?php if ($settings['debug_output'])
					printJSDebugString('Auto zoom performed to level '.$settings['zoom']);
				?>
			});
			<?php if ($settings['debug_output'])
				printJSDebugString('Responsive setup performed');
			?>
			<?php endif;//responsive?>

			<?php if ( ($settings['modal_fix']) && (!empty($settings['map_center'])) ):?>
			var modal_id='#<?php echo $settings['map_id']?>';
			var modal_dialog=$(modal_id).closest('.uk-modal');
			if (modal_dialog){
				var box_id=modal_dialog.attr("id");
				if (box_id){
					<?php if ($settings['debug_output'])
						printJSDebugString('Modal fix setup successfull for modal id'.modal_id);
					?>
					$('#'+box_id).on({
						'show.uk.modal': function(){
							var map = jQuery('#<?php echo $settings['map_id']?>', '#'+box_id).first().get(0);
							google.maps.event.trigger(map, 'resize');
							<?php if (!empty($settings['map_center'])):?>
							item.setCenter(new google.maps.LatLng(<?php echo $settings['map_center']?>));
							item.panTo(new google.maps.LatLng(<?php echo $settings['map_center']?>));
							<?php if ($settings['debug_output']){
								printJSDebugString('Map centered to '.$settings['map_center']);
								printJSDebugString('Auto pan performed to '.$settings['map_center']);
							}?>
							<?php endif;//map_center?>

							item.setZoom(<?php echo $settings['zoom']?>);
							<?php if ($settings['debug_output'])
								printJSDebugString('Auto zoom performed to level '.$settings['zoom']);
							?>

							<?php if ($settings['debug_output'])
								printJSDebugString('Modal fix performed for modal id'.modal_id);
							?>
						}
					});
				}
				<?php if ($settings['debug_output']):?>
				else
					<?php printJSDebugString('Failed to find modal with id'.modal_id,3);?>
				<?php endif;?>

			}
			<?php if ($settings['debug_output']):?>
			else
				<?php printJSDebugString('Failed to find modal with id'.modal_id,3);?>
			<?php endif;?>

			<?php endif;//modal fix?>
	   }
	   else
		   setTimeout(checkWidgetkitMaps,1000);
	}
	setTimeout(checkWidgetkitMaps,1000);
});
</script>
<?php endif;?>

<?php if ($settings['debug_output']):?>
<script>
	<?php
	printJSDebugText($debug_info,1);
	printJSDebugText($debug_warning,2);
	printJSDebugText($debug_error,3);
	?>
	jQuery(document).ready(function($){
		var countAPILoaded=0;
		var isUsingMap=false;
		var srcFirstAPI='';
		$("script").each(function() {
			var srcAPI=$(this).attr("src");
			if ( (srcAPI) && (srcAPI.indexOf('/maps.google.com/')>0) ) {
				console.info('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>Found Google Maps API loading script: '+srcAPI);
				if (countAPILoaded==0)
					srcFirstAPI=srcAPI;
				countAPILoaded++;
			}
			if ( (srcAPI) && (srcAPI.indexOf('wkInitializeGoogleMapsApi')>0) )
				isUsingMap=true;
		});
		switch(countAPILoaded){
			case 0:
				console.error('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>No script found that loads Google Maps API. It\'s a fatal error. Possible reasons: invalid installation or conflict with other components, modules or plugins.');
				break;
			case 1:
				console.info('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>Single script detected that loads Google Maps API.');
				if (srcFirstAPI.length>0)
				if (srcFirstAPI.indexOf('wkInitializeGoogleMapsEx')>0)
					console.info('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>The Google Maps API loading script is correct.');
				else
					console.error('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>The Google Maps API loading script is not correct. Some other component, module or plugin on your website overrides our script making this widget inactive.');
				break;
			default:
				if (isUsingMap)
					console.error('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>We found out that you are using both MapEx and Map widget on the same page. This leads to conflicts and errors. You can use only MapEx or Map widget, but not both. You can convert all your Map widgets to MapEx widgets in the control panel.');
				else
					console.error('<?php echo '['.$widget_name.' #'.$widget_id.'] '; ?>Multiple scripts detected that try to load Google Maps API. This may cause unexpected behaviour or malfunction of the widget. Please, turn of other plugins that use Goolge Maps API to fix this error.');
				break;
		}
	});
</script>
<?php endif;?>