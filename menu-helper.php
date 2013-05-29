<?php 
/**
 * @package MenuHelper
 */
/*
* Plugin Name: Menu Helper
* Description: Users can use short-codes to retrieve different sub-menus. In Settings -> Menu Helper Info are described the options provided by the plugin.
* Author: Simona Ilie
* Author URI: http://profiles.wordpress.org/simonailie/
* Version: 1.1
* Requires at least: 3.5
* License: GPLv2 or later
*/


/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}


// Check WordPress Version. Needs to be at least 3.5
global $wp_version;
if ( $wp_version < '3.5' ) {
    echo 'Sorry, to run this plugin you need at least WordPress version 3.5. For customizations you can contact the creator of this plugin;).';
	exit;
}
/*********************************************************/
/************** CONSTANTS DEFINITIONS ********************/
/*********************************************************/
if( !defined('MENU_HELPER_PLUGIN_URL') )			define( 'MENU_HELPER_PLUGIN_URL', trailingslashit(plugin_dir_url( __FILE__ )) );
if( !defined('MENU_HELPER_PLUGIN_PATH') )			define( 'MENU_HELPER_PLUGIN_PATH', trailingslashit(dirname(__FILE__)) );

// define the main shortcode
add_shortcode( 'menu_helper', 'menu_helper_main_func' );


// set the option in menu
if( !function_exists('menu_helper_options_submenu') ) :
function menu_helper_options_submenu()
{
	// add sub option
	add_submenu_page('options-general.php', 'Menu Helper Info', 'Menu Helper Info', 'manage_options', 'menu-helper-info', 'menu_helper_info_display');
}	
endif;
add_action('admin_menu', 'menu_helper_options_submenu');

// description page for WP admin
if( !function_exists('menu_helper_info_display') ) :
function menu_helper_info_display()
{
	include_once(MENU_HELPER_PLUGIN_PATH . 'includes/description.php');
}
endif;

/*******************************************************/
/*************** THE SHORTCODE MAGIC *******************/
/*******************************************************/
if(!function_exists('menu_helper_func') ) :
function menu_helper_func( $atts ) {
	extract( shortcode_atts( array(
		'submenu_slug' 				=> '',			/* 	1 */
		'parent_id' 				=> null,		/*  2 */
		'menu_id' 					=> '',			/*  3 */
		'include_parent' 			=> false,		/*  4 */
		'container_tag' 			=> 'ul',		/*  5 */
		'container_class' 			=> '',			/*  6 */
		'item_tag' 					=> 'li',		/*  7 */
		'item_class' 				=> '',			/*  8 */
		'first_item_class' 			=> '',			/*  9 */
		'strict_first_item_class' 	=> '',			/* 10 */
		'last_item_class' 			=> '',			/* 11 */
		'submenu_depth' 			=> 1,			/* 12 */
		'theme_location'			=> 'primary'	/* 13 */
	), $atts ) );

	$helper_data = array(
		'submenu_slug'				=> $submenu_slug,
		'parent_id'					=> $parent_id,
		'menu_id'					=> $menu_id,
		'include_parent'			=> $include_parent,
		'container_tag'				=> $container_tag,
		'container_class'			=> $container_class,
		'item_tag'					=> $item_tag,
		'first_item_class'			=> $first_item_class,
		'strict_first_item_class'	=> $strict_first_item_class,
		'last_item_class'			=> $last_item_class,
		'submenu_depth'				=> $submenu_depth,
		'theme_location'			=> $theme_location		
	);
	$submenu = menu_helper_generate_submenu($helper_data);
	return $submenu;
}

add_shortcode( 'menu-helper', 'menu_helper_func' );
endif;

