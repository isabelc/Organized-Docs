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

		do_action('organized_docs_before_widget');

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
		$top_level_parent_term_id = $Isa_Organized_Docs->term_top_parent_id( $curr_term_id );

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
 
		if ( empty( $termchildren ) ) {

			/* There are no child terms. 
			 * This happens when docs are assigned directly to the top level category, rather than to sub-categories.
			 *
			 * Do regular term loop to list ALL posts within current term,
			 * unless its top level category only has 1 post under it...
			 * If cat has only 1 post under it, then show "jump-links" to jump down on this page,
			 * rather than show the link to same current page.
			 */
			
			$only_one = ( $Isa_Organized_Docs->count_cat_posts( $top_level_parent_term_id ) < 2 );

			if ( $only_one ) {
				wp_enqueue_script( 'organized-docs' );

				// The category for this post only has this 1 post under it

				// Rather than show the link to same current page, use JS to find the h2#docs- and show links to those
				?>
				
				<aside class="widget well"><ul id="odocs-only-one"></ul></aside><?php


			} else {

				// Do regular term loop to list ALL posts within current term
				$the_query = odocs_query_docs( $curr_term_id );
				if ( ! empty( $the_query[0] ) ) { ?>
					<aside class="widget well"><ul>
					<?php 
					foreach ( $the_query as $single_doc ) {
						echo '<li';
						if ( $single_doc->ID == $current_single_postID ) {
							echo ' class="organized-docs-active-side-item"';
						}
						echo '><a href="' . esc_url( get_permalink( $single_doc->ID ) ) . '">' . esc_html( $single_doc->post_title ) . '</a></li>';
					}
					?>
					</ul></aside><?php
				}
			}

		} else {

			// We have term children

			// First, list any orphaned posts not assiged to a child cat and only assiged directly to a parent cat
			$orphans = odocs_query_docs( $top_level_parent_term_id, true );
			if ( ! empty( $orphans[0] ) ) {
				?>
				<aside class="widget well"><ul>
				<?php
				foreach ( $orphans as $orphan ) { ?>
					<li><a href="<?php echo esc_url( get_permalink( $orphan->ID ) ); ?>"><?php echo esc_html( $orphan->post_title ); ?></a></li>
				<?php } ?>
				</ul></aside><?php
			}


			// sort $termchildren by custom subheading_sort_order numbers
			$sorted_termchildren = $Isa_Organized_Docs->sort_terms_custom( $termchildren, 'subheading_sort_order' );

			$list_each = get_option('od_widget_list_toggle');

			if ( $sorted_termchildren ) {

				foreach ( $sorted_termchildren as $child_id => $order ) {
					$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );
					
					//Display the sub Term information, in open widget container ?>
					<aside class="widget well">
					<h3 class="widget-title docs-sub-heading"><?php echo $termobject->name; ?></h3>
					<ul<?php
					if ( 'toggle' == $list_each ) {
						echo ' style="display:none"';
					}
					?>><?php
					$postlist = odocs_query_docs( $termobject->term_id );
					if ( ! empty( $postlist[0] ) ) {
						foreach ( $postlist as $single_post ) {
								echo '<li';
								if( $single_post->ID == $current_single_postID ) 
									echo ' class="organized-docs-active-side-item"';
								echo '><a href="' . esc_url( get_permalink( $single_post->ID ) ) . '" title="' . esc_attr( $single_post->post_title ) . '">' . esc_html( $single_post->post_title ) . '</a></li>';   
						}
					}
					?>
					</ul>
					</aside><?php
				}
			}

		}
		echo $args['after_widget'];
		if ( 'toggle' == $list_each ) {
			wp_enqueue_script( 'organized-docs-toggle' );
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