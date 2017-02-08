<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

require_once(__DIR__.'/views/WidgetkitExMapPlugin.php');
use WidgetkitEx\MapEx\WidgetkitExPlugin;
use WidgetkitEx\MapEx\WidgetkitExMapPlugin;

return array(

    'name' => 'widget/map_ex',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

	'plugin_version' => 'v1.5.7',
	
	'plugin_date' => '08/02/2017',
	
	'plugin_logo' => 'https://raw.githubusercontent.com/wiki/rvalitov/widgetkit-map-ex/images/mapex-logo.png',
	
	'plugin_wiki' => 'https://github.com/rvalitov/widgetkit-map-ex/wiki',
	
    'config' => array(

        'name'  => 'map_ex',
        'label' => 'MapEx',
        'core'  => true,
        'icon'  => 'plugins/widgets/map_ex/widget.svg',
        'view'  => 'plugins/widgets/map_ex/views/widget.php',
        'item'  => array('title', 'content', 'media'),
        'fields' => array(
            array('name' => 'location'),
			array(
                'type' => 'media',
                'name' => 'custom_pin_path',
                'label' => 'Custom Pin Image'
            ),
			array(
                'type' => 'text',
                'name' => 'custom_pin_anchor_x',
                'label' => 'Custom Pin Anchor X (px)'
            ),
			array(
                'type' => 'text',
                'name' => 'custom_pin_anchor_y',
                'label' => 'Custom Pin Anchor Y (px)'
            )
        ),
        'settings' => array(
            'width'                   => 'auto',
            'height'                  => 400,
            'maptypeid'               => 'styled',
            'maptypecontrol'          => false,
			
			//Global settings, shared between all instances of the plugin
			'global' => array(
						'apikey'=>''
					),
					
			//Left for backward compatibility with original Yootheme's Map widget:
            'mapctrl'                 => true,
			
            'zoom'                    => 9,
            'marker'                  => 2,
            'markercluster'           => '',
            'popup_max_width'         => 300,
            'zoomwheel'               => true,
            'draggable'               => true,
            'directions'              => false,
            'disabledefaultui'        => false,
			
			//Extra parameters:
			'autohide'					=> false,
			'zoom_phone_h'				=> '',
			'zoom_tablet'				=> '',
			'zoom_desktop'				=> '',
			'zoom_large'				=> '',
			'maptypecontrol_style'      => 'dropdown_menu',
			'maptype_name'      		=> 'Styled',
			'show_styled'      			=> true,
			'show_roadmap'     			=> true,
			'show_satellite'      		=> true,
			'show_hybrid'      			=> false,
			'show_terrain'      		=> false,
			'styling_mode'      		=> '',
			'styling_json'      		=> '',
			'tiles_color'				=> '',
			'zoomcontrol'               => true,
			//Not working in the current Google Maps API:
			'zoom_style'         		=> '',
			'streetviewcontrol'         => true,
			'rotatecontrol'             => true,
			'directionstext'            => 'Get directions',
			//Not working in the current Google Maps API:
			'scalecontrol'              => false,
			//Not implemented in the current widget:
			'scale_units'               => '',
			'pin_type'            		=> 'default',
			'custom_pin_path'			=> '',
			'custom_pin_anchor_x'		=> '',
			'custom_pin_anchor_y'		=> '',
			'map_center'        	  => '',
			'debug_output'        	  => false,
			'cluster_gridSize'				=> 60,
			'cluster_maxZoom'				=> 0,
			'cluster_minimumClusterSize'	=>	2,
			'clusters'						=> [],

            'styler_invert_lightness' => false,
            'styler_hue'              => '',
            'styler_saturation'       => 0,
            'styler_lightness'        => 0,
            'styler_gamma'            => 0,

            'media'                   => true,
            'image_width'             => 'auto',
            'image_height'            => 'auto',
            'media_align'             => 'top',
            'media_width'             => '1-2',
            'media_breakpoint'        => 'medium',
            'media_border'            => 'none',
            'media_overlay'           => 'icon',
            'overlay_animation'       => 'fade',
            'media_animation'         => 'scale',

            'title'                   => true,
            'content'                 => true,
            'social_buttons'          => true,
            'title_size'              => 'h3',
            'text_align'              => 'left',
            'link'                    => true,
            'link_style'              => 'button',
            'link_text'               => 'Read more',

            'link_target'             => false,
            'class'                   => ''
        )

    ),

    'events' => array(

        'init.site' => function($event, $app) {
			$uikit=(WidgetkitExMapPlugin::getCSSPrefix($app)=='uk') ? 'uikit' : 'uikit2';
            $app['scripts']->add('widgetkit-map-ex', 'plugins/widgets/map_ex/assets/maps.js', array($uikit));
        },

        'init.admin' => function($event, $app) {
			$plugin=new WidgetkitExMapPlugin($app);
			$uikit=(WidgetkitExMapPlugin::getCSSPrefix($app)=='uk') ? 'uikit' : 'uikit2';
			//Shared styles for the Ex widgets
			$app['styles']->add('widget-ex', 'plugins/widgets/map_ex/css/widgetex.css', array('widgetkit-application'));
			//Adding our own translations:
			$app['translator']->addResource('plugins/widgets/map_ex/languages/'.$app['locale'].'.json');
			//Edit template:
            $app['angular']->addTemplate('map_ex.edit', 'plugins/widgets/map_ex/views/edit.php', true);
			//Adding tooltip:
			$app['scripts']->add($uikit.'-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', array($uikit));
			$app['styles']->add($uikit.'-tooltip', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/'.$plugin->getUIkitVersion().'/css/components/tooltip.min.css', array($uikit));
			//jQuery wait plugin:
			$app['scripts']->add('jquery.wait', 'plugins/widgets/map_ex/assets/jquery.wait.min.js', array($uikit));
			//Marked:
			$app['scripts']->add('marked', 'plugins/widgets/map_ex/assets/marked.min.js', array($uikit));
			//Mailchimp for subscription:
			$app['scripts']->add('mailchimp', 'plugins/widgets/map_ex/assets/jquery.formchimp.min.js', array($uikit));
			//Underscore.js
			$app['scripts']->add('underscore', 'plugins/widgets/map_ex/assets/underscore-min.js', array($uikit));
			//Semantic version compare
			$app['scripts']->add('versioncompare', 'plugins/widgets/map_ex/assets/versioncompare.min.js', array($uikit));
			//Marked:
			$app['scripts']->add('replacer', 'plugins/widgets/map_ex/assets/replacer.min.js', array($uikit));
			//Generating dynamic update script:
			$app['scripts']->add('map_ex.dynamic-updater', $plugin->generateUpdaterJS($app), array(), 'string');
			//Generating dynamic MapEx script:
			$app['scripts']->add('map_ex.dynamic-custom', $plugin->generateMapExJS($app), array(), 'string');
        },
				
		'request' => function($event, $app) {
			$global=null;
			if ( (isset($app['request'])) && (isset($app['request']->request)) ) {
				$content=$app['request']->request->get('content');
				if (isset($content['data']['_widget']['data']['global']))
					$global=$content['data']['_widget']['data']['global'];
			}
				
			if ($global){
				//Global is set for valid requests like "Save" and "Save & Close"
				$plugin=new WidgetkitExPlugin($app);
				$plugin->saveGlobalSettings($global);
			}
		}

    )

);
