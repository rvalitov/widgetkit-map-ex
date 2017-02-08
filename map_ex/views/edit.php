<?php
/*
Custom Map widget for Widgetkit 2.
Author: Ramil Valitov
Contacts: ramilvalitov@gmail.com
Web: http://www.valitov.me/
Git: https://github.com/rvalitov/widgetkit-map-ex
*/

use WidgetkitEx\MapEx\WidgetkitExMapPlugin;
$plugin=new WidgetkitExMapPlugin($app);
$is_api_key_supported=$plugin->isWKAPIKeySupported($app);
?>
<div class="uk-grid uk-grid-divider uk-form uk-form-horizontal" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">

        <div class="wk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#nav-content-map'}">
                <li><a href="#">{{'Map' | trans}}</a></li>
                <li><a href="#">{{'Style' | trans}}</a></li>
                <li><a href="#">{{'Media' | trans}}</a></li>
				<li><a href="#">{{'Marker Clustering' | trans}}</a></li>
                <li><a href="#">{{'Content' | trans}}</a></li>
                <li><a href="#">{{'General' | trans}}</a></li>
				<li><a href="#">{{'Global Settings' | trans}}</a></li>
				<li><a href="#">{{'About' | trans}}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="nav-content-map" class="uk-switcher">
            <li>

                <h3 class="wk-form-heading">{{'Map' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-width">{{'Width (px)' | trans}}<span data-uk-tooltip title="{{ 'The width of the widget in pixels or \'auto\'.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-width" class="uk-form-width-medium" type="text" ng-model="widget.data['width']">
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-height">{{'Height (px)' | trans}}<span data-uk-tooltip title="{{ 'The height of the widget in pixels.' |trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-height" class="uk-form-width-medium" type="text" ng-model="widget.data['height']">
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-maptypeid">{{'Map Type' | trans}}<span data-uk-tooltip title="{{ 'The map type used in the widget. The \'Styled\' type is a map that is customized in the \'Styles\' settings section. Other types are the classic Google Map\'s types.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-maptypeid" class="uk-form-width-medium" ng-model="widget.data['maptypeid']">
							<option value="styled">{{'Styled' | trans}}</option>
                            <option value="roadmap">{{'Roadmap' | trans}}</option>
                            <option value="satellite">{{'Satellite' | trans}}</option>
							<option value="hybrid">{{'Hybrid' | trans}}</option>
							<option value="terrain">{{'Terrain' | trans}}</option>
                        </select>
                    </div>
                </div>
						
				<div class="uk-panel uk-panel-box uk-alert-warning uk-margin" ng-if="!widget.data.map_center">
					<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'Some important responsive and rendering features of this widget are disabled, because you did not set the Map Center Point. Please, set the Map Center Point, otherwise the widget may have unexpected behaviour.' |trans}}</p>
				</div>
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-map_center">{{'Map Center Point' | trans}}<span data-uk-tooltip title="{{ 'The map will be displayed in a way that this point will be located right in the center of the map. The center point is defined by its coordinates, e.g. -34.23456, 12.15672.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-map_center" class="uk-form-width-medium" type="text" ng-model="widget.data['map_center']">
                    </div>
                </div>
				
				<h3 class="wk-form-heading">{{'Zoom' | trans}}</h3>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom">{{'Phone Portrait' | trans}}<span data-uk-tooltip title="{{ 'Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom" class="uk-form-width-medium" ng-options="optionvalue for optionvalue in [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19] track by optionvalue" ng-model="widget.data['zoom']">
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom_phone_h">{{'Phone Landscape' | trans}}<span data-uk-tooltip title="{{ 'Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom_phone_h" class="uk-form-width-medium" ng-model="widget.data['zoom_phone_h']">
							<option value="">{{ 'Inherit' | trans}}</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom_tablet">{{'Tablet' | trans}}<span data-uk-tooltip title="{{ 'Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom_tablet" class="uk-form-width-medium" ng-model="widget.data['zoom_tablet']">
							<option value="">{{ 'Inherit' | trans}}</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom_desktop">{{'Desktop' | trans}}<span data-uk-tooltip title="{{ 'Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom_desktop" class="uk-form-width-medium" ng-model="widget.data['zoom_desktop']">
							<option value="">{{ 'Inherit' | trans}}</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom_large">{{'Large Screens' | trans}}<span data-uk-tooltip title="{{ 'Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom_large" class="uk-form-width-medium" ng-model="widget.data['zoom_large']">
							<option value="">{{ 'Inherit' | trans}}</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                    </div>
                </div>
				
                <h3 class="wk-form-heading">{{'Controls' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Disable default UI' | trans}}<span data-uk-tooltip title="{{ 'If checked, then the Google Map API\'s default UI settings are disabled entirely. This option is \'off\' by default.' |trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['disabledefaultui']"> {{'Disable automatic UI behavior' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Type Controls' | trans}}<span data-uk-tooltip title="{{ 'Enables/disables the Map Type control that lets the user toggle between map types (such as Map and Satellite).'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['maptypecontrol']"> {{'Show type controls' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.maptypecontrol == '1'">
                    <span class="uk-form-label">{{'Available types' | trans}}<span data-uk-tooltip title="{{ 'The list of map types that will show in the map\'s type controls. Google displays maximum first 3 map types.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['show_styled']"> {{'Styled map' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_roadmap']"> {{'Roadmap' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_satellite']"> {{'Satellite' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_hybrid']"> {{'Hybrid' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_terrain']"> {{'Terrain' | trans}}</label>
                    </div>
					<div class="uk-panel uk-panel-box uk-alert-warning uk-margin" ng-if="widget.data.show_styled + widget.data.show_roadmap + widget.data.show_satellite + widget.data.show_hybrid + widget.data.show_terrain > 3">
						<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'You have selected more than 3 map types. The map will show only the first 3 of them.' | trans}}</p>
					</div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.maptypecontrol == '1'">
                    <span class="uk-form-label" for="wk-maptypecontrol_style">{{'Type Control Design' | trans}}<span data-uk-tooltip title="{{ 'This option defines the appearance of the type controls. The controls can show as buttons in a horizontal bar or a single button control allowing you to select the map type via a dropdown menu. The default behavior means that the optimal visual style is chosen depending on the screen size.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-maptypecontrol_style" class="uk-form-width-medium" ng-model="widget.data['maptypecontrol_style']">
                            <option value="">{{'Default' | trans}}</option>
							<option value="dropdown_menu">{{'Dropdown menu' | trans}}</option>
                            <option value="horizontal_bar">{{'Horizontal bar' | trans}}</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Zoom Controls' | trans}}<span data-uk-tooltip title="{{ 'Show zoom controls: \'+\' and \'-\' buttons for zooming.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['zoomcontrol']"> {{'Show zoom controls' | trans}}</label>
                    </div>
                </div>
				
				<?php
				/*
				Not working in the current Google Maps API:
				<div class="uk-form-row" ng-if="widget.data['zoomcontrol'] == '1'">
                    <span class="uk-form-label" for="wk-zoom_style">{{'Zoom Style' | trans}}<span data-uk-tooltip title="The style of the zoom control. The 'Default' style means that Google Map will choose the style according to its internal logic."><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom_style" class="uk-form-width-medium" ng-model="widget.data['zoom_style']">
                            <option value="">{{'Default' | trans}}</option>
                            <option value="large">{{'Large (Zoom slider &amp; "+" and "-" buttons)' | trans}}</option>
                            <option value="small">{{'Small ("+" and "-" buttons)' | trans}}</option>
                        </select>
                    </div>
                </div>
				*/
				?>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Street View Controls' | trans}}<span data-uk-tooltip title="{{ 'Enables/disables the Pegman control that lets the user activate a Street View panorama.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['streetviewcontrol']"> {{'Show the Pegman control' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Rotate Controls' | trans}}<span data-uk-tooltip title="{{ 'Enables/disables the appearance of a Rotate control for controlling the orientation of 45° imagery. By default, the control\'s presence is determined by the presence or absence of 45° imagery for the given map type at the current zoom and location. You cannot make the control appear if no 45° imagery is currently available.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['rotatecontrol']"> {{'Show rotate controls' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Scale Controls' | trans}}<span data-uk-tooltip title="{{'Enables/disables the Scale control that provides a simple map scale.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['scalecontrol']"> {{'Show the scale controls' | trans}}</label>
                    </div>
                </div>

				<?php
				/*
				Currently not implemented, because Google doesn't support this feature natively. Various workarounds may not work in localized versions of Google Maps.
				<div class="uk-form-row" ng-if="widget.data['scale_units']">
                    <span class="uk-form-label" for="wk-scale_units">{{'Scale Units System' | trans}}<span data-uk-tooltip title="Units system used in the Scale controls. By default, Google shows the scale in metric system. You may override this unit system by setting one explicitly: metric (distances are shown using kilometers) or imperial (distances are shown using miles)."><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-scale_units" class="uk-form-width-medium" ng-model="widget.data['scale_units']">
							<option value="">{{'Default' | trans}}</option>
                            <option value="metric">{{'Metric (kilometers)' | trans}}</option>
                            <option value="imperial">{{'Imperial (miles)' | trans}}</option>
                        </select>
                    </div>
                </div>
				*/
				?>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Directions Controls' | trans}}<span data-uk-tooltip title="{{'Enables/disables the directions button that opens the Google Map in a new window for navigation and routing.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['directions']"> {{'Show directions controls' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.directions == '1'">
                    <span class="uk-form-label">{{'Directions Label' | trans}}<span data-uk-tooltip title="{{ 'Text to show on the directions button. If left empty, then the default value is used: \'Get directions\'.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<span><input id="wk-directionstext" type="text" ng-model="widget.data['directionstext']"> {{ 'Text displayed on the directions button' | trans}}</span>
                    </div>
                </div>
								
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Scroll Wheel' | trans}}<span data-uk-tooltip title="{{ 'If checked, then user can zoom in and out using the mouse wheel.' |trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['zoomwheel']"> {{'Zoom map on scroll' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Draggable' | trans}}<span data-uk-tooltip title="{{ 'If checked, then user can move the map. If unchecked, then the map\'s location is fixed (static) and can\'t be changed by the user.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['draggable']"> {{'Move map on drag' | trans}}</label>
                    </div>
                </div>
				
				<h3 class="wk-form-heading">{{'Markers' | trans}}</h3>
				
                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-marker">{{'Marker' | trans}}<span data-uk-tooltip title="{{'Select if you want to display markers or not. If yes, then you can choose the popup window\'s behaviour.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-marker" class="uk-form-width-medium" ng-model="widget.data['marker']">
                            <option value="0">{{'Hide' | trans}}</option>
                            <option value="1">{{'Show' | trans}}</option>
                            <option value="2">{{'Show and enable Popup' | trans}}</option>
                            <option value="3">{{'Show with opened Popup' | trans}}</option>
                        </select>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.marker != '0'">
                            <label><input class="uk-form-width-mini" type="text" ng-model="widget.data['popup_max_width']"> {{'Popup max width (px)' | trans}}</label>
                        </p>
						<p class="uk-form-controls-condensed" ng-if="widget.data.marker == '2'">
                            <span><input class="uk-form-width-mini" type="checkbox" ng-model="widget.data['autohide']"> {{'Single popup' | trans}}<span data-uk-tooltip title="{{'This option controls the maximum number of popup windows that can be visible at once. If checked, then maximum one popup is visible at a time and other popups will automatically hide. If unchecked, then any number of popups can be visible without restrictions.'|trans}}"><i></i></span></span>
                        </p>
                    </div>
                </div>

				<div class="uk-form-row" ng-if="widget.data['marker'] > 0">
                    <span class="uk-form-label" for="wk-pin_type">{{'Marker Pin Icon' | trans}}<span data-uk-tooltip title="{{'Select a type of pin that will be used in map markers.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-pin_type" class="uk-form-width-medium" ng-model="widget.data['pin_type']">
                            <option value="default">{{'Default' | trans}}</option>
                            <option value="">{{'Custom' | trans}}</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data['pin_type'] == ''">
                    <span class="uk-form-label">{{'Image' | trans}}<span data-uk-tooltip title="{{'Path to an image file that will be used as a custom pin icon. PNG and SVG image formats are recommended. This setting sets the default icon for all map markers (elements). If this field is left empty, then a default icon will be used. You can set a unique icon for any map marker: you can do this by setting a \'Custom Pin Image\' field in the \'Content Settings\' tab. This field is visible and available only if you use \'Custom\' content source type. The \'Custom Pin Image\' field has a higher priority and if set will override the value of this setting.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <field-media title="item.title" media="widget.data.custom_pin_path"></field-media>
					</div>
				</div>
								
				<div class="uk-form-row" ng-if="widget.data['pin_type'] == ''">
					<span class="uk-form-label">{{'Anchor' | trans}}<span data-uk-tooltip title="{{'The anchor is a place where the icon\'s hotspot is located. The position is defined in pixels and is relative to the the image\'s dimensions, so that the bottom left corner of the image is a zero-point (0,0); axes have standard orientation: the X to the right; Y to the top. If the position is empty then the bottom center of the image is set as the anchor.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['custom_pin_anchor_x']"> {{'X (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['custom_pin_anchor_y']"> {{'Y (px)' | trans}}</label>
                        </p>
                    </div>
				</div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Style' | trans}}</h3>

				<div class="uk-panel uk-panel-box uk-alert">
					<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{ 'This section allows you to create your own uinque map type with custom styles. You can achieve sophisticated results, e.g. set individual colors for different elements (countries, roads, POI, etc.) and control their visibility.'|trans}}</p>
				</div>
				
				<div class="uk-panel uk-panel-box uk-alert uk-alert-warning" ng-if="widget.data['maptypeid'] != 'styled'">
					<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{'The styles are applied only to a map type called "Styled" (see the "Map Type" option in the "Map" tab). You have another type of map selected. You should probably select the "Styled" map type if you want to use the styling.'|trans}}</p>
				</div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Name' | trans}}<span data-uk-tooltip title="{{ 'The label that will be displayed in the type controls to refer to your custom map style. If left empty, then the default value \'Styled\' is used'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<span><input id="wk-maptype_name" type="text" ng-model="widget.data['maptype_name']"></span>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Styling Mode' | trans}}<span data-uk-tooltip title="{{ 'There are 2 modes: \'Simple\' is the default that allows you to do some basic styling. For for arbitrary styling choose \'Advanced\' mode.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<label><input type="radio" ng-model="widget.data['styling_mode']" value="" checked> {{'Simple (default)' | trans}}</label>
						<label><input type="radio" ng-model="widget.data['styling_mode']" value="json" checked> {{'Advanced' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data['styling_mode'] == 'json'">
					<span class="uk-form-label">{{'Custom Style (JSON)' | trans}}<span data-uk-tooltip title="{{ 'Enter the text for you custom style. The data must be in JSON format and comply with Google Maps API rules. You may use the Google\'s Styled Map Wizard tool below to easily create the desired styling.'|trans}}"><i></i></span></span>
					<div class="uk-form-controls">
						<textarea id="wk-styling_json" class="uk-width-1-1" ng-model="widget.data['styling_json']" rows="10"></textarea>
					</div>
				</div>
				
				<div class="uk-form-row uk-text-center" ng-if="widget.data['styling_mode'] == 'json'">
					<a href="http://googlemaps.github.io/js-samples/styledmaps/wizard/index.html" class="uk-button uk-button-primary uk-container-center" target="_blank">{{'Launch the Styled Maps Wizard'|trans}}<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>
				</div>
				
                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label">{{'Invert' | trans}}<span data-uk-tooltip title="{{'Simply inverts the existing lightness. This is useful, for example, for quickly switching to a darker map with white text.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span><input type="checkbox" ng-model="widget.data['styler_invert_lightness']"> {{'Invert lightness' | trans}}</span>
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-hue">{{'Hue' | trans}}<span data-uk-tooltip title="{{ 'Enter an RGB hex string that indicates the basic color. The default value is #ff0000.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-hue" class="uk-form-width-small" type="text" ng-model="widget.data['styler_hue']"> ({{'e.g. %example%' | trans: {example:'#ff0000'} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-saturation">{{'Saturation' | trans}}<span data-uk-tooltip title="{{ 'Saturation is a floating point value between -100 and 100 that indicates the percentage change in intensity of the basic color to apply to the element.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-saturation" class="uk-form-width-small" type="text" ng-model="widget.data['styler_saturation']"> ({{'%from% to %to%' | trans: {from:-100, to:100} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-lightness">{{'Lightness' | trans}}<span data-uk-tooltip title="{{ 'Lightness is a floating point value between -100 and 100 that indicates the percentage change in brightness of the element. Negative values increase darkness (where -100 specifies black) while positive values increase brightness (where +100 specifies white).'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-lightness" class="uk-form-width-small" type="text" ng-model="widget.data['styler_lightness']"> ({{'%from% to %to%' | trans: {from:-100, to:100} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-gamma">{{'Gamma' | trans}}<span data-uk-tooltip title="{{ 'Gamma is a floating point value between 0.01 and 10.0 (where 1.0 applies no correction) indicates the amount of gamma correction to apply to the element. Gammas modify the lightness of hues in a non-linear fashion, while not impacting white or black values. Gammas are typically used to modify the contrast of multiple elements. For example, you could modify the gamma to increase or decrease the contrast between the edges and interiors of elements. Low gamma values (less than 1) increase contrast, while high values (greater than 1) decrease contrast.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-gamma" class="uk-form-width-small" type="text" ng-model="widget.data['styler_gamma']"> ({{'%from% to %to%' | trans: {from:0, to:10} }})
                    </div>
                </div>

				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-tiles_color">{{'Tiles Background' | trans}}<span data-uk-tooltip title="{{ 'Color used for the background of the Map. This color will be visible when tiles have not yet loaded as the user pans. Example: #ff0000. The default value is empty that uses the default color of Google Maps (currently it is light grey).'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<span><input id="wk-tiles_color" type="text" ng-model="widget.data['tiles_color']"></span>
                    </div>
                </div>
            </li>
            <li>

                <h3 class="wk-form-heading">{{'Media' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}<span data-uk-tooltip title="{{ 'Display the image inside the popup.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['media']"> {{'Show media' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Image' | trans}}<span data-uk-tooltip title="{{ 'Set the width and height of the image in pixels. Use \'auto\' for auto size.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_width']"> {{'Width (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_height']"> {{'Height (px)' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-media-align">{{'Alignment' | trans}}<span data-uk-tooltip title="{{'Define the image alignment regarding to the content in the popup.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-media-align" class="uk-form-width-medium" ng-model="widget.data['media_align']">
                            <option value="top">{{'Above Title' | trans}}</option>
                            <option value="bottom">{{'Below Title' | trans}}</option>
                            <option value="left">{{'Left' | trans}}</option>
                            <option value="right">{{'Right' | trans}}</option>
                        </select>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.media_align == 'left' || widget.data.media_align == 'right'">
                            <label>
                                <select class="uk-form-width-mini" ng-model="widget.data['media_width']">
                                    <option value="1-5">20%</option>
                                    <option value="1-4">25%</option>
                                    <option value="3-10">30%</option>
                                    <option value="1-3">33%</option>
                                    <option value="2-5">40%</option>
                                    <option value="1-2">50%</option>
                                </select>
                                {{'Column Width' | trans}}<span data-uk-tooltip title="{{'Defines the size of the column for the image. Another width that is left will be used to display the content.'|trans}}"><i></i></span>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.media_align == 'left' || widget.data.media_align == 'right'">
                            <label>
                                <select class="uk-form-width-small" ng-model="widget.data['media_breakpoint']">
                                    <option value="small">{{'Phone Landscape' | trans}}</option>
                                    <option value="medium">{{'Tablet' | trans}}</option>
                                    <option value="large">{{'Desktop' | trans}}</option>
                                </select>
                                {{'Breakpoint' | trans}}<span data-uk-tooltip title="{{'Defines screen size when the columns will stack.'|trans}}"><i></i></span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-media-border">{{'Border' | trans}}<span data-uk-tooltip title="{{'Defines the style of the image\'s border inside the Popup.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-media-border" class="uk-form-width-medium" ng-model="widget.data['media_border']">
                            <option value="none">{{'None' | trans}}</option>
                            <option value="circle">{{'Circle' | trans}}</option>
                            <option value="rounded">{{'Rounded' | trans}}</option>
                        </select>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Overlay' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-media-overlay">{{'Overlay' | trans}}<span data-uk-tooltip title="{{'Define what will be displayed inside the overlay or hide the overlay.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-media-overlay" class="uk-form-width-medium" ng-model="widget.data['media_overlay']">
                            <option value="none">{{'None' | trans}}</option>
                            <option value="link">{{'Link' | trans}}</option>
                            <option value="icon">{{'Icon' | trans}}</option>
                            <option value="image">{{'Image' | trans}} ({{'If second one exists' | trans}})</option>
                            <option value="social-buttons">{{'Social Buttons' | trans}} ({{'If enabled' | trans}})</option>
                        </select>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.media_overlay == 'icon' || widget.data.media_overlay == 'social-buttons'">
                            <label>
                                <select class="uk-form-width-small" ng-model="widget.data['overlay_animation']">
                                    <option value="fade">{{'Fade' | trans}}</option>
                                    <option value="slide-top">{{'Slide Top' | trans}}</option>
                                    <option value="slide-bottom">{{'Slide Bottom' | trans}}</option>
                                    <option value="slide-left">{{'Slide Left' | trans}}</option>
                                    <option value="slide-right">{{'Slide Right' | trans}}</option>
                                </select>
                                {{'Animation' | trans}}<span data-uk-tooltip title="{{'The animation that will be applied to the overlay when being displayed on hover.'|trans}}"><i></i></span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-thumbnail-animation">{{'Image Animation' | trans}}<span data-uk-tooltip title="{{'The animation that will be applied to the image on hover.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-thumbnail-animation" class="uk-form-width-medium" ng-model="widget.data['media_animation']">
                            <option value="none">{{'None' | trans}}</option>
                            <option value="fade">{{'Fade' | trans}}</option>
                            <option value="scale">{{'Scale' | trans}}</option>
                            <option value="spin">{{'Spin' | trans}}</option>
                            <option value="grayscale">{{'Grayscale' | trans}}</option>
                        </select>
                    </div>
                </div>

            </li>
						
			<li>
				<h3 class="wk-form-heading">{{'Markers Clustering Manager' | trans}}</h3>
				
				<div class="uk-panel uk-panel-box uk-alert">
					<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{'Sometimes you need to display a large number of locations or markers. Naively plotting thousands of markers on a map can quickly lead to a degraded user experience. Too many markers on the map cause both visual overload and sluggish interaction with the map. To overcome this poor performance, clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form.'|trans}}</p>
				</div>
				
                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-cluster_type">{{'Clustering Mode' | trans}}<span data-uk-tooltip title="{{'Defines how clustering will work. If enabled, there are 2 modes: \'Simple\' mode mimics the standard behaviour of standard Map widget; \'Advanced\' - is a superior mode that gives a full control of the clustering.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-cluster_type" class="uk-form-width-medium" ng-model="widget.data['markercluster']">
                            <option value="">{{'None' | trans}}</option>
                            <option value="default">{{'Simple' | trans}}</option>
							<option value="custom">{{'Advanced' | trans}}</option>
                        </select>
                    </div>
                </div>
				
				<h3 class="wk-form-heading" ng-if="widget.data['markercluster']=='custom'">{{'Clustering Options' | trans}}</h3>
				
				<div class="uk-form-row" ng-if="widget.data['markercluster']=='custom'">
					<span class="uk-form-label" for="wk-cluster_gridSize">{{'Grid Size' | trans}}<span data-uk-tooltip title="{{ 'The grid size of a cluster in pixels. The higher values join the markers into clusters more condensed, therefore resulting in a lower number of total clusters. The default value is 60.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<select id="wk-cluster_gridSize" class="uk-form-width-medium" ng-options="optionvalue for optionvalue in [20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,105,110,115,120,125,130,135,140] track by optionvalue" ng-model="widget.data['cluster_gridSize']">
                        </select>
                    </div>
				</div>
				
				<div class="uk-form-row" ng-if="widget.data['markercluster']=='custom'">
                    <span class="uk-form-label" for="wk-cluster_maxZoom">{{'Max Zoom Level' | trans}}<span data-uk-tooltip title="{{ 'The maximum zoom level that a marker can be part of a cluster.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-cluster_maxZoom" class="uk-form-width-medium" ng-options="optionvalue for optionvalue in [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19] track by optionvalue" ng-model="widget.data['cluster_maxZoom']">
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data['markercluster']=='custom'">
                    <span class="uk-form-label" for="wk-cluster_minimumClusterSize">{{'Minimum Markers' | trans}}<span data-uk-tooltip title="{{ 'The minimum number of markers to be in a cluster before the markers are hidden and a count is shown.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-cluster_minimumClusterSize" class="uk-form-width-medium" ng-options="optionvalue for optionvalue in [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20] track by optionvalue" ng-model="widget.data['cluster_minimumClusterSize']">
                        </select>
                    </div>
                </div>
				
				<h3 class="wk-form-heading" ng-if="widget.data['markercluster']=='custom'">{{'Cluster Markers' | trans}}</h3>
				
				<div class="uk-panel uk-panel-box uk-alert" ng-if="widget.data['markercluster']=='custom'">
					<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{'Each cluster belongs to one of the levels (groups) defined below. Levels differ by density: the more items a cluster contains, the higher level ID this cluster belongs to. You must define at least 1 cluster level.'|trans}}</p>
				</div>
				
				<!-- This is the container of the toggling elements -->
				<div class="uk-tab-center" ng-if="widget.data['markercluster']=='custom'">
					<ul class="uk-tab" data-uk-switcher="{connect:'#mapex-clusters'}">
						<li><a href="">{{'Marker Options'|trans}}</a></li>
						<li><a href="">{{'Marker Library'|trans}}</a></li>
					</ul>
				</div>

				<!-- This is the container of the content items -->
				<ul id="mapex-clusters" class="uk-switcher uk-margin" ng-if="widget.data['markercluster']=='custom'">
					<li>
						<h3 class="uk-text-center">{{'Marker Options'|trans}}</h3>
						
						<span class="cluster-level" ng-repeat-start="value in widget.data['clusters'] track by $index">&nbsp;</span>
						
						<div>
						<hr>
						<h4>{{ 'Cluster Marker Level #%level%' | trans:{level:(($index+1)| number:0)} }}</h4>
				
						<div class="uk-panel uk-panel-box uk-alert-warning uk-margin" ng-if="!value['icon']">
							<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'This level is ignored, because you did not specify the icon to use.'|trans}}</p>
						</div>
						<div class="uk-form-row">
							<span class="uk-form-label">{{'Icon' | trans}}<span data-uk-tooltip title="{{ 'Path to an image file that will be used as a cluster\'s icon. PNG and SVG image formats are recommended.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<field-media id="cluster-{{$index+1}}-icon" media="value['icon']" options="value['options']"></field-media>
							</div>
						</div>
						
						<div class="uk-form-row">
							<span class="uk-form-label">{{'Image' | trans}}<span data-uk-tooltip title="{{ 'Set the width and height of the image in pixels. Use \'auto\' for auto size.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-width" type="text" ng-model="value['width']"> {{'Width (px)' | trans}}</label>
								<p class="uk-form-controls-condensed">
									<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-height" type="text" ng-model="value['height']"> {{'Height (px)' | trans}}</label>
								</p>
							</div>
						</div>
						
						<div class="uk-form-row">
							<span class="uk-form-label">{{'Icon Anchor' | trans}}<span data-uk-tooltip title="{{ 'The anchor is a place where the icon\'s hotspot is located. The position is defined in pixels and is relative to the the image\'s dimensions, so that the upper left corner of the image is a zero-point (0,0); axes have the following orientation: the X to the right; Y to the bottom. If any of the values is empty then the center of the image is set as the anchor.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-icon_x" type="text" ng-model="value['icon_anchor_x']"> {{'X (px)' | trans}}</label>
								<p class="uk-form-controls-condensed">
									<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-icon_y" type="text" ng-model="value['icon_anchor_y']"> {{'Y (px)' | trans}}</label>
								</p>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{'Label Anchor' | trans}}<span data-uk-tooltip title="{{ 'The anchor is a place where the text label is located. The position is defined in pixels and is relative to the the image\'s dimensions. A positive value sets the offset relative to the top/left side of the image; negative value - relative to the right/bottom side of the image; zero - means to use center/middle position. The absolute value of the offset must be less than corresponding image\'s dimension, otherwise such value is considered to be invalid and is ignored.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-label_x" type="text" ng-model="value['label_anchor_x']"> {{'X (px)' | trans}}</label>
								<p class="uk-form-controls-condensed">
									<label><input class="uk-form-width-small" id="cluster-{{$index+1}}-label_y" type="text" ng-model="value['label_anchor_y']"> {{'Y (px)' | trans}}</label>
								</p>
							</div>
						</div>
						
						<div class="uk-form-row">
							<span class="uk-form-label">{{'Text Size' | trans}}<span data-uk-tooltip title="{{ 'The size of text label of the cluster marker in pixels. The default value is 11.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<label><select class="uk-form-width-medium" id="cluster-{{$index+1}}-size" ng-options="optionvalue for optionvalue in [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] track by optionvalue" ng-model="value.textSize">
								</select> {{'(px)' | trans}}</label>
							</div>
						</div>

						<div class="uk-form-row">
							<span class="uk-form-label">{{'Text Color' | trans}}<span data-uk-tooltip title="{{'Color of text of the cluster marker. Example: #ff0000. Standard HTML color names are also valid. The default value is black.'|trans}}"><i></i></span></span>
							<div class="uk-form-controls">
								<span><input type="text" id="cluster-{{$index+1}}-color" ng-model="value['textColor']"></span>
							</div>
						</div>
						
						<div class="uk-text-right">
							<button class="uk-button uk-button-danger uk-margin-top" onclick="UIkit.notify(jQuery('#level-removed').text(), {'timeout':3000,'pos':'top-center','status':'info'});" ng-click="widget.data['clusters'].splice($index,1)"><i class="uk-icon uk-icon-minus-circle uk-margin-small-right"></i>{{'Remove Level'|trans}}</button>
						</div>
						
						</div>
						<span ng-repeat-end>&nbsp;</span>
						
						<div id="level-added" class="uk-hidden">
							{{'New cluster level added'|trans}}
						</div>
						<div id="level-removed" class="uk-hidden">
							{{'Cluster level removed'|trans}}
						</div>
						
						<hr>
						<div class="uk-margin-top uk-grid uk-grid-width-1-2">
							<div>
								<button id="mapex-add-level" class="uk-button uk-button-success" onclick="UIkit.notify(jQuery('#level-added').text(), {'timeout':3000,'pos':'top-center','status':'info'});" ng-click="widget.data['clusters'].push({'icon':'','width':'auto','height':'auto','icon_anchor_x':0,'icon_anchor_y':0,'label_anchor_x':0,'label_anchor_y':0,'textSize':10,'textColor':'black'})"><i class="uk-icon uk-icon-plus-circle uk-margin-small-right"></i>{{'Add Level'|trans}}</button>
							</div>
							<div class="uk-text-right">
								<button id="mapex-clear-levels" class="uk-button uk-button-danger" ng-click="widget.data['clusters']=[]"><i class="uk-icon uk-icon-trash uk-margin-small-right"></i>{{'Remove all levels'|trans}}</button>
							</div>
						</div>
					</li>
					<li>
						<h3 class="uk-text-center">{{'Marker Library'|trans}}</h3>
						<div class="uk-panel uk-panel-box uk-alert">
							<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{'You can load a predefined set of markers from our collection that are ready for use in your project. Activating any set will overwrite all the settings on the "Marker Options" tab. You may check our collection from time to time to see newly added items.'|trans}}</p>
						</div>

						<div class="uk-text-center">
							<button class="uk-button" onclick="loadClusterCollections()"><i class="uk-icon uk-icon-download uk-margin-small-right"></i>{{'Retrieve active collection from Internet'|trans}}</button>
						</div>
						
						<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top" id="cluster-collection">
						</div>
				
					</li>
				</ul>

			</li>
			
            <li>

                <h3 class="wk-form-heading">{{'Text' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}<span data-uk-tooltip title="{{'Show or hide title, content and social buttons.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['title']"> {{'Show title' | trans}}</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['content']"> {{'Show content' | trans}}</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['social_buttons']"> {{'Show social buttons' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-title-size">{{'Title Size' | trans}}<span data-uk-tooltip title="{{'Define the font size of the title.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-title-size" class="uk-form-width-medium" ng-model="widget.data['title_size']">
                            <option value="h1">H1</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                            <option value="large">{{'Extra Large' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-text-align">{{'Alignment' | trans}}<span data-uk-tooltip title="{{'Define the text alignment.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-text-align" class="uk-form-width-medium" ng-model="widget.data['text_align']">
                            <option value="left">{{'Left' | trans}}</option>
                            <option value="right">{{'Right' | trans}}</option>
                            <option value="center">{{'Center' | trans}}</option>
                        </select>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Link' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}<span data-uk-tooltip title="{{'Display the Read More link. The link URL is added to each item in the Content Manager.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link']"> {{'Show link' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-link-style">{{'Style' | trans}}<span data-uk-tooltip title="{{'Set the style of the Read More link.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-link-style" class="uk-form-width-medium" ng-model="widget.data['link_style']">
                            <option value="text">{{'Text' | trans}}</option>
                            <option value="button">{{'Button' | trans}}</option>
                            <option value="primary">{{'Button Primary' | trans}}</option>
                            <option value="button-large">{{'Button Large' | trans}}</option>
                            <option value="primary-large">{{'Button Large Primary' | trans}}</option>
                            <option value="button-link">{{'Button Link' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-link-text">{{'Text' | trans}}<span data-uk-tooltip title="{{'Define the link text.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-link-text" class="uk-form-width-medium" type="text" ng-model="widget.data['link_text']">
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'General' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Link Target' | trans}}<span data-uk-tooltip title="{{'Enables/disables opening all links in a new window of the browser. Otherwise, they open in the same window.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link_target']"> {{'Open all links in a new window' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-class">{{'HTML Class' | trans}}<span data-uk-tooltip title="{{'Adds a custom CSS class to the widget. You can specify several classes using space between them.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-class" class="uk-form-width-medium" type="text" ng-model="widget.data['class']">
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Debug output' | trans}}<span data-uk-tooltip title="{{'If checked, then the MapEx will print some debug and status information to the browser\'s console window. This option should be off by default and is intended to be used for investigating problems with the widget.'|trans}}"><i></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['debug_output']"> {{'Print debug info to browser console' | trans}}</label>
                    </div>
                </div>
            </li>
			<li>

                <h3 class="wk-form-heading">{{'Global Settings' | trans}}</h3>
				
				<div class="uk-panel uk-panel-box uk-alert">
					<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>{{'Global settings are shared between all the instances of this plugin. If you change any options below, then they will automatically change in all other plugins of this type, too.'|trans}}</p>
				</div>
		
				<h3 class="wk-form-heading">{{'Google Maps API key' | trans}}</h3>
		
				<?php if ($is_api_key_supported) : ?>
				<div class="uk-panel uk-panel-box uk-alert">
					<p class="uk-text-center"><i class="uk-icon-info-circle uk-margin-small-right"></i>{{ 'Your version of Widgetkit supports Google Maps API Key natively. You should edit the key in the Widgetkit settings' |trans }}<a href="<?php echo $app['config']->get('settings-page'); ?>"><i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a></p>
				</div>
                <?php endif; ?>
				<?php if ($is_api_key_supported): ?>
				<div class="uk-panel uk-panel-box uk-alert-warning uk-margin <?php echo (!$app['config']->get('googlemapseapikey'))?'':'uk-hidden' ?>">
				<?php else:?>
				<div class="uk-panel uk-panel-box uk-alert-warning uk-margin" ng-if="!widget.data.global['apikey']">
				<?php endif; ?>
					<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>{{ 'The Google API key is not defined. Please, enter your API key in order to use the Google Map service. You can obtain the key from Google.' | trans}}</p>
					<p class="uk-text-center">
					<a class="uk-button" href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank"><i class="uk-icon uk-icon-external-link uk-margin-small-right"></i>{{'More info' |trans}}</a>
					</p>
				</div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-width">{{'API key' | trans}}<span data-uk-tooltip title="{{ 'Google API key that you should obtain from Google in order to use the Google Map services.' | trans}}"><i></i></span></span>
                    <div class="uk-form-controls">
						<?php if ($is_api_key_supported): ?>
						<input id="wk-apikey" class="uk-form-width-medium" type="text" disabled value="<?php echo $app['config']->get('googlemapseapikey')?>">
						<?php else:?>
                        <input id="wk-apikey" class="uk-form-width-medium" type="text" ng-model="widget.data.global['apikey']">
						<?php endif; ?>
                    </div>
					<div class="uk-form-controls uk-margin-top">
						<?php if ($is_api_key_supported): ?>
						<button class="uk-button" onclick="WKverifyMapsApiKey()" <?php if (!$app['config']->get('googlemapseapikey')) echo "disabled";?>>{{ 'Verify key' | trans}}</button>
						<?php else:?>
                        <button class="uk-button" ng-disabled="!widget.data.global['apikey']" onclick="WKverifyMapsApiKey()">{{ 'Verify key' | trans}}</button>
						<?php endif; ?>
                    </div>
                </div>
				
			</li>
			<li>

                <h3 class="wk-form-heading">{{'About' | trans}}</h3>

				<?php $plugin->printAboutInfo($app);?>

				<h3 class="wk-form-heading">{{'Newsletter' | trans}}</h3>
	
				<?php $plugin->printNewsletterInfo($app);?>
				
				<h3 class="wk-form-heading">{{'Donation' | trans}}</h3>
				<?php $plugin->printDonationInfo($app);?>

            </li>
        </ul>

    </div>
</div>