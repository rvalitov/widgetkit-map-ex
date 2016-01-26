![MapEx widget logo](https://raw.githubusercontent.com/wiki/rvalitov/widgetkit-map-ex/images/mapex-logo.png)

# Overview
**MapEx** is an advanced Map widget for [Yootheme Widgetkit2](https://yootheme.com/widgetkit). After installation it becomes available in the Widgets list as a "native" widget and can be used as any other widget.

## Features
### Basic Features

* **Based on Google Map** - the widget uses Google Map API to create the map.
* **Map with markers** - the widget shows a map with optional markers on it.
* **Styled map** - you can change the visual look of the map.
* **Backward compatibility** - all other behavior, styling and features of the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) are preserved.

### Unique Features
The new features that the MapEx has and that are not available in the original Map widget:
 
* **Responsive behavior** - the map will automatically adjust (pan & zoom) if the user changes the window size or orientation (on mobile devices). [The problem's description](https://yootheme.com/component/answers/question/52808).
* **Custom pin images** - you can set a custom icon for all pin markers or set a unique image for each marker ([Read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Customizing-marker's-pin-image)).
* **Center map** - you can put an arbitrary center of the map. This feature is not available in the original Map widget - problem exists since 2013, e.g. [post#1](https://yootheme.com/component/answers/question/75957), [post#2](https://yootheme.com/component/answers/question/52808).
* **Correct visualization inside the [UIKit modal dialog](http://getuikit.com/docs/modal.html)** - there's a problem with the original [Map widget](http://yootheme.com/demo/widgetkit/joomla/index.php/home/map) when the widget is not rendered correctly when displayed inside the modal dialog: the map tiles are not rendered completely or partly, so the widget is not visible properly. This MapEx widget fixes this issue.
* **Tooltips for all options** - it's much easier to use the widget, because tooltips are available for all settings.
* **More map types (Roadmap, Satellite, Hybrid, Terrain, Styled)** - the widget supports all standard map types that are available at Google Maps, [read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Map-types).
* **Advanced map controls** - you can customize visibility of all map controls and tweak their look, [read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Map-controls).
* **Sophisticated styling options** - you can create your own custom map with unique appearance using various styling options and Wizard, [read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Map-styling).
* **Update notifications** - you will be notified if new versions of the widget are available.
* **Access to the original [Google Map object](https://developers.google.com/maps/documentation/javascript/reference#Map)** - Javascript object that is used in creation of the map. So, the user may change and/or modify the object as he needs from any web page using Javascript which provides vast opportunities for Javascript programmers to customize the map using native [Google Map API](https://developers.google.com/maps/documentation/javascript/tutorial), [read more](https://github.com/rvalitov/widgetkit-map-ex/wiki/Working-with-Google-Map-object-(for-Javascript-programmers)).

# Supported platforms
* The latest code is based on Widgetkit 2.5.3, however it should work with any Widgetkit 2.4.x and later.
* Joomla 3.4.x or later required

Widget should work with Wordpress 4.4.x (according to feedback from users; I can't test it personally, because I don't have the required subscription).

# How to install?
The installation procedure is described [here](https://github.com/rvalitov/widgetkit-map-ex/wiki/How-to-install).

# The manual
Some issues about using the widget are available in the [Wiki area](https://github.com/rvalitov/widgetkit-map-ex/wiki).

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
