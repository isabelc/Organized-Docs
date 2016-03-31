<?php
/**
 * Adds Docs Section Contents widget
 * Shows list of documentation articles for 1 item (top-level docs category). Works only on single Docs.
 * @package	Organized Docs
 * @extends	WP_Widget
 * @author	Isabel Castillo <me@isabelcastillo.com>
 * @licens	http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class DocsSectionContents extends WP_Widget {
	/** Register widget */
	public function __construct() {
		parent::__construct(
	 		'docs_section_contents',
			__( 'Organized Docs Section Contents', 'organized-docs' ),
			array( 'description' => __( 'Shows list of documentation articles for 1 item (top-level docs category). Works on single Docs sidebar.', 'organized-docs' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	
		if ( ! is_singular('isa_docs') )
			return;
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Table of Contents', 'organized-docs' ) : $instance['title'], $instance, $this->id_base );		
		echo $args['before_widget'];
		if ( $title ) {
			echo '<h3 class="widget-title">'. $title . '</h3>';
		}
		// get current term id
		global $post, $Isa_Organized_Docs;
		$current_single_postID = $post->ID;// to highlight current item below
		
		$doc_categories = wp_get_object_terms( $post->ID, 'isa_docs_category' );
		
		if ( ! $doc_categories ) {
			// cat has not been assigned yet
			return;
		}
			
		$first_term = $doc_categories[0];
		$curr_term_id = $first_term->term_id;
		$top_level_parent_term_id = $Isa_Organized_Docs->isa_term_top_parent_id( $curr_term_id );

		// orderby custom option
		$single_sort_by = get_option('od_single_sort_by');
		$orderby_order = get_option('od_single_sort_order');
				
		if ( 'date' == $single_sort_by ) {
			$orderby = 'date';
		} elseif ( 'title - alphabetical' == $single_sort_by ) {
			$orderby = 'title';
		} else {
			$orderby = 'meta_value_num';
		}
		$list_each = '';
		// get term children
		$termchildren =  get_term_children( $top_level_parent_term_id, 'isa_docs_category' );
 
		if ( empty($termchildren) ) {
			// there are no child terms, do regular term loop to list ALL posts within current term
			$query_args = array(
						'post_type' => 'isa_docs', 
						'posts_per_page' => -1,
						'tax_query' => array(
								array(
									'taxonomy' => 'isa_docs_category',
									'field' => 'id',
									'terms' => $curr_term_id
								)
							),
						'orderby' => $orderby,
						'meta_key' => '_odocs_meta_sortorder_key',
						'order' => $orderby_order
			);
			$the_query = new WP_Query( $query_args );
			if ( $the_query->have_posts() ) : ?>

				<aside class="widget well"><ul>
				<?php while ( $the_query->have_posts() ) {
					$the_query->the_post();
					echo '<li';
						if( $post->ID == $current_single_postID ) 
							echo ' class="organized-docs-active-side-item"';
						echo '><a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( $post->post_title ) . '">' . $post->post_title . '</a></li>';   

				} ?>
				</ul></aside><?php
			endif;
			wp_reset_postdata();
		} else {
			// sort $termchildren by custom subheading_sort_order numbers
			$sorted_termchildren = $Isa_Organized_Docs->sort_terms_custom( $termchildren, 'subheading_sort_order' );

			$list_each = get_option('od_widget_list_toggle');

			if ($sorted_termchildren) {

				foreach ( $sorted_termchildren as $child_id => $order ) {
					$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );
					
					//Display the sub Term information, in open widget container ?>
					<aside class="widget well"><h3 class="widget-title docs-sub-heading"><?php echo $termobject->name; ?></h3><?php
					
					// only list all posts if not disabled with setting
					if( $list_each != 'hide' ) { ?>
					<ul<?php
					if ( 'toggle' == $list_each ) {
						echo ' style="display:none"';
					}
					?>><?php
					// nest a loop through each child cat's posts
					$query_args = array(	'post_type' => 'isa_docs', 
								'posts_per_page' => -1,
								'order' => 'ASC',
								'tax_query' => array(
											array(
												'taxonomy' => 'isa_docs_category',
												'field' => 'id',
												'terms' => $termobject->term_id
												)
											),
								'orderby' => $orderby,
								'meta_key' => '_odocs_meta_sortorder_key',
								'order' => $orderby_order
						);
					$postlist = get_posts( $query_args );
					foreach ( $postlist as $single_post ) {
							echo '<li';
							if( $single_post->ID == $current_single_postID ) 
								echo ' class="organized-docs-active-side-item"';
							echo '><a href="' . get_permalink( $single_post->ID ) . '" title="' . esc_attr( $single_post->post_title ) . '">' . $single_post->post_title . '</a></li>';   
					} ?>
					</ul>
					<?php } ?>
					</aside><?php
				}
			}

		}
		echo $args['after_widget'];
		if ( 'toggle' == $list_each ) {
			echo $Isa_Organized_Docs->inline_js();
		}		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = empty( $instance[ 'title' ] ) ? __( 'Table of Contents', 'organized-docs' ) : $instance[ 'title' ]; ?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'organized-docs' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
		<?php 
	}
}
?>