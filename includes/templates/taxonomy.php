<?php
/**
* The template for displaying Docs category taxonomy archives
* @package Organized Docs
* @version 2.6
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
	
	/* Display a list of subTerms, within a specified Parent Cat, AND show all the posts within
	   each of those subTerms on archive page */
		
	// get current term id on docs category taxonomy page
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
	$curr_termID = $term->term_id;
	do_action( 'organized_docs_microdata_cat', $curr_termID );
	
	// get term children
	$termchildren =  get_term_children( $curr_termID, 'isa_docs_category' );

	if ( empty( $termchildren ) ) {
		// there are no child terms, do regular term loop to list ALL posts within current term
		$docs = odocs_query_docs( $curr_termID );
		if ( ! empty( $docs[0] ) ) { ?>
			<ul>
				<?php foreach ( $docs as $single_doc ) { ?>
					<li><a href="<?php echo esc_url( get_permalink( $single_doc->ID ) ); ?>"><?php echo esc_html( $single_doc->post_title ); ?></a></li>
				<?php } ?>
			</ul>
		<?php } else { ?>
			<h2><?php _e( 'Error 404 - Not Found', 'organized-docs' ); ?></h2>
		<?php }

	} else {
	
		// There are subTerms, do list each subTerm with all its posts under each subTerm.

		// But first, list any orphaned posts not assiged to a subTerm and only assiged directly to a parent cat
		$orphans = odocs_query_docs( $curr_termID, true );
		if ( ! empty( $orphans[0] ) ) {
			?>
			<ul>
			<?php
			foreach ( $orphans as $orphan ) { ?>
				<li><a href="<?php echo esc_url( get_permalink( $orphan->ID ) ); ?>"><?php echo esc_html( $orphan->post_title ); ?></a></li>
			<?php } ?>
			</ul><?php
		}

		$list_each = get_option('od_list_toggle');
		
		// sort $termchildren by custom subheading_sort_order numbers
		$sorted_termchildren = $Isa_Organized_Docs->sort_terms_custom( $termchildren, 'subheading_sort_order' );

		foreach ( $sorted_termchildren as $child_id => $order ) {

			$termobject = get_term_by( 'id', $child_id, 'isa_docs_category' );

			//Display the sub Term information 
			?>
			<h2 class="docs-sub-heading"><?php echo esc_html( $termobject->name ); ?></h2>
			<ul<?php
			if ( 'toggle' == $list_each ) {
				echo ' style="display:none"';
			}
			?>>
			<?php
			$postlist = odocs_query_docs( $termobject->term_id );
			if ( empty( $postlist[0] ) ) {
				continue;
			}

			foreach ( $postlist as $single_post ) { ?>
				<li><a href="<?php echo esc_url( get_permalink($single_post->ID) ); ?>"><?php echo esc_html( $single_post->post_title ); ?></a></li>
			<?php } ?>
			</ul><?php
		}
		if ( 'toggle' == $list_each ) {
			wp_enqueue_script( 'organized-docs-toggle' );
		}		

	}// end check for empty $termchildren
	do_action( 'organized_docs_content_bottom' ); ?>
</div><!-- .isa-docs-archive-content -->
</div><!-- .docs-entry-content -->
</article>
</div><!-- #docs-content -->
</section><!-- #docs-primary -->
<?php get_footer();