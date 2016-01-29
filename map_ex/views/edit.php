<div class="uk-grid uk-grid-divider uk-form uk-form-horizontal" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">

        <div class="wk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#nav-content-map'}">
                <li><a href="#">{{'Map' | trans}}</a></li>
                <li><a href="#">{{'Style' | trans}}</a></li>
                <li><a href="#">{{'Media' | trans}}</a></li>
                <li><a href="#">{{'Content' | trans}}</a></li>
                <li><a href="#">{{'General' | trans}}</a></li>
				<li><a href="#">{{'About' | trans}}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="nav-content-map" class="uk-switcher">
            <li>

                <h3 class="wk-form-heading">{{'Map' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-width">{{'Width (px)' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The width of the widget in pixels or 'auto'."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-width" class="uk-form-width-medium" type="text" ng-model="widget.data['width']">
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-height">{{'Height (px)' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The height of the widget in pixels."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-height" class="uk-form-width-medium" type="text" ng-model="widget.data['height']">
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-maptypeid">{{'Map Type' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The map type used in the widget. The 'Styled' type is a map that is customized in the 'Styles' settings section. Other types are the classic Google Map's types."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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

				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-zoom">{{'Zoom Level' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Initial zoom level of the map. The higher numbers - the greater the zoom is (more detailed map). The value 0 corresponds to a map of the Earth fully zoomed out."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-zoom" class="uk-form-width-medium" ng-model="widget.data['zoom']">
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
                    <span class="uk-form-label">{{'Responsive' | trans}}<span  data-uk-tooltip title="If checked, then the map will automatically pan and zoom to the Map Center Point (see below) every time the window is resized or when orientation of the device changes (e.g. for smartphones or tablets). Note, that panning and zooming is done to the initial position of the map, i.e. if the user was interacting with the map and changed its position or zooming level, then this changes will be lost."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['responsive']"> {{'Adjust map on window resize and orientation change' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label" for="wk-map_center">{{'Map Center Point' | trans}}<span data-uk-tooltip title="If set (not blank), then the map will be displayed in a way that this point will be located right in the center of the map. The center point is defined by its coordinates, e.g. -34.23456, 12.15672. Leave this field blank if you want to automatically set the best optimal center of the map. However, automatic approach may not always give the expected results."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-map_center" class="uk-form-width-medium" type="text" ng-model="widget.data['map_center']">
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.map_center">
                    <span class="uk-form-label">{{'Modal dialog fix' | trans}}<span  data-uk-tooltip title="If checked, the widget applies a fix for the problem of incorrect rendering inside UIKit modal dialogs. Besides, it resets the map to its default position and zoom level every time the modal is displayed, e.g. if the user opens a modal, changes the map position or zoom level, closes the modal and then opens the modal again, the map will be reset to its initial configuration."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['modal_fix']" ng-checked="widget.data.map_center" ng-disabled="! widget.data.map_center"> {{'Correct visualization inside modal' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="! widget.data.map_center">
                    <span class="uk-form-label">{{'Modal dialog fix' | trans}}<span  data-uk-tooltip title="If checked, the widget applies a fix for the problem of incorrect rendering inside UIKit modal dialogs. Besides, it resets the map to its default position and zoom level every time the modal is displayed, e.g. if the user opens a modal, changes the map position or zoom level, closes the modal and then opens the modal again, the map will be reset to its initial configuration. <strong>You must set the 'Map Center Point' in order this feature to work.</strong>"><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" disabled> {{'Correct visualization inside modal' | trans}}</label>
                    </div>
                </div>
								
                <h3 class="wk-form-heading">{{'Controls' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Disable default UI' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="If checked, then the Google Map API's default UI settings are disabled entirely. This option is 'off' by default."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['disabledefaultui']"> {{'Disable automatic UI behavior' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Type Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enables/disables the Map Type control that lets the user toggle between map types (such as Map and Satellite)."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['maptypecontrol']"> {{'Show type controls' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.maptypecontrol == '1'">
                    <span class="uk-form-label">{{'Available types' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The list of map types that will show in the map's type controls. Google displays maximum first 3 map types."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['show_styled']"> {{'Styled map' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_roadmap']"> {{'Roadmap' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_satellite']"> {{'Satellite' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_hybrid']"> {{'Hybrid' | trans}}</label>
						<label><input type="checkbox" ng-model="widget.data['show_terrain']"> {{'Terrain' | trans}}</label>
                    </div>
					<div class="uk-panel uk-panel-box uk-alert-warning" ng-if="widget.data.show_styled + widget.data.show_roadmap + widget.data.show_satellite + widget.data.show_hybrid + widget.data.show_terrain > 3">
						<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>You have selected more than 3 map types. The map will show only the first 3 of them.</p>
					</div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.maptypecontrol == '1'">
                    <span class="uk-form-label" for="wk-maptypecontrol_style">{{'Type Control Design' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="This option defines the appearance of the type controls. The controls can show as buttons in a horizontal bar or a single button control allowing you to select the map type via a dropdown menu. The default behavior means that the optimal visual style is chosen depending on the screen size."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-maptypecontrol_style" class="uk-form-width-medium" ng-model="widget.data['maptypecontrol_style']">
                            <option value="">{{'Default' | trans}}</option>
							<option value="dropdown_menu">{{'Dropdown menu' | trans}}</option>
                            <option value="horizontal_bar">{{'Horizontal bar' | trans}}</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Zoom Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Show zoom controls: '+' and '-' buttons for zooming."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['zoomcontrol']"> {{'Show zoom controls' | trans}}</label>
                    </div>
                </div>
				
				<?php
				/*
				Not working in the current Google Maps API:
				<div class="uk-form-row" ng-if="widget.data['zoomcontrol'] == '1'">
                    <span class="uk-form-label" for="wk-zoom_style">{{'Zoom Style' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The style of the zoom control. The 'Default' style means that Google Map will choose the style according to its internal logic."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label">{{'Street View Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enables/disables the Pegman control that lets the user activate a Street View panorama."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['streetviewcontrol']"> {{'Show the Pegman control' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Rotate Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enables/disables the appearance of a Rotate control for controlling the orientation of 45&deg; imagery. By default, the control's presence is determined by the presence or absence of 45&deg; imagery for the given map type at the current zoom and location. You cannot make the control appear if no 45&deg; imagery is currently available."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['rotatecontrol']"> {{'Show rotate controls' | trans}}</label>
                    </div>
                </div>
				
				<?php
				/*
				Not working in the current Google Maps API:
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Scale Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enables/disables the Scale control that provides a simple map scale."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['scalecontrol']"> {{'Show the scale controls' | trans}}</label>
                    </div>
                </div>
				*/
				?>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Directions Controls' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enables/disables the directions button that opens the Google Map in a new window for navigation and routing."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['directions']"> {{'Show directions controls' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data.directions == '1'">
                    <span class="uk-form-label">{{'Directions Label' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Text to show on the directions button. If left empty, then the default value is used: 'Get directions'."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
						<span><input id="wk-directionstext" type="text" ng-model="widget.data['directionstext']"> {{ 'Text displayed on the directions button' | trans}}</span>
                    </div>
                </div>
								
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Scroll Wheel' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="If checked, then user can zoom in and out using the mouse wheel."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['zoomwheel']"> {{'Zoom map on scroll' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Draggable' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="If checked, then user can move the map. If unchecked, then the map's location is fixed (static) and can't be changed by the user."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['draggable']"> {{'Move map on drag' | trans}}</label>
                    </div>
                </div>
				
				<h3 class="wk-form-heading">{{'Markers' | trans}}</h3>
				
                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-marker">{{'Marker' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Select if you want to display markers or not. If yes, then you can choose the popup window's behaviour."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    </div>
                </div>

				<div class="uk-form-row" ng-if="widget.data['marker'] > 0">
                    <span class="uk-form-label" for="wk-pin_type">{{'Marker Pin Icon' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Select a type of pin that will be used in map markers."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <select id="wk-pin_type" class="uk-form-width-medium" ng-model="widget.data['pin_type']">
                            <option value="default">{{'Default' | trans}}</option>
                            <option value="">{{'Custom' | trans}}</option>
                        </select>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data['pin_type'] == ''">
                    <span class="uk-form-label">{{'Image' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Path to an image file that will be used as a custom pin icon. PNG and SVG image formats are recommended. This setting sets the default icon for all map markers (elements). If this field is left empty, then a default icon will be used. You can set a unique icon for any map marker: you can do this by setting a 'Custom Pin Image' field in the 'Content Settings' tab. This field is visible and available only if you use 'Custom' content source type. The 'Custom Pin Image' field has a higher priority and if set will override the value of this setting."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <field-media title="item.title" media="widget.data.custom_pin_path"></field-media>
					</div>
				</div>
								
				<div class="uk-form-row" ng-if="widget.data['pin_type'] == ''">
					<span class="uk-form-label">{{'Anchor' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The anchor is a place where the icon's hotspot is located. The position is defined in pixels and is relative to the the image's dimensions, so that the bottom left corner of the image is a zero-point (0,0); axes have standard orientation: the X to the right; Y to the top. If the position is empty then the bottom center of the image is set as the anchor."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['custom_pin_anchor_x']"> {{'X (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['custom_pin_anchor_y']"> {{'Y (px)' | trans}}</label>
                        </p>
                    </div>					
				</div>
				
                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Cluster Markers' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Sometimes you need to display a large number of locations or markers. Naively plotting thousands of markers on a map can quickly lead to a degraded user experience. Too many markers on the map cause both visual overload and sluggish interaction with the map. To overcome this poor performance, clustering simplifies your data visualization by consolidating data that are nearby each other on the map in an aggregate form."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['markercluster']"> {{'Group markers on zoom out' | trans}}</label>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Style' | trans}}</h3>

				<div class="uk-panel uk-panel-box uk-alert">
					<p class="uk-text-center"><i class="uk-icon uk-icon-info-circle uk-margin-small-right"></i>This section allows you to create your own uinque map type with custom styles. You can achieve sophisticated results, e.g. set individual colors for different elements (countries, roads, POI, etc.) and control their visibility.</p>
				</div>
				
				<div class="uk-panel uk-panel-box uk-alert uk-alert-warning" ng-if="widget.data['maptypeid'] != 'styled'">
					<p class="uk-text-center"><i class="uk-icon uk-icon-warning uk-margin-small-right"></i>The styles are applied only to a map type called "Styled" (see the "Map Type" option in the "Map" tab). You have another type of map selected. You should probably select the "Styled" map type if you want to use the styling.</p>
				</div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Name' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The label that will be displayed in the type controls to refer to your custom map style. If left empty, then the default value 'Styled' is used"><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
						<span><input id="wk-maptype_name" type="text" ng-model="widget.data['maptype_name']"></span>
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Styling Mode' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="There are 2 modes: 'Simple' is the default that allows you to do some basic styling. For for arbitrary styling choose 'Advanced' mode."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
						<label><input type="radio" ng-model="widget.data['styling_mode']" value="" checked> {{'Simple (default)' | trans}}</label>
						<label><input type="radio" ng-model="widget.data['styling_mode']" value="json" checked> {{'Advanced' | trans}}</label>
                    </div>
                </div>
				
				<div class="uk-form-row" ng-if="widget.data['styling_mode'] == 'json'">
					<span class="uk-form-label">{{'Custom Style (JSON)' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enter the text for you custom style. The data must be in JSON format and comply with Google Maps API rules. You may use the Google's Styled Map Wizard tool below to easily create the desired styling."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
					<div class="uk-form-controls">
						<textarea id="wk-styling_json" class="uk-width-1-1" ng-model="widget.data['styling_json']" rows="10"></textarea>
					</div>
				</div>
				
				<div class="uk-form-row uk-text-center" ng-if="widget.data['styling_mode'] == 'json'">
					<a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html" class="uk-button uk-button-primary uk-container-center" target="_blank">Launch the Styled Maps Wizard<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>
				</div>
				
                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label">{{'Invert' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Simply inverts the existing lightness. This is useful, for example, for quickly switching to a darker map with white text."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <span><input type="checkbox" ng-model="widget.data['styler_invert_lightness']"> {{'Invert lightness' | trans}}</span>
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-hue">{{'Hue' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Enter an RGB hex string that indicates the basic color. The default value is #ff0000."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-hue" class="uk-form-width-small" type="text" ng-model="widget.data['styler_hue']"> ({{'e.g. %example%' | trans: {example:'#ff0000'} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-saturation">{{'Saturation' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Saturation is a floating point value between -100 and 100 that indicates the percentage change in intensity of the basic color to apply to the element."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-saturation" class="uk-form-width-small" type="text" ng-model="widget.data['styler_saturation']"> ({{'%from% to %to%' | trans: {from:-100, to:100} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-lightness">{{'Lightness' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Lightness is a floating point value between -100 and 100 that indicates the percentage change in brightness of the element. Negative values increase darkness (where -100 specifies black) while positive values increase brightness (where +100 specifies white)."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-lightness" class="uk-form-width-small" type="text" ng-model="widget.data['styler_lightness']"> ({{'%from% to %to%' | trans: {from:-100, to:100} }})
                    </div>
                </div>

                <div class="uk-form-row" ng-if="widget.data['styling_mode'] == ''">
                    <span class="uk-form-label" for="wk-styler-gamma">{{'Gamma' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Gamma is a floating point value between 0.01 and 10.0 (where 1.0 applies no correction) indicates the amount of gamma correction to apply to the element. Gammas modify the lightness of hues in a non-linear fashion, while not impacting white or black values. Gammas are typically used to modify the contrast of multiple elements. For example, you could modify the gamma to increase or decrease the contrast between the edges and interiors of elements. Low gamma values (< 1) increase contrast, while high values (> 1) decrease contrast."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-styler-gamma" class="uk-form-width-small" type="text" ng-model="widget.data['styler_gamma']"> ({{'%from% to %to%' | trans: {from:0, to:10} }})
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Media' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Display the image inside the popup."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['media']"> {{'Show media' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Image' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Set the width and height of the image in pixels. Use 'auto' for auto size."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_width']"> {{'Width (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_height']"> {{'Height (px)' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-media-align">{{'Alignment' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Define the image alignment regarding to the content in the popup."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                                {{'Column Width' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Defines the size of the column for the image. Another width that is left will be used to display the content."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span>
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.media_align == 'left' || widget.data.media_align == 'right'">
                            <label>
                                <select class="uk-form-width-small" ng-model="widget.data['media_breakpoint']">
                                    <option value="small">{{'Phone Landscape' | trans}}</option>
                                    <option value="medium">{{'Tablet' | trans}}</option>
                                    <option value="large">{{'Desktop' | trans}}</option>
                                </select>
                                {{'Breakpoint' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Defines screen size when the columns will stack."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-media-border">{{'Border' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Defines the style of the image's border inside the Popup."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label" for="wk-media-overlay">{{'Overlay' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Define what will be displayed inside the overlay or hide the overlay."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                                {{'Animation' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The animation that will be applied to the overlay when being displayed on hover."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span>
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-thumbnail-animation">{{'Image Animation' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="The animation that will be applied to the image on hover."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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

                <h3 class="wk-form-heading">{{'Text' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Show or hide title, content and social buttons."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label" for="wk-title-size">{{'Title Size' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Define the font size of the title."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label" for="wk-text-align">{{'Alignment' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Define the text alignment."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label">{{'Display' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Display the Read More link. The link URL is added to each item in the Content Manager."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link']"> {{'Show link' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-link-style">{{'Style' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Set the style of the Read More link."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
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
                    <span class="uk-form-label" for="wk-link-text">{{'Text' | trans}}<span  data-uk-tooltip style="margin-top: 5px;" title="Define the link text."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-link-text" class="uk-form-width-medium" type="text" ng-model="widget.data['link_text']">
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'General' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Link Target' | trans}}<span data-uk-tooltip title="Enables/disables opening all links in a new window of the browser. Otherwise, they open in the same window."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link_target']"> {{'Open all links in a new window' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label" for="wk-class">{{'HTML Class' | trans}}<span data-uk-tooltip title="Adds a custom CSS class to the widget. You can specify several classes using space between them."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls">
                        <input id="wk-class" class="uk-form-width-medium" type="text" ng-model="widget.data['class']">
                    </div>
                </div>
				
				<div class="uk-form-row">
                    <span class="uk-form-label">{{'Debug output' | trans}}<span data-uk-tooltip title="If checked, then the MapEx will print some debug and status information to the browser's console window. This option should be off by default and is intended to be used for investigating problems with the widget."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span></span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['debug_output']"> {{'Print debug info to browser console' | trans}}</label>
                    </div>
                </div>

            </li>
			<li>

                <h3 class="wk-form-heading">{{'About' | trans}}</h3>

				<div class="uk-grid">
					<div class="uk-text-center uk-width-medium-1-3" id="logo-widgetkit-map-ex">
					</div>
					<div class="uk-width-medium-2-3">
						<table class="uk-table uk-table-striped">
							<tr>
								<td>
									Widget Name
								</td>
								<td id="name-widgetkit-map-ex">
									N/A
								</td>
							</tr>
							<tr>
								<td>
									Version
								</td>
								<td id="version-widgetkit-map-ex">
									N/A
								</td>
							</tr>
							<tr>
								<td>
									Build Date
								</td>
								<td id="build-widgetkit-map-ex">
									N/A
								</td>
							</tr>
							<tr>
								<td>
									Author<span data-uk-tooltip title="See the complete information about contributors and acknowledgement on the website below."><i class="uk-icon uk-icon-question-circle uk-margin-small-left" style="color:#ffb105"></i></span>
								</td>
								<td>
									<a href="https://valitov.me" target="_blank">Ramil Valitov<i class="uk-icon uk-icon-external-link uk-margin-small-left"></i></a>
								</td>
							</tr>
							<tr>
								<td>
									Website
								</td>
								<td id="website-widgetkit-map-ex">
									N/A
								</td>
							</tr>
							<tr>
								<td>
									Wiki and Manuals
								</td>
								<td id="wiki-widgetkit-map-ex">
									N/A
								</td>
							</tr>
						</table>
						<div id="update-widgetkit-map-ex" class="uk-text-center">
						</div>
					</div>
				<div>

            </li>
        </ul>

    </div>
</div>
