<?php
/*
 * Template Name: Organized Docs
 *
 * The template for displaying Organized Docs Archive pages, and the Docs main page, but not single Docs.
 * 
 * @package		Organized Docs
 * @since		1.0
 */
get_header(); ?>
<div id="primary">
<div id="content" role="main">
<article class="docs-archive-template hentry">
<?php do_action( 'organized_docs_content_before' ); ?>
<div class="entry-content">
<?php global $Isa_Organized_Docs;
echo $Isa_Organized_Docs->organized_docs_section_heading();
echo $Isa_Organized_Docs->organized_docs_content_nav();
do_action( 'organized_docs_content_after_nav' ); 
if ( have_posts() ) :
	
		// Display a list of subTerms, within a specified Term, AND show all the posts within each of those subTerms on archive page
		
		// get current term id on category, taxonomy or archive page

		if ( is_tax( 'isa_docs_category' ) ) {

			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

			$curr_termID = $term->term_id;
			$curr_term_name = $term->name;
	
			// get term children
				
			$termchildren =  get_term_children( $curr_termID, 'isa_docs_category' );

		}
		if ( empty($termchildren) ) {

			// if main docs page, do only top level terms

			if( is_post_type_archive( 'isa_docs' ) ) {

				// this is main docs page

				$terms = get_terms( 'isa_docs_category' );

					
				// need simple array of term ids to sort... @test
				$term_ids = array();
				foreach ( $terms as $single_term_object ) {

					$term_ids[] = $single_term_object->term_id;

				}

				// sort terms by custom sort-order meta @test
				$sorted_term_ids = $Isa_Organized_Docs->sort_terms( $term_ids, 'main_doc_item_sort_order' );

				$count = count( $sorted_term_ids );
				if ( $count > 0 ) {
				     echo '<ul id="organized-docs-main">';
				     foreach ( $sorted_term_ids as $sorted_term_id => $sorted_term_id_order ) {

// @test remove redundant						global $Isa_Organized_Docs;

						if( $sorted_term_id == $Isa_Organized_Docs->isa_term_top_parent_id( $sorted_term_id ) ) {
							// this is a top parent
							$top_parent_term = get_term( $sorted_term_id, 'isa_docs_category');
							echo '<li><a href="' . get_term_link( $sorted_term_id, 'isa_docs_category' ).'" title="' . esc_attr( $top_parent_term->name ) . '">' . $top_parent_term->name . '</a></li>';
						} // end if
				        
					} // end foreach
				echo '</ul>';
				} // end if ( $count > 0 )

				// done with main Docs page

			} else {
		
				/**
				* Not main docs page, and There are no child terms, do regular term loop to list posts within current term
				*/
				echo '<ul>';
				while ( have_posts() ) {
						the_post();
			
					echo '<li><a href="' . get_permalink($post->ID).'">' . get_the_title() . '</a></li>';		
			
				}
				echo '</ul>';


			} // end check for main docs archive page
		
		} else {

				/** 
				* there are subTerms, do list subTerms with all its posts for each subTerm
				*/

				// sort $termchildren by custom subheading_sort_order numbers
				$sorted_termchildren = $Isa_Organized_Docs->sort_terms( $termchildren, 'subheading_sort_order' );

				foreach ( $sorted_termchildren as $child_id => $order ) {

					$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

					//Display the sub Term information
					echo '<h2>' . $termobject->name . '</h2>';
					echo '<ul>';
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
							echo '<li><a href="'.get_permalink($single_post->ID).'" title="' . esc_attr( $single_post->post_title ) .'">'.$single_post->post_title.'</a></li>';   
				        }  

				        echo '</ul>';

					} // end foreach ( $sorted_termchildren as $child_id => $order ) {
			}// end if ( empty($termchildren) ) else
		else : ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
		<?php 
		endif;
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .entry-content -->
</article>
</div><!-- #content -->
</div><!-- #primary -->
<!-- end docs-template.php -->
<?php get_footer();