/*******************************************************/
/* THE METHOD THAT PROCESSES THE OPTIONS AND RETURNS ***/
/* THE REQUESTED INFORMATION ***************************/ 
/*******************************************************/
global $mh_depth;
if( !function_exists( 'menu_helper_generate_submenu' ) ) :
function menu_helper_generate_submenu( $options = array() )
{		
	global $mh_depth;
	$final_menu			= '';
	$parent_id			= 0;
	$is_echo			= ( isset( $options['echo'] ) ) ? $options['echo'] : true;	
	$depth				= 1;
	
	/* step 1. decide what menu to use and the parent_id*/
	// case 1. best case scenario, the user defined a menu_id
	if( isset( $options['menu_id']) && !empty( $options['menu_id'] ) )
		$final_menu 	= $options['menu_id'];
	// case 2. the user did not define a menu_id, but defined a theme location
	else if( isset($options['theme_location']) && !empty( $options['theme_location']) )
		$final_menu	 	= menu_helper_get_menu_by_theme_location( $options['theme_location'] );
	
	// case 3. the user did not define a menu_id and the theme_location is undefined. Now we need to get the menu of the parent_id
	$parent_id			= ( isset( $options['parent_id'] ) && !is_null( $options['parent_id'] ) ) ? $options['parent_id'] : get_the_ID();
	
	if( !$final_menu )
		$final_menu		= menu_helper_get_menu_by_parent_id( $parent_id );
	/* step 1. FINISHED */
	
	if( isset( $options['submenu_depth'] ) && !is_null( $options['submenu_depth'] ) && !empty( $options['submenu_depth'] ) && is_numeric( $options['submenu_depth'] ) )
		$depth 			= $options['submenu_depth'];

	
	// start menu processing
	if( $final_menu )
	{
		$menu_items		= wp_get_nav_menu_items( $final_menu );	
		$menu_parent_id	= ($parent_id != 0) ? menu_helper_get_menu_equivalent_id( $parent_id ) : $parent_id;
		if( $depth == -1 )
			$depth =  menu_helper_set_max_depth_for_menu( $menu_items, $menu_parent_id );
		
		$mh_depth		= $depth;
		$menu_data		= menu_helper_create_menu_hierarchy( $menu_items, $menu_parent_id, $depth );
		
		// if there are returned any submenu info,
		// start processing it according to request
		if( $menu_data )
		{
			if( !$is_echo )
				return $menu_data;
			else {
				echo menu_helper_display_submenu( $menu_data, $menu_parent_id, $options );
			}
		}		
	}
}
endif;

if( !function_exists( 'menu_helper_get_menu_by_theme_location' ) ) :
function menu_helper_get_menu_by_theme_location( $theme_location = null)
{
	if( is_null( $theme_location ) || empty( $theme_location ) )
		return;
		
	$menu_locations = get_nav_menu_locations();
	
	if( !isset( $menu_locations[$theme_location] ) ) return;
	
	$menu = get_term( $menu_locations[$theme_location], 'nav_menu' );
	
	return ( !is_null( $menu) && isset( $menu->slug ) ) ? $menu->slug : null;
}
endif;

// check if the parent id is used in any menu and if so get that menu slug
if( !function_exists( 'menu_helper_get_menu_by_parent_id' ) ) :
function menu_helper_get_menu_by_parent_id( $id )
{
	global $wpdb;
	// get all posts of type nav_menu_item
	$menu_items = get_posts( array( 'post_type' => 'nav_menu_item', 'posts_per_page' => -1 ) );
	
	$menu_slug	= null;
	
	$menu_items_ids = array();
	
	if($menu_items)
	{
		foreach( $menu_items as $mi )		
			$menu_items_ids[] = $mi->ID;
		
		$sql 	= $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '%s' AND POST_ID IN ( " . implode( ',', $menu_items_ids ) . " )", '_menu_item_object_id' );
		$pages_ids = $wpdb->get_col( $sql );
		if( in_array( $id, ( array_unique( $pages_ids) ) ) )
			$menu_slug = menu_helper_get_menu_slug_for_post( $id );
	}
	
	return $menu_slug;
}
endif;

