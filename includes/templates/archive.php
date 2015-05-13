<?php
/*
* The template for displaying the main Docs page
* @package	Organized Docs
* @since 2.0
*/
get_header(); 

$schema = '';
$itemprop_name = '';
if ( ! get_option('od_disable_microdata') ) {
	$schema = ' itemscope itemtype="http://schema.org/CollectionPage"';
	$itemprop_name = ' itemprop="name"';
}
?>
<section id="primary" class="content-area" <?php if($schema) echo $schema; ?>>
<div id="content" class="site-content" role="main">
<article <?php post_class('docs-archive-template'); ?>>
	<?php do_action( 'organized_docs_main_content_before' ); ?>
	<div class="entry-content">
	<h1 id="isa-docs-main-title" class="entry-title" <?php if($itemprop_name) echo $itemprop_name; ?>>
		<?php 
		$custom_title = get_option('od_change_main_docs_title');
		$page_title = $custom_title ? sanitize_text_field( $custom_title ) : __('Docs', 'organized-docs');
		echo apply_filters( 'od_docs_main_title', $page_title ); ?>
	</h1>
	<?php wp_enqueue_style('organized-docs'); ?>
	
	<div class="isa-docs-archive-content">
	<?php do_action( 'organized_docs_main_content_after_nav' ); 
	
	// Display a list of subTerms, within a specified Term, AND show all the posts within each of those subTerms on archive page
		
	// get current term id on category or archive page

	// do only top level terms
	
	$terms = get_terms( 'isa_docs_category' );
					
	// need simple array of term ids to sort
	$term_ids = array();
	foreach ( $terms as $single_term_object ) {
		$term_ids[] = $single_term_object->term_id;
	}

	// sort terms by custom sort-order meta
	global $Isa_Organized_Docs;
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
	do_action( 'organized_docs_main_content_bottom' ); ?>
	</div><!-- .isa-docs-archive-content -->
	</div><!-- .entry-content -->
</article>
</div><!-- #content -->
</section><!-- #primary -->
<?php get_footer();