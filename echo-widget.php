<?php
/*
Plugin Name: Echo Widget
Plugin URI: http://wordpress.org/extend/plugins/#
Description: Add widget to create sidebar menu for Echo KB
Author: Chris Butler
Version: 1.0
*/
// que style
add_action('wp_enqueue_scripts', 'echotoc_scripts');
function echotoc_scripts(){
	wp_enqueue_style('echotocstyle', plugins_url('/style.css', __FILE__) );
}
// register widget
add_action( 'widgets_init', function(){
	register_widget( 'EchoKB_TOC_Widget' );
});

class EchoKB_TOC_Widget extends WP_Widget {
	// class constructor
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'EchoKB_TOC_Widget',
			'description' => 'Adds a TOC widget for Echo KB',
		);
		parent::__construct( 'EchoKB_TOC_Widget', 'Echo TOC', $widget_ops );
	}
	
	// output the widget content on the front-end
	public function widget( $args, $instance ) {
		extract($args);
		echo $before_widget;
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		
		$custom_terms = get_terms('epkb_post_type_1_category');
		echo '<div class="echo-toc">';
		foreach($custom_terms as $custom_term) {
			wp_reset_query();
			$args = array(
				'post_type' => 'epkb_post_type_1',
				'orderby' => 'title',
				'order' => 'ASC',
				'tax_query' => array(
					array(
						'taxonomy' => 'epkb_post_type_1_category',
						'field' => 'slug',
						'terms' => $custom_term->slug						
					),
				),
			 );
			//add_filter( 'posts_orderby', array(&$this, 'filter_query' ));
			 $loop = new WP_Query($args);
			 //remove_filter( 'posts_orderby', array(&$this,'filter_query' ));
			
			if($loop->have_posts()) {		
				echo '<input type="checkbox" id="check_' . $custom_term->name . '">';
				echo '<label for ="check_' . $custom_term->name . '">'.$custom_term->name.'</label>';
				
				echo '<ul>';
				while($loop->have_posts()) : $loop->the_post();
					echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
				endwhile;
				echo '</ul></br>';
			}
			
		}
		echo '</div>';

		echo $after_widget;
	}


	function filter_query( $query ) {
		$query .= ', wp_posts.menu_order DESC';
		return $query;
	}

	// output the option form field in admin Widgets screen
	public function form( $instance ) {

		
	}

	// save options
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			
		$selected_posts = ( ! empty ( $new_instance['selected_posts'] ) ) ? (array) $new_instance['selected_posts'] : array();
		$instance['selected_posts'] = array_map( 'sanitize_text_field', $selected_posts );

		return $instance;
	}
	public function m_explode(array $array,$key = ''){     
        if( !is_array($array) or $key == '')
             return;        
        $output = array();

        foreach( $array as $v ){        
            if( !is_object($v) ){
                return;
            }
            $output[] = $v->$key;

        }

        return $output;

      }
}