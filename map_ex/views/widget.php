<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

require_once(__DIR__.'/WidgetkitExPluginDebug.php');
use WidgetkitEx\MapEx\WidgetkitExPluginDebug;

$map_id  = uniqid('wk-map-ex');
$map_id2 = substr($map_id,9);

$debug=new WidgetkitExPluginDebug($map_id);

$markers = array();
$width   = $settings['width']  == 'auto' ? 'auto'  : ((int)$settings['width']).'px';
$height  = $settings['height'] == 'auto' ? '300px' : ((int)$settings['height']).'px';

$zoom_phone_portrait=$settings['zoom'];
$zoom_phone_landscape=is_numeric($settings['zoom_phone_h']) ? $settings['zoom_phone_h'] : $zoom_phone_portrait;
$zoom_tablet=is_numeric($settings['zoom_tablet']) ? $settings['zoom_tablet'] : $zoom_phone_landscape;
$zoom_desktop=is_numeric($settings['zoom_desktop']) ? $settings['zoom_desktop'] : $zoom_tablet;
$zoom_large=is_numeric($settings['zoom_large']) ? $settings['zoom_large'] : $zoom_desktop;

// Markers
$item_id=0;
foreach ($items as $i => $item) {
	$item_id++;
    if (isset($item['location']) && $item['location']) {
        $marker = array(
            'lat'     => $item['location']['lat'],
            'lng'     => $item['location']['lng'],
            'title'   => $item['title'],
			'id'      => $map_id+"-marker-"+$item_id,
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
				$debug->addInfoString('Unique custom pin image provided for item#'.$item_id.': '.$marker['pin']);
			}
			else{
				$marker['pin']=trim($settings['custom_pin_path']);
				$debug->addInfoString('Global custom pin image will be used for item#'.$item_id.': '.$marker['pin']);
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
						$debug->addWarningString('Relative URLs for WordPress are not supported in this version of the widget. Please, specify a full URL manually for '.$marker['pin'].' - this is done in the settings of the widget.');

				$debug->addInfoString('The final URL for the custom pin of the item#'.$item_id.' is '.$marker['pin']);
				if ($settings['debug_output'])
					if (url_exists($marker['pin']))
						$debug->addInfoString('The URL '.$marker['pin'].' is valid.');
					else
						$debug->addErrorString('Failed to check the URL '.$marker['pin']." - it doesn't exist?");
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
				$debug->addWarningString('The custom image path is empty for item#'.$item_id.'. The deafult pin image will be used.');
		}
		else{
			$debug->addInfoString('The configuration is set to use a default pin image for item#'.$item_id);
			if (strlen($item['custom_pin_path'])>0)
				$debug->addWarningString('You have defined a custom unique pin image for item#'.$item_id.". However, this image will be ignored. To use custom images you must select 'Custom' as the 'Marker Pin Icon' option in the widget's settings. Ignore this message if the widget works as you expect.");
		}

        $markers[] = $marker;
    }
	else
		$debug->addWarningString('The location is missing for item#'.$item_id.'. This item will be ignored.');
}

$settings['map_id'] = $map_id;
$settings['map_id2'] = $map_id2;
if (!empty($settings['map_center'])){
	$center=explode(',',$settings['map_center']);
	if ( (sizeof($center)==2) && (is_numeric($center[0])) && (is_numeric($center[1])) ){
		$settings['center_lat'] = $center[0];
		$settings['center_lng'] = $center[1];
	}
}

$settings['clusterstyles']=[];
//Checking the sizes of cluster images
if ($settings['markercluster']=='custom'){
	$styles=[];
	
	for ($i=0; $i<sizeof($settings['clusters']); $i++)
		if ( (isset($settings['clusters'][$i]['icon'])) && ($settings['clusters'][$i]['icon']) ){
			$cstyle=[];
			$cstyle['url']=$settings['clusters'][$i]['icon'];
			
			if (!is_numeric($settings['clusters'][$i]['width']))
				$cstyle['width']=($settings['clusters'][$i]['options']['width'] > 0) ? intval($settings['clusters'][$i]['options']['width']) : 53;
			else
				$cstyle['width']=intval($settings['clusters'][$i]['width']);
			if (!is_numeric($settings['clusters'][$i]['height']))
				$cstyle['height']=($settings['clusters'][$i]['options']['height'] > 0) ? intval($settings['clusters'][$i]['options']['height']) : 53;
			else
				$cstyle['height']=intval($settings['clusters'][$i]['height']);
			
			$cstyle['textSize']=is_numeric($settings['clusters'][$i]['textSize']) ? intval($settings['clusters'][$i]['textSize']) : 11;
			$cstyle['textColor']=($settings['clusters'][$i]['textColor']) ? $settings['clusters'][$i]['textColor'] : 'black';
			if ( (is_numeric($settings['clusters'][$i]['icon_anchor_x'])) && (is_numeric($settings['clusters'][$i]['icon_anchor_y'])) )
				$cstyle['iconAnchor']=[intval($settings['clusters'][$i]['icon_anchor_x']), intval($settings['clusters'][$i]['icon_anchor_y'])];
			//X and Y are inversed:
			$cstyle['anchor']=[is_numeric($settings['clusters'][$i]['label_anchor_y']) ? intval($settings['clusters'][$i]['label_anchor_y']) : 0, is_numeric($settings['clusters'][$i]['label_anchor_x']) ? intval($settings['clusters'][$i]['label_anchor_x']) : 0];

			array_push($styles,$cstyle);
		}
	$settings['clusterstyles']=$styles;
}
	
?>

<?php
	//We must print the contents in HTML, not in JS. Such approach allows to use SEF urls.
	for ($i=0; $i<sizeof($markers); $i++)
		if (isset($markers[$i]['content'])){
			echo '<div class="uk-hidden" id="'.$markers[$i]['id'].'">'.$markers[$i]['content'].'</div>';
			unset($markers[$i]['content']);
		}
	$settings['markers'] = $markers;
	
	$debug->addInfoString('Widget settings: '.print_r($settings,true));
?>

<script type="widgetkit/mapex" data-id="<?php echo $map_id;?>" data-class="<?php echo $settings['class']; ?> uk-img-preserve" data-style="width:<?php echo $width?>;height:<?php echo $height?>;">
    <?php echo json_encode($settings) ?>
</script>

<script>
function getMapZoom<?php echo $map_id2;?>(){
	if (window.outerWidth<=767)
		if (Math.abs(window.orientation) === 90){
			<?php if ($settings['debug_output'])
			printJSDebugString('Detected Phone Landscape mode');
			?>
			return <?php echo $zoom_phone_landscape;?>;
		}
		else{
			<?php if ($settings['debug_output'])
			printJSDebugString('Detected Phone Portrait mode');
			?>
			return <?php echo $zoom_phone_portrait;?>;
		}
	else
		if (window.outerWidth<=959){
			<?php if ($settings['debug_output'])
			printJSDebugString('Detected Tablet mode');
			?>
			return <?php echo $zoom_tablet;?>;
		}
		else{
			<?php if ($settings['debug_output'])
			printJSDebugString('Detected Large Screen mode');
			?>
			return <?php echo $zoom_large;?>;
		}
}

function updateMap<?php echo $map_id2;?>(item){
	<?php if (!empty($settings['map_center'])):?>
	item.panTo(new google.maps.LatLng(<?php echo $settings['map_center']?>));
	<?php if ($settings['debug_output'])
		printJSDebugString('Auto pan performed to '.$settings['map_center']);
	?>
	<?php endif;?>

	item.setZoom(getMapZoom<?php echo $map_id2;?>());
	<?php if ($settings['debug_output'])
		printJSDebugString('Auto zoom performed to level '.$settings['zoom']);
	?>
}

jQuery(document).ready(function($){
	function checkWidgetkitMaps() {
		var item=getWidgetkitMap("<?php echo $map_id?>");
		if (item) {
			google.maps.event.addDomListener(window, 'resize', function(){
				<?php if ($settings['debug_output'])
					printJSDebugString('Window resize event captured, updating the map...');
				?>
				updateMap<?php echo $map_id2;?>(item);
			});
			window.addEventListener("orientationchange", function () {
				<?php if ($settings['debug_output'])
					printJSDebugString('Screen orientation changed, updating the map...');
				?>
				updateMap<?php echo $map_id2;?>(item);
			});
			<?php if ($settings['debug_output'])
				printJSDebugString('Responsive setup performed');
			?>
			
			<?php if (!empty($settings['map_center'])):?>
			jQuery(document).on(
				"display.uk.check",
				function(event) {
					var self = jQuery(event.target);
					var map = self.find('[id^="wk-map-ex"]');
					if(map.is(':visible')) {
						map.each(
							function() {
								var id = jQuery(this).attr('id');
								var item = getWidgetkitMap(id);
								if(item) {
									google.maps.event.trigger(item, "resize");
									var sub_id = id.substring(9);
									if(typeof window['updateMap' + sub_id] === 'function') {
										window['updateMap' + sub_id](item);
										<?php if ($settings['debug_output'])
											printJSDebugString('Map updated on display.uk.check event.');
										?>
									}
								}
							}
						);
					}
				}
			);
			<?php endif;//uikit fix?>
	   }
	   else
		   setTimeout(checkWidgetkitMaps,1000);
	}
	setTimeout(checkWidgetkitMaps,1000);
});

<?php if ($settings['debug_output']):?>
	<?php
	$debug->printDebugStrings();
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
<?php endif;?>
</script>