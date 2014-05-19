<?php
/*
* The template for displaying Docs category taxonomy archives
* @package	Organized Docs
* @since 2.0
*/
get_header(); ?>
<section id="primary" class="content-area">
<div id="content" class="site-content" role="main">
<article <?php post_class('docs-archive-template'); ?>>
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
		// there are no child terms, do regular term loop to list posts within current term
		if ( have_posts() ) : ?>
			<ul>
			<?php while ( have_posts() ) {
				the_post(); ?>
				<li><a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title(); ?></a></li>
			<?php } ?>
			</ul><?php
	 	else : ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
			<?php 
		endif;
	} else {

		// there are subTerms, do list subTerms with all its posts for each subTerm

		// sort $termchildren by custom subheading_sort_order numbers
		$sorted_termchildren = $Isa_Organized_Docs->sort_terms( $termchildren, 'subheading_sort_order' );

		foreach ( $sorted_termchildren as $child_id => $order ) {

			$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

			//Display the sub Term information ?>
			<h2><?php echo $termobject->name; ?></h2>
			<?php
			// @test only list all posts if not disabled with setting
			if( ! get_option('od_disable_list_each_single') ) { ?>
			
				<ul><?php
				global $post;
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
							'orderby' => $orderby,
							'meta_key' => '_odocs_meta_sortorder_key',
							'order' => $orderby_order
				);
				$postlist = get_posts( $args );
				foreach ( $postlist as $single_post ) { ?>
					<li><a href="<?php echo get_permalink($single_post->ID); ?>" title="<?php echo esc_attr( $single_post->post_title ); ?>"><?php echo $single_post->post_title; ?></a></li>
				<?php } ?>
				</ul><?php
			}
		} // end foreach ( $sorted_termchildren as $child_id => $order )

	}// end check for empty $termchildren
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .isa-docs-archive-content -->
</div><!-- .entry-content -->
</article>
</div><!-- #content -->
</section><!-- #primary -->
<?php get_footer();