if( !function_exists( 'menu_helper_get_menu_slug_for_post' ) ) :
function menu_helper_get_menu_slug_for_post( $id )
{
	global $wpdb;
	$menu_slug	= null;
	// get the menu_nav_item associated
	$sql 		= $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '%s' AND meta_value = %d", '_menu_item_object_id', $id );
	$post_id	= $wpdb->get_var( $sql );
	
	// get relationship with terms
	if( $post_id )
	{
		$sql 		= $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->prefix}term_relationships WHERE object_id = %d", $post_id );
		$term_id	= $wpdb->get_var( $sql );
		
		// get the menu slug if any
		if( $term_id )
		{
			$menu_term = get_term( $term_id, 'nav_menu' );
			$menu_slug = ( !is_null( $menu_term ) &&  isset( $menu_term->slug ) ) ? $menu_term->slug : null;
		}
	}
	
	return $menu_slug;
}
endif;

// the recursive function that generates the menu
if( !function_exists( 'menu_helper_create_menu_hierarchy' ) ) :
function menu_helper_create_menu_hierarchy($menu, $parent_id = 0, $depth = 1)
{
	$final_menu = array();
	
	if( !is_null( $menu ) && !empty( $menu ) && is_array( $menu ) ) {
		foreach( $menu as $m )
		{ 
			if($m->menu_item_parent == $parent_id)
			{	$new_depth = $depth;			
				if( $new_depth > 1 ) {
					--$new_depth;
					$children = menu_helper_create_menu_hierarchy($menu, $m->ID, $new_depth, $only_ids);				
					$m->children = $children;	
						
					$final_menu[] = array(
						'post_id' 			=> $m->object_id,						
						'title'				=> apply_filters( 'the_title', $m->title ),
						'url'				=> $m->url, 
						'menu_nav_id'		=> $m->ID,
						'parent_id'			=> $m->menu_item_parent,
						'children'			=> $children						
					);
				} else
					$final_menu[] = array(
						'post_id'			=> $m->object_id,
						'title'				=> apply_filters( 'the_title', $m->title ),
						'url'				=> $m->url,
						'menu_nav_id'		=> $m->ID,
						'parent_id'			=> $m->menu_item_parent
					);
			}
		}
	}
	return $final_menu;
}
endif;

// for a post id get the menu_nav_item equivalent
if( !function_exists( 'menu_helper_get_menu_equivalent_id' ) ):
function menu_helper_get_menu_equivalent_id( $id )
{
	global $wpdb;
	$query 	= $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '%s' AND meta_value = %d", '_menu_item_object_id', $id);
	$data 	= $wpdb->get_var($query);
	return $data;
}
endif;


// get the max depth that a post with menu equivalent $id can get into the menu structure
if( !function_exists( 'menu_helper_set_max_depth_for_menu' ) ) :
function menu_helper_set_max_depth_for_menu( $data, $id )
{
	$depth 	= 0;
	$temp	= array();
	// rearrange the menu items
	foreach( $data as $d )
	{
		if( $d->menu_item_parent == 0)		
			$temp[$d->ID] = 1;
		else {		
			if( !isset( $temp[$d->ID] ) && in_array( $d->menu_item_parent, array_keys( $temp ) ) )
				$temp[$d->ID] = $temp[$d->menu_item_parent] + 1;
		}			
	}	
	
	$depth = isset( $temp[$id] ) ? ( max($temp) - $temp[$id] ) : 0;
	return $depth;
}
endif;

if( !function_exists( 'menu_helper_process_item_info' ) ) :
function menu_helper_process_item_info( $menu )
{	
	global $menu_helper_data;
	if( is_array( $menu ) ) { 
		foreach( $menu as $k => $item )
		{
			$post_id 				= $item['post_id'];
			$menu[$k]['title'] 		= apply_filters( 'the_title', get_the_title( $post_id ) );
			$menu[$k]['url']		= get_permalink( $post_id );
			
			if( isset( $item['children'] ) && !empty( $item['children'] ) && is_array( $item['children'] ) )
				$menu[$k]['children'] = menu_helper_process_item_info( $item['children'] );
		}		
	}
	$menu_helper_data = $menu;
	return $menu;
}
endif;

if( !function_exists( 'menu_helper_display_submenu' ) ) :
function menu_helper_display_submenu( $menu, $parent_id, $options = array(), $depth = null )
{
	global $mh_depth;	
	if( is_null( $depth ) )
		$depth = $mh_depth;
	
	$content 			= '';
	$current_id			= get_the_ID();
	if($menu)
	{
		
		$submenu_slug 				= ( isset( $options['submenu_slug'] ) && !empty( $options['submenu_slug'] ) ) ?  " id='" . $options['submenu_slug'] . "' " : '';
		$include_parent				= ( isset( $options['include_parent'] ) && !empty( $options['include_parent'] ) ) ? $options['include_parent'] : false;
		$container_tag				= ( isset( $options['container_tag'] ) && !empty( $options['container_tag'] ) ) ? $options['container_tag'] : 'ul';
		$container_class			= ( isset( $options['container_class'] ) && !empty( $options['container_class'] ) ) ? " class='" . $options['container_class'] . "' " : '';
		$item_tag					= ( isset( $options['item_tag'] ) && !empty( $options['item_tag'] ) ) ? $options['item_tag'] : 'li';
		$item_class					= ( isset( $options['item_class'] ) && !empty( $options['item_class'] ) ) ? $options['item_class'] : '';
		$first_item_class			= ( isset( $options['first_item_class'] ) && !empty( $options['first_item_class'] ) ) ? $options['first_item_class'] : '';
		$strict_first_item_class	= ( isset( $options['strict_first_item_class'] ) && !empty( $options['strict_first_item_class'] ) ) ? $options['strict_first_item_class'] : '';
		$last_item_class 			= ( isset( $options['last_item_class'] ) && !empty( $options['last_item_class'] ) ) ? $options['last_item_class'] : '';
		
		$content .= '<' . $container_tag . $submenu_slug . $container_class . '>';
		if( $include_parent )
		{
			$post_parent_id	= get_post_meta($parent_id, '_menu_item_object_id', true);
			$current_class 	= ($current_id == $post_parent_id) ? " current-item" : '';
			$content 		.= '<' . $item_tag . " class='" . $item_class . $first_item_class . $strict_first_item_class . $current_class . "'>";
			$content 		.= "<a href='" . get_permalink( $post_parent_id ) . "'>" . apply_filters( 'the_title', get_the_title( $post_parent_id ) ) . '</a>';			
			$content		.= '<' . $container_tag . '>';
		}
				
		foreach( $menu as $k => $item)
		{						
			$current_class 	= ($current_id == $item['post_id']) ? " current-item " : '';
			$content .= '<' . $item_tag . " class='" . $item_class . $current_class . ( ( $k == 0 ? $first_item_class : (
				( $k == ( count( $menu ) - 1) ? $last_item_class : '')
			) ) ) . ( ( $k == 0 && $depth == $mh_depth) ? ' ' . $strict_first_item_class : '' )  . "'>";
			$content .= "<a href='" . $item['url'] . "'>" . $item['title'] . '</a>';
			if( isset( $item['children'] ) && !empty( $item['children'] ) && is_array( $item['children'] ) ) {
				if( isset($options['include_parent'] ) ) $options['include_parent'] = false;			
				$depth = $depth - 1;
				$content 		.= menu_helper_display_submenu( $item['children'], $item['parent_id'], $options, $depth);
			}
				
			$content .= '</' . $item_tag . '>';
		}
		
		if( $include_parent )
		{
			$content 		.= '</' . $container_tag . '>';
			$content 		.= '</' . $item_tag . '>';			
		}
		
		$content .= '</' . $container_tag . '>';
	}
	return $content;
}
endif;

if( !function_exists('_pre') ) :
function _pre($data, $clr = 'red', $ips = array() )
{
	echo "<pre style='color:{$clr};'>";
	print_r($data);
	echo "</pre>";
}
endif;
