<link rel="stylesheet" type="text/css" href="<?php echo MENU_HELPER_PLUGIN_URL;?>css/desc.css" />
<div class="menu_helper_wrapper">
	<h1><?php _e('Menu Helper', 'menu-helper');?></h1>
	<h2><?php _e('Description:', 'menu-helper');?></h2>
<p><?php _e('Menu Helper can be used in 2 forms: as short code ( as described in section A) or, for more advanced programmatic use, as function which returns an array (as described in section B). For both usage styles the options are the same.', 'menu-helper');?></p>

	<h2><?php _e('Options:', 'menu-helper');?></h2>
	<ol>
		<li><span class="code">submenu_slug</span> (<?php _e('default', 'menu-helper');?> <em>empty string</em>. <?php _e('Allows to define a custom id for the submenu element', 'menu-helper');?>);</li>
		<li><span class="code">parent_id</span> (<?php _e('default', 'menu-helper');?> <em>null</em>. <?php _e('If not null, will provide the children of the parent_id option. If set to 0 will get level 1 items (and their children, if submenu_depth set to a number greater than 1) ', 'menu-helper');?>);</li>
		<li><span class="code">menu_id</span> (<?php _e('default', 'menu-helper');?> <em>empty space</em>. <?php _e('If integer, will be used the menu ID, otherwise will be the menu\'s slug/name. If not defined the plugin will check if the parent_id is used in any menu definition and pick the first it finds.', 'menu-helper');?>);</li>
		<li><span class="code">include_parent</span> (<?php _e('default', 'menu-helper');?> <em>false</em>. <?php _e('If set to true will include the parent element', 'menu-helper');?>);</li>
		<li><span class="code">container_tag</span> (<?php _e('default', 'menu-helper');?> <em>ul</em>. <?php _e('Allows to set the tag element of the containers', 'menu-helper');?>);</li>
		<li><span class="code">container_class</span> (<?php _e('default', 'menu-helper');?> <em>empty space</em>. <?php _e('Allows to define a custom CSS class for container element. Can contain more class names separated by space', 'menu-helper');?>);</li>
		<li><span class="code">item_tag</span> (<?php _e('default', 'menu-helper');?> <em>li</em>. <?php _e('Allows to set the tag element for menu items', 'menu-helper');?>);</li>
		<li><span class="code">item_class</span> (<?php _e('default', 'menu-helper');?> <em>empty string</em>. <?php _e('Allows to define a custom CSS class for each submenu item', 'menu-helper');?>);</li>
		<li><span class="code">first_item_class</span> (<?php _e('default', 'menu-helper');?> <em>empty string</em>. <?php _e('Allows to define a custom CSS class for first item in submenu. If submenu_depth is set to greater than 1 the class will repeat on each level', 'menu-helper');?>);
		<li><span class="code">strict_first_item_class</span> (<?php _e('default', 'menu-helper');?> <em>empty string</em>. <?php _e('Allows to define a custom CSS class only for the very first item in submenu', 'menu-helper');?>);</li>
		<li><span class="code">last_item_class</span> (<?php _e('default', 'menu-helper');?> <em>empty string</em>. <?php _e('Allows to define a custom CSS class for last item in submenu. If submenu_depth is set to greater than 1 the class will repeat on each level', 'menu-helper');?>);</li>
		<li><span class="code">submenu_depth</span> (<?php _e('default', 'menu-helper');?> <em>1</em>. <?php _e('If set to a number greater than 1 the call will show in depth children. To get full submenu depth set this parameter to -1', 'menu-helper');?>).</li>
		<li><span class="code">theme_location</span> (<?php _e('default', 'menu-helper');?> <em>primary</em>. <?php _e('Will use the menu associated to the value of the theme_location', 'menu-helper');?>).</li>
	</ol>
	
	<h2><?php _e('Section A - short code use:', 'menu-helper');?></h2>
	<b><?php _e('Examples', 'menu-helper');?></b>
	<pre>[menu-helper /]</pre> - if no menu_id provided the plugin will check the theme_location. If it finds a theme location in options will use the menu associated to that theme location. Otherwise the plugin will check if the parent id - which in this case will be the current page - is used in any menu and will consider the first one it finds;
	<pre>[menu-helper parent_id=8 /]</pre> - Checkes for the menu associated with theme_location = 'primary' and from that menu take the first level children from the option with ID=0;
	<pre>[menu-helper menu_id="main_menu" /]</pre> - main_menu is the name of a menu defined in Appearance -> Menu. The plugin does not require to associate the menu with a registered theme location.
	<pre>[menu-helper submenu_depth=-1 container_tag="div" item_tag="div"]</pre> - will display all options in a divs DOM structure.
	
	<h2><?php _e('Section B - function use:', 'menu-helper');?></h2>
	<b><?php _e('How To:', 'menu-helper');?></b>
	<pre>$args = array(
	'echo'					=> false				/* this is used only for function's direct call */
	'submenu_slug' 				=> '',					/*  1 */
	'parent_id' 				=> null,				/*  2 */
	'menu_id' 				=> '',					/*  3 */
	'include_parent' 			=> false,				/*  4 */
	'container_tag' 			=> 'ul',				/* 5  */
	'container_class' 			=> '',					/*  6 */
	'item_tag' 				=> 'li',				/*  7 */
	'item_class' 				=> '',					/*  8 */
	'first_item_class' 			=> '',					/*  9 */
	'strict_first_item_class' 		=> '',					/* 10 */
	'last_item_class' 			=> '',					/* 11 */
	'submenu_depth' 			=> 1,					/* 12 */
	'theme_location'			=> 'primary'				/* 13 */

);

if( function_exists( 'menu_helper_generate_submenu' ) ) :
	$my_menu_data = menu_helper_generate_submenu( $args );
endif;
</pre>
		<b><em>$my_menu_data</em> will containa structure of arrays of arrays with information about each sub-menu item:
		<ul>
			<li><span class="code">post_id</span> 	- the ID of the displayed post</li>
			<li><span class="code">title</span> 	- will be displayed as submenu option</li>
			<li><span class="code">url</span> 		- link for subemnu option</li>
			<li><span class="code">menu_nav_id</span> - the ID of the menu_nav_item element</li>
			<li><span class="code">parent_id</span> - the ID of the parent post</li>
			<li><span class="code">children</span> - if the post has "children" they will be arrays with similar structure</li>
			
		</ul></b>
</div>