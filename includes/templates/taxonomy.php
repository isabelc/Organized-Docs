<?php
/**
* The template for displaying Docs category taxonomy archives
* @package Organized Docs
* @version 2.2
* @since 2.0
*/
get_header();

$schema = '';
if ( ! get_option('od_disable_microdata') ) {
	$type = apply_filters( 'od_category_schema_type', 'CollectionPage' );
	$schema = ' itemscope itemtype="http://schema.org/' . $type . '"';
} ?>
<section id="docs-primary" class="docs-content-area" <?php if($schema) echo $schema; ?>>
<div id="docs-content" class="docs-site-content" role="main">
<article <?php post_class('docs-archive-template'); ?>>
<?php do_action( 'organized_docs_content_before' ); ?>
<div class="docs-entry-content">
<?php global $Isa_Organized_Docs;
do_action( 'organized_docs_tax_top' );
echo $Isa_Organized_Docs->organized_docs_archive_section_heading();
echo $Isa_Organized_Docs->organized_docs_content_nav();
wp_enqueue_style('organized-docs'); ?>

<div class="isa-docs-archive-content">
	<?php do_action( 'organized_docs_content_after_nav' ); 
	
	// Display a list of subTerms, within a specified Term, AND show all the posts within each of those subTerms on archive page
		
	// get current term id on docs category taxonomy page
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	$curr_termID = $term->term_id;
	do_action( 'organized_docs_microdata_cat', $curr_termID );
	
	// get term children
	$termchildren =  get_term_children( $curr_termID, 'isa_docs_category' );

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
	
	if ( empty($termchildren) ) {
		// there are no child terms, do regular term loop to list ALL posts within current term
		$args = array(
					'post_type' => 'isa_docs', 
					'posts_per_page' => -1,
					'tax_query' => array(
							array(
								'taxonomy' => 'isa_docs_category',
								'field' => 'id',
								'terms' => $curr_termID
							)
						),
					'orderby' => $orderby,
					'meta_key' => '_odocs_meta_sortorder_key',
					'order' => $orderby_order
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) : ?>

			<ul>
			<?php while ( $the_query->have_posts() ) {
				$the_query->the_post(); ?>
				<li><a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title(); ?></a></li>
			<?php } ?>
			</ul><?php
	 	else : ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
			<?php 
		endif;
		wp_reset_postdata();

	} else {
	
		// there are subTerms, do list subTerms with all its posts for each subTerm
		
		$list_each = get_option('od_list_toggle');
		
		// sort $termchildren by custom subheading_sort_order numbers
		$sorted_termchildren = $Isa_Organized_Docs->sort_terms_custom( $termchildren, 'subheading_sort_order' );

		foreach ( $sorted_termchildren as $child_id => $order ) {

			$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

			//Display the sub Term information 
			?>
			<h2 class="docs-sub-heading"><?php echo $termobject->name; ?></h2>
			<?php
			// only list all posts if not hidden by option
			
			if( $list_each != 'hide' ) { ?>
				<ul<?php
				if ( 'toggle' == $list_each ) {
					echo ' style="display:none"';
				}
				?>><?php
				global $post;
				
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
		}
		if ( 'toggle' == $list_each ) {
			echo $Isa_Organized_Docs->inline_js();
		}		

	}// end check for empty $termchildren
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .isa-docs-archive-content -->
</div><!-- .docs-entry-content -->
</article>
</div><!-- #docs-content -->
</section><!-- #docs-primary -->
<?php get_footer();