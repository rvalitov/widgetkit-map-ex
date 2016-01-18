![MapEx widget logo](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/mapex-logo.png)
# Overview
**MapEx** is an advanced Map widget for [Yootheme Widgetkit2](https://yootheme.com/widgetkit). After installation it becomes available in the Widgets list as a "native" widget and can be used with any [Warp 7 theme](https://yootheme.com/themes).

## Features
Or why it's better than original Map provided by Yootheme?
* **Responsive behavior** - the map will automatically adjust (pan & zoom) if the user changes the window size or orientation (on mobile devices). Responsive behavior is not available in the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map), e.g. [post in Yootheme forum](https://yootheme.com/component/answers/question/52808).
* **Custom pin images** - you can set a custom icon for all pin markers or set a unique image for each marker ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Center map** - you can put an arbitrary center of the map. This feature is not available in the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map), problem exists since 2013, e.g. posts at Yootheme forum: [post#1](https://yootheme.com/component/answers/question/75957), [post#2](https://yootheme.com/component/answers/question/52808)
* **Correct visualization** of the widget if it's displayed inside the [UIKit modal dialog](http://getuikit.com/docs/modal.html). There's a problem with the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) when the widget is not rendered correctly when displayed inside the modal dialog: the map tiles are not rendered completely or partly, so the widget is not visible properly. This MapEx widget fixes this issue.
* **Access to the original [Google Map object](https://developers.google.com/maps/documentation/javascript/reference#Map)** - Javascript object that is used in creation of the map. So, the user may change and/or modify the object as he needs from any web page using Javascript which provides vast opportunities for Javascript programmers to customize the map using native [Google Map API](https://developers.google.com/maps/documentation/javascript/tutorial).
* **Backward compatibility** - all other behavior, styling and features of the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) are preserved.

# Supported platforms
* The code is based on Widgetkit 2.5.0, however it should work with any Widgetkit 2.4.x and later.
* Joomla 3.4.x or later required
I didn't test it with Wordpress.

# Installation
## Installation Overview
We use "clean" and "neat" approach according to the official Yootheme's manual:
* [Custom widgetkit plugin](http://yootheme.com/widgetkit/documentation/customizing/custom-widget-plugin)
* [Where to store your customizations](https://yootheme.com/widgetkit/documentation/customizing/where-to-store-your-customizations)

Such approach allows you to:
* keep original Yootheme's Widgetkit source files
* preserve original Yootheme's functionality
* safely do updates to Yootheme's files (download new versions of Widgetkit 2), keeping our new widget (modifications won't be overwritten during update process)

## Installation Process
The installation is very simple. You just need to copy the folder _map_ex_ to _/templates/THEME-NAME/widgetkit/widgets/_, so that you will have a folder _/templates/THEME-NAME/widgetkit/widgets/map_ex_. The _THEME-NAME_ is a folder of your [Warp 7 theme](https://yootheme.com/themes), e.g. it can be _yoo_vida_, _yoo_finch_, etc. 

# Setup and usage
## Configure the widget
After successful installation you should see the MapEx widget in the widgetkit control panel page, so that you can select it from the list:
![MapEx widget](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/widgets-list.jpeg)

You should configure the widget as usual, e.g. the [Yootheme documentation](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) can be useful.

### The "Settings" interface
The screenshot of the widget's *"Settings"* interface with options marked that are the advanced features of the MapEx:
![MapEx widget settings screen](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/mapex-settings-screen.jpeg)
If you don't activate and use the advanced features of the MapEx, then it behaves exactly as the original Map widget from the Widgetkit bundle.

Options and description:
* **Marker Pin Icon** - defines a type of pin that will be used in map markers ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Image** - defines the image used as a custom marker's pin ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Anchor** - defines the anchor - a place where the icon's hotspot is located. The position is defined in pixels and is relative to the the image's dimensions, so that the bottom left corner of the image is a zero-point (0,0); axes have standard orientation: the X to the right; Y to the top. If the position is empty then the bottom center of the image is set as the anchor ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Responsive** - if checked, then the map will automatically pan and zoom to the _Map Center Point_ (see below) every time the window is resized or when orientation of the device changes (e.g. for smartphones or tablets). Note, that panning and zooming is done to the initial position of the map, i.e. if the user was interacting with the map and changed its position or zooming level, then this changes will be lost.
* **Modal dialog fix** - if checked, then the widget will automatically try to find the [UIKit modal dialog](http://getuikit.com/docs/modal.html) inside of which the MapEx is located. Then it adds some code to the webpage that displays the map correctly and fixes a corresponding problem of the original Map widget. Besides, the MapEx always resets the map to its default position and zoom level every time the modal is displayed, e.g. if the user opens a modal, changes the map position or zoom level, closes the modal and then opens the modal again, the map will be reset to its initial configuration (the changes that user made are not saved). It's a useful feature, because the widget will always show the intended part of a map. If the check box is set and the MapEx is not inside a modal, then it doesn't affect the visualization.
* **Map Center Point** - if set (not blank), then the map will be displayed in a way that this point will be located right in the center of the map. The center point is defined by its coordinates, e.g. _-34.23456, 12.15672_.
* **Debug output** - if checked, then the MapEx will print some debug and status information to the browser's console window. This option should be off by default and is intended to be used for investigating problems with the widget.

### The "Content" interface
The screenshot of the widget's *"Content"* interface with options marked that are the advanced features of the MapEx:
![MapEx widget content screen](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/mapex-content-screen.jpeg)
If you don't activate and use the advanced features of the MapEx, then it behaves exactly as the original Map widget from the Widgetkit bundle.

Options and description:
* **Custom Pin Image** - defines an image used as a custom marker's pin icon for the item ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Custom Pin Anchor X** and **Custom Pin Anchor Y** - define the icon's hotspot ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).

## Screenshots
Examples of using a custom marker:
![Custom map widget markers](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/mapex-global-custom-marker.jpg)

Examples of using a unique custom marker for every map's item:
![Custom map widget unique item markers](https://raw.githubusercontent.com/rvalitov/widgetkit-map-ex/master/images/mapex-unique-custom-marker.jpg)

## Advanced usage (Javascript)
Information about accessing the map object with Javascript is available in the [Wiki area](https://github.com/rvalitov/widgetkit-map-ex/wiki).

# Authors, Contributors and Acknowledgment
* This widget is created by [Ramil Valitov](http://www.valitov.me).
* The code is based on the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) by [Yootheme](http://yootheme.com/).
* Logo designed by [Freepik](http://www.freepik.com/)
* Special thanks to [Florian](https://yootheme.com/support/profile/florian), member of the Yootheme support team, for his [post](https://yootheme.com/support/question/80769) about the problem with the map widget displayed in a lightbox and the approach how to solve it.

# Feedback
Your feedback is very appreciated. If you want to see new features in this module, please, post your ideas and feature requests in the [issue tracker](https://github.com/rvalitov/widgetkit-map-ex/issues).

# Support or Contact
Having trouble with MapEx Widget? May be something has already been described in the [Wiki area](https://github.com/rvalitov/widgetkit-map-ex/wiki) or reported in the [issue tracker](https://github.com/rvalitov/widgetkit-map-ex/issues). If you don't find your problem there, then, please, add your issue there. 

Being a free project which I do in my spare time, I hope you understand that I can't offer 24/7 support:) You may contact me via e-mail ramilvalitov@gmail.com, I will try to answer to all of them (if such messages imply an answer), however, not immediately, it may take a few days or a week... so, just be patient. 

Note, that I can answer only to questions and problems directly related to MapEx widget. Answers to basic questions about the widgetkit nature and simple help about how to use widgets in general or how to use Joomla you can find in appropriate forums:
* [Joomla](http://forum.joomla.org/)
* [Widgetkits](https://yootheme.com/support)
