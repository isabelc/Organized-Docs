<?php
/**
 * Adds Docs Section Contents widget
 *
 * Shows list of documentation articles for 1 item (top-level docs category). Works only on single Docs.
 * 
 * @package		Organized Docs
 * @extends		WP_Widget
 * @author		Isabel Castillo <me@isabelcastillo.com>
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 */

class DocsSectionContents extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'docs_section_contents', // Base ID
			__( 'Organized Docs Section Contents', 'organized-docs' ), // Name
			array( 'description' => __( 'Shows list of documentation articles for 1 item (top-level docs category). Works on single Docs sidebar.', 'organized-docs' ), )
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if ( ! empty( $title ) )
			echo '<h3 class="widget-title">'. $title . '</h3>';
		
		// get current term id
		global $post, $Isa_Organized_Docs;

		$current_single_postID = $post->ID;// to highlight current item below
		$doc_categories = wp_get_object_terms( $post->ID, 'isa_docs_category' );
		$first_term = $doc_categories[0];
		$curr_term_id = $first_term->term_id;
		$top_level_parent_term_id = $Isa_Organized_Docs->isa_term_top_parent_id( $curr_term_id );

		// get term children
		$termchildren =  get_term_children( $top_level_parent_term_id, 'isa_docs_category' );
	
		foreach ( $termchildren as $child ) {

			$termobject = get_term_by( 'id', $child, 'isa_docs_category' );
			//Display the sub Term information, in open widget container
			echo '<aside class="widget well"><h3 class="widget-title">' . $termobject->name . '</h3>';
			echo '<ul>';
			// nest a loop through each child cat's posts
			global $post;
			$args = array(	'post_type' => 'isa_docs', 
						'posts_per_page' => -1,
						'order' => 'ASC',
						'tax_query' => array(
										array(
											'taxonomy' => 'isa_docs_category',
											'field' => 'id',
											'terms' => $termobject->term_id
											)
									)
				);
			$postlist = get_posts( $args );
			foreach ( $postlist as $single_post ) {
					echo '<li';
					if( $single_post->ID == $current_single_postID ) 
						echo ' class="organized-docs-active-side-item"';	// @test end
					echo '><a href="' . get_permalink( $single_post->ID ) . '" title="' . esc_attr( $single_post->post_title ) . '">' . $single_post->post_title . '</a></li>';   
			}  
					echo '</ul></aside>';
		} // end foreach ( $termchildren
		
		echo $after_widget;

	}// end widget

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Table of Contents', 'organized-docs' );
		} ?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'organized-docs' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php 
	}
}
?>