<<<<<<< HEAD
<?php
/*
 * Template Name: Organized Docs
 * The template for displaying Organized Docs Archive pages, and the Docs main page, but not single Docs.
 * @package	Organized Docs
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
echo $Isa_Organized_Docs->organized_docs_content_nav(); ?>

<div class="isa-docs-archive-content">
	<?php do_action( 'organized_docs_content_after_nav' ); 
	
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
					
			// need simple array of term ids to sort
			$term_ids = array();
			foreach ( $terms as $single_term_object ) {
				$term_ids[] = $single_term_object->term_id;
			}

			// sort terms by custom sort-order meta
			$sorted_term_ids = $Isa_Organized_Docs->sort_terms( $term_ids, 'main_doc_item_sort_order' );

			$count = count( $sorted_term_ids );
			if ( $count > 0 ) {
			     echo '<ul id="organized-docs-main">';
			     foreach ( $sorted_term_ids as $sorted_term_id => $sorted_term_id_order ) {
					if( $sorted_term_id == $Isa_Organized_Docs->isa_term_top_parent_id( $sorted_term_id ) ) {
						// this is a top parent
						$top_parent_term = get_term( $sorted_term_id, 'isa_docs_category');
						echo '<li><a href="' . get_term_link( $sorted_term_id, 'isa_docs_category' ).'" title="' . esc_attr( $top_parent_term->name ) . '">' . $top_parent_term->name . '</a></li>';
					}
				        
				}
				echo '</ul>';
			} // end if ( $count > 0 

		} else {
		
			// Not main docs page, and there are no child terms, do regular term loop to list posts within current term
			if ( have_posts() ) :
			echo '<ul>';
			while ( have_posts() ) {
				the_post();
				echo '<li><a href="' . get_permalink($post->ID).'">' . get_the_title() . '</a></li>';		
			}
			echo '</ul>';
	
	 		else : ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
			<?php 
			endif;

		} // end check for main docs archive page
		
	} else {

			// there are subTerms, do list subTerms with all its posts for each subTerm

			// sort $termchildren by custom subheading_sort_order numbers
			$sorted_termchildren = $Isa_Organized_Docs->sort_terms( $termchildren, 'subheading_sort_order' );

			foreach ( $sorted_termchildren as $child_id => $order ) {

				$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

				//Display the sub Term information
				echo '<h2>' . $termobject->name . '</h2>';
				echo '<ul>';
				global $post;

				// @test orderby custom option
				
				$single_sort_by = get_option('od_single_sort_by');
				$single_sort_by_order = get_option('od_single_sort_by_order');// @todo make option
				
				if ( 'date' == $single_sort_by ) {
					$orderby = 'date';
				} elseif ( 'title - alphabetical' == $single_sort_by ) {
					$orderby = 'title';
				} else {
					$orderby = 'meta_value_num';
				}
			
				if ( 'descending' == single_sort_by_order ) {
					$orderby_order = 'DESC';
				} else {
					$orderby_order = 'ASC';
				}

				// prep nested loop
				$args = array(	'post_type' => 'isa_docs', 
							'posts_per_page' => -1,
							'tax_query' => array(
									array(
										'taxonomy' => 'isa_docs_category',
										'field' => 'id',
										'terms' => $termobject->term_id
									)
								),
							'orderby' => $orderby,// @test
							'meta_key' => '_odocs_meta_sortorder_key',// @test does this hurt when not needed
							'order' => $orderby_order// @test
				);

				$postlist = get_posts( $args );

				foreach ( $postlist as $single_post ) {
					echo '<li><a href="'.get_permalink($single_post->ID).'" title="' . esc_attr( $single_post->post_title ) .'">'.$single_post->post_title.'</a></li>';   
				}  

		        echo '</ul>';

			} // end foreach ( $sorted_termchildren as $child_id => $order )

		}// end check for empty $termchildren
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .isa-docs-archive-content -->
</div><!-- .entry-content -->
</article>
</div><!-- #content -->
</div><!-- #primary -->
<!-- end docs-template.php -->
=======
<?php
/*
 * Template Name: Organized Docs
 * The template for displaying Organized Docs Archive pages, and the Docs main page, but not single Docs.
 * @package	Organized Docs
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
echo $Isa_Organized_Docs->organized_docs_content_nav(); ?>

<div class="isa-docs-archive-content">
	<?php do_action( 'organized_docs_content_after_nav' ); 
	
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
					
			// need simple array of term ids to sort
			$term_ids = array();
			foreach ( $terms as $single_term_object ) {
				$term_ids[] = $single_term_object->term_id;
			}

			// sort terms by custom sort-order meta
			$sorted_term_ids = $Isa_Organized_Docs->sort_terms( $term_ids, 'main_doc_item_sort_order' );

			$count = count( $sorted_term_ids );
			if ( $count > 0 ) {
			     echo '<ul id="organized-docs-main">';
			     foreach ( $sorted_term_ids as $sorted_term_id => $sorted_term_id_order ) {
					if( $sorted_term_id == $Isa_Organized_Docs->isa_term_top_parent_id( $sorted_term_id ) ) {
						// this is a top parent
						$top_parent_term = get_term( $sorted_term_id, 'isa_docs_category');
						echo '<li><a href="' . get_term_link( $sorted_term_id, 'isa_docs_category' ).'" title="' . esc_attr( $top_parent_term->name ) . '">' . $top_parent_term->name . '</a></li>';
					}
				        
				}
				echo '</ul>';
			} // end if ( $count > 0 

		} else {
		
			// Not main docs page, and there are no child terms, do regular term loop to list posts within current term
			if ( have_posts() ) :
			echo '<ul>';
			while ( have_posts() ) {
				the_post();
				echo '<li><a href="' . get_permalink($post->ID).'">' . get_the_title() . '</a></li>';		
			}
			echo '</ul>';
	
	 		else : ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
			<?php 
			endif;

		} // end check for main docs archive page
		
	} else {

			// there are subTerms, do list subTerms with all its posts for each subTerm

			// sort $termchildren by custom subheading_sort_order numbers
			$sorted_termchildren = $Isa_Organized_Docs->sort_terms( $termchildren, 'subheading_sort_order' );

			foreach ( $sorted_termchildren as $child_id => $order ) {

				$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

				//Display the sub Term information
				echo '<h2>' . $termobject->name . '</h2>';
				echo '<ul>';
				global $post;

				// @test orderby custom option
				
				$single_sort_by = get_option('od_single_sort_by');
				$single_sort_by_order = get_option('od_single_sort_by_order');// @todo make option
				
				if ( 'date' == $single_sort_by ) {
					$orderby = 'date';
				} elseif ( 'title - alphabetical' == $single_sort_by ) {
					$orderby = 'title';
				} else {
					$orderby = 'meta_value_num';
				}
			
				if ( 'descending' == single_sort_by_order ) {
					$orderby_order = 'DESC';
				} else {
					$orderby_order = 'ASC';
				}

				// prep nested loop
				$args = array(	'post_type' => 'isa_docs', 
							'posts_per_page' => -1,
							'tax_query' => array(
									array(
										'taxonomy' => 'isa_docs_category',
										'field' => 'id',
										'terms' => $termobject->term_id
									)
								),
							'orderby' => $orderby,// @test
							'meta_key' => '_odocs_meta_sortorder_key',// @test does this hurt when not needed
							'order' => $orderby_order// @test
				);

				$postlist = get_posts( $args );

				foreach ( $postlist as $single_post ) {
					echo '<li><a href="'.get_permalink($single_post->ID).'" title="' . esc_attr( $single_post->post_title ) .'">'.$single_post->post_title.'</a></li>';   
				}  

		        echo '</ul>';

			} // end foreach ( $sorted_termchildren as $child_id => $order )

		}// end check for empty $termchildren
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .isa-docs-archive-content -->
</div><!-- .entry-content -->
</article>
</div><!-- #content -->
</div><!-- #primary -->
<!-- end docs-template.php -->
>>>>>>> c65e9c58b33780dc4b6ffd406113856ce8d7c1ec
<?php get_footer();