<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

$map_id  = uniqid('wk-map-ex');
$markers = array();
$width   = $settings['width']  == 'auto' ? 'auto'  : ((int)$settings['width']).'px';
$height  = $settings['height'] == 'auto' ? '300px' : ((int)$settings['height']).'px';

// Markers
foreach ($items as $i => $item) {
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
			}
			else
				$marker['pin']=trim($settings['custom_pin_path']);
			
			if (strlen($marker['pin'])>0){
				//Checking for absolute URL
				if ( (substr($marker['pin'], 0, 7) != 'http://') && (substr($marker['pin'], 0, 8) != 'https://') && (substr($marker['pin'], 0, 2) != '//') && (strlen($marker['pin'])>2) )
					//We must remove the starting '/' if it exists, because JURI::base() already has it set.
					if (substr($marker['pin'], 0, 1) != '/')
						$marker['pin']=JURI::base().$marker['pin'];
					else
						$marker['pin']=JURI::base().substr($marker['pin'], 1);
				
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
		}

        $markers[] = $marker;
    }
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

<script type="widgetkit/map" data-id="<?php echo $map_id;?>" data-class="<?php echo $settings['class']; ?> uk-img-preserve" data-style="width:<?php echo $width?>;height:<?php echo $height?>;">
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
				<?php if ($settings['debug_output']):?>
				console.log('[MapEx] auto pan performed to <?php echo $settings['map_center']?> for id#<?php echo $settings['map_id']?>');
				<?php endif;?>
				<?php endif;?>
				item.setZoom(<?php echo $settings['zoom']?>);
				<?php if ($settings['debug_output']):?>
				console.log('[MapEx] auto zoom performed to level <?php echo $settings['zoom']?> for id#<?php echo $settings['map_id']?>');
				<?php endif;?>
			});
			<?php if ($settings['debug_output']):?>
			console.log('[MapEx] responsive setup performed for id#<?php echo $settings['map_id']?>');
			<?php endif;?>
			<?php endif;?>
			
			<?php if ( ($settings['modal_fix']) && (!empty($settings['map_center'])) ):?>
			var modal_id='#<?php echo $settings['map_id']?>';
			var modal_dialog=$(modal_id).closest('.uk-modal');
			if (modal_dialog){
				var box_id=modal_dialog.attr("id");
				if (box_id){
					<?php if ($settings['debug_output']):?>
					console.log('[MapEx] modal fix setup successfull for id'+modal_id);
					<?php endif;?>
					$('#'+box_id).on({  
						'show.uk.modal': function(){  
							var map = jQuery('#<?php echo $settings['map_id']?>', '#'+box_id).first().get(0);
							google.maps.event.trigger(map, 'resize');
							<?php if (!empty($settings['map_center'])):?>
							item.setCenter(new google.maps.LatLng(<?php echo $settings['map_center']?>));
							item.panTo(new google.maps.LatLng(<?php echo $settings['map_center']?>));
							<?php if ($settings['debug_output']):?>
							console.log('[MapEx] map centered to <?php echo $settings['map_center']?> for id#<?php echo $settings['map_id']?>');
							console.log('[MapEx] auto pan performed to <?php echo $settings['map_center']?> for id#<?php echo $settings['map_id']?>');
							<?php endif;?>
							<?php endif;?>
							
							item.setZoom(<?php echo $settings['zoom']?>);
							<?php if ($settings['debug_output']):?>
							console.log('[MapEx] auto zoom performed to level <?php echo $settings['zoom']?> for id#<?php echo $settings['map_id']?>');
							<?php endif;?>
						
							<?php if ($settings['debug_output']):?>
							console.log('[MapEx] modal fix performed for id'+modal_id);
							<?php endif;?>
						}
					});
				}
				<?php if ($settings['debug_output']):?>
				else
					console.log('[MapEx] failed to get id for modal dailog for id'+modal_id);
				<?php endif;?>
				
			}
			<?php if ($settings['debug_output']):?>
			else
				console.log('[MapEx] failed to find modal dailog for id'+modal_id);
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
