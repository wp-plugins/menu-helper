=== Menu Helper ===
Author: SimonaIlie
Tags: menus manipulation, submenus, menu, navigation
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Menu Helper can be used in 2 forms: as short code or, for more advanced programmatic use, as function which returns an  multi-dimensional array.

== Description ==

Menu Helper can be used in 2 forms: as short code ( as described in section A) or, for more advanced programmatic use, as function which returns an array (as described in section B). For both usage styles the options are the same. You will find a detailed description with examples in Settings -> Menu Helper Info after the plugin's activation.

1. **submenu_slug** - Allows to define a custom id for the submenu element
1. **parent_id** - If not null, will provide the children of the parent_id option. If set to 0 will get level 1 items (and their children, if submenu_depth set to a number greater than 1)
1. **menu_id** - If integer, will be used the menu ID, otherwise will be the menu's slug/name. If not defined the plugin will check if the parent_id is used in any menu definition and pick the first it finds
1. **include_parent** - If set to true will include the parent element
1. **container_tag** - Allows to set the tag element of the containers
1. **container_class** - Allows to define a custom CSS class for container element. Can contain more class names separated by space
1. **item_tag** - Allows to set the tag element for menu items
1. **item_class** - Allows to define a custom CSS class for each submenu item
1. **first_item_class** - Allows to define a custom CSS class for first item in submenu. If submenu_depth is set to greater than 1 the class will repeat on each level
1. **strict_first_item_class** - Allows to define a custom CSS class only for the very first item in submenu
1. **last_item_class** - Allows to define a custom CSS class for last item in submenu. If submenu_depth is set to greater than 1 the class will repeat on each level
1. **submenu_depth** - If set to a number greater than 1 the call will show in depth children. To get full submenu depth set this parameter to -1
1. **theme_location** - The plugin will use the menu associated to the value of the theme_location

For older WordPress versions or any issue you can email me at simonailie@gmail.com

== Installation ==

1. Upload `menu-helper.zip` content to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress