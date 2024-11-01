<?php
/*
Plugin Name: WPEC Product Search History
Version: 1.0.1
Plugin URI: http://anthonycole.me/plugins/
Description: WP-Ecommerce History is a plugin for the popular WP-Ecommercrce plugin that allows users to view the products they have looked at in a sidebar widget.
Author: Anthony Cole
Author URI: http://anthonycole.me/

Copyright 2011  (email: anthony@radiopicture.com.au )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* 
 * @function wpecsh_register_pt() 
 * @description Registers a Post Type for WordPress Search History
 * @package wpecsh
 * @since 1.0
*/
function wpecsh_register_pt() {
	$args = array(
		'public' => false,
	    'publicly_queryable' => true,
	    'show_in_menu' => false, 
	    'query_var' => false,
	    'rewrite' => false,
	    'capability_type' => 'post',
	    'has_archive' => false, 
	    'hierarchical' => false,
	    'supports' => array('author'),
	);
	register_post_type( 'wpecsh_log', $args );
}
add_action( 'init', 'wpecsh_register_pt' );

/* 
 * @function wpecsh_search_log
 * @description logs a users' search history
 * @package wpecsh
 * @since 1.0
*/
function wpecsh_search_log() {	
	global $wp_query, $wpdb;
	
	if( !function_exists( 'wpsc_is_product' ) )
		return true;
	
	if( is_user_logged_in() && wpsc_is_product() ) {
		
		$user = wp_get_current_user();
	

		$parent_id = $wpdb->get_var( $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_name = %s', get_query_var( 'wpsc-product' )) );

		$args = array( 
			'post_author'      => $user->id, 
			'post_title'       => $user->id,
			'post_content'     => '',
			'post_parent'	   => $parent_id,
			'post_type'        => 'wpecsh_log',
			'post_status'      => 'publish'
		);
		
		if( function_exists( 'bp_activity_add' ) ) {
			
			$post = get_post( $parent_id );
			
			$args = array(
				'action' => $user->user_nicename . ' has viewed <a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>' ,
				'content' => $my_content,
				'primary_link' => get_permalink( $product->post_content ),
				'component' => 'wp-ecommerce',
				'type' => 'new_product_view',
				'user_id' => $product->post_author,
				'item_id' => $parent_id,
			);
				
			bp_activity_add( $args );
		}
		
		$post = wp_insert_post( $args );
	}
}
add_action( 'template_redirect', 'wpecsh_search_log' );

/* 
 * @WPECsh_Widget extends WP_Widget 
 *
*/
class WPECsh_Widget extends WP_Widget {

	/* 
	 * @method wpecsh_Widget()
	 * init our widget and put all the settings on
	*/
	function wpecsh_Widget() {
		$widget_ops = array( 'classname' => 'wpecsh_widget', 'description' => 'Displays a users\'s product search history' );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'wpecsh_widget' );
		$this->WP_Widget( 'wpecsh_widget', __('Product Search History'), $widget_ops, $control_ops );
	}
	
	/* 
	 * @method widget()
	 * show the widget!
	*/

	function widget( $args, $instance ) {
			global $wpdb;
			
			$user = wp_get_current_user();
			
			$recent = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT post_parent from $wpdb->posts WHERE post_type = 'wpecsh_log' AND post_author = %d order by post_date DESC LIMIT %d", $user->ID, $instance['results_show'] ) );
			
			extract( $args );
			
			$title = apply_filters('widget_title', $instance['title'] );
			if ( $title && count( $recent ) > 0 ) 
				echo $before_title . $title . $after_title;
			
			echo '<ul class=\'wpec-history\'>';
			foreach( $recent as $product ) {
			?>
			<li><a href="<?php echo get_permalink( $product->post_parent ); ?>"><?php echo get_the_title( $product->post_parent ); ?></a></li>
			<?php
			}
			echo "</ul>";
	}
	
	/* 
	* @method update()
	* update our instance
	*/

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		foreach ( array('title', 'results_show') as $val ) {
			$instance[$val] = strip_tags( $new_instance[$val] );
		}
		return $instance;
	}
	
	/* 
	 * @method form
	 * form for widget options!
	*/
	
	function form( $instance ) {
		$defaults = array( 
			'title' 		=> 'Product Search History', 
			'results_show'  => 5,
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title"); ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'results_show' ); ?>"><?php _e("Number of results to show"); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'results_show' ); ?>" name="<?php echo $this->get_field_name( 'results_show' ); ?>" value="<?php echo $instance['results_show']; ?>" size="5" />
		</p>
		<p><strong>Note:</strong> This widget will not display if there are no search history results for the user.</p>
	<?php 
	}
}

/* 
 * our widget registration script
*/

function wpesh_widget_func() {
	register_widget( 'wpecsh_Widget' );
}
add_action( 'widgets_init', 'wpesh_widget_func' );

?>
