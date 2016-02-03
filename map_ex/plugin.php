<?php
/*
MapEx plugin for Widgetkit 2.
Author: Ramil Valitov
E-mail: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

return array(

    'name' => 'widget/map_ex',

    'main' => 'YOOtheme\\Widgetkit\\Widget\\Widget',

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
			
			//Left for backward compatibility with original Yootheme's Map widget:
            'mapctrl'                 => true,
			
            'zoom'                    => 9,
            'marker'                  => 2,
            'markercluster'           => false,
            'popup_max_width'         => 300,
            'zoomwheel'               => true,
            'draggable'               => true,
            'directions'              => false,
            'disabledefaultui'        => false,
			
			//Extra parameters:
			'maptypecontrol_style'      => 'dropdown_menu',
			'maptype_name'      		=> 'Styled',
			'show_styled'      			=> true,
			'show_roadmap'     			=> true,
			'show_satellite'      		=> true,
			'show_hybrid'      			=> false,
			'show_terrain'      		=> false,
			'styling_mode'      		=> '',
			'styling_json'      		=> '',
			'zoomcontrol'               => true,
			//Not working in the current Google Maps API:
			'zoom_style'         		=> '',
			'streetviewcontrol'         => true,
			'rotatecontrol'             => true,
			'directionstext'            => 'Get directions',
			//Not working in the current Google Maps API:
			'scalecontrol'              => false,
			'pin_type'            		=> 'default',
			'custom_pin_path'			=> '',
			'custom_pin_anchor_x'		=> '',
			'custom_pin_anchor_y'		=> '',
			'responsive'        	  => true,
			'modal_fix'	        	  => true,
			'map_center'        	  => '',
			'debug_output'        	  => false,

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
			//We replace the original Map widget js file with ours. It must be done to avoid issues with multiple loading of Google Map API JS libraies.
            $app['scripts']->add('widgetkit-map_ex', 'plugins/widgets/map_ex/assets/maps.js', array('uikit'));
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('map_ex.edit', 'plugins/widgets/map_ex/views/edit.php', true);
			//Adding tooltip:
			$app['scripts']->add('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', array('uikit'));
			$app['styles']->add('uikit-tooltip', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/2.24.3/css/components/tooltip.min.css', array('uikit'));
			//Marked:
			$app['scripts']->add('marked', 'plugins/widgets/map_ex/assets/marked.min.js', array('uikit'));
			//Updater:
			$app['scripts']->add('map_ex.updater', 'plugins/widgets/map_ex/assets/updater.js', array('map_ex'));
        }

    )

);
