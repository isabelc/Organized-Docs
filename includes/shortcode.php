<?php
add_shortcode( 'organized_docs', 'organized_docs_shortcode' );
/**
 * Main Docs list shortcode.
 */
function organized_docs_shortcode() {

	global $post, $Isa_Organized_Docs;

	$schema = '';
	if ( ! get_option('od_disable_microdata') ) {
		$schema = ' itemscope itemtype="http://schema.org/CollectionPage"';
	}
	$sorted_terms = $Isa_Organized_Docs->get_sorted_main_item_terms();
	$count = count( $sorted_terms );	

	ob_start();

	?>
	<section id="docs-primary" class="docs-content-area" <?php if($schema) echo $schema; ?>>
		<?php if ( $schema ) { ?>
			<meta itemprop="name" content="<?php the_title_attribute(); ?>">
		<?php }	?>
		<div id="docs-content" class="docs-site-content" role="main">
		<article <?php post_class('docs-archive-template'); ?>>
			<?php do_action( 'organized_docs_main_content_before' ); ?>
			<div class="docs-entry-content">
			<?php wp_enqueue_style('organized-docs'); ?>
			
			<div class="isa-docs-archive-content">
			<?php do_action( 'organized_docs_main_content_after_nav' ); 
			
			// Display a list of subTerms, within a specified Term, AND show all the posts within each of those subTerms on archive page

			if ( $count > 0 ) { ?>
				
				<ul id="organized-docs-main">

				<?php foreach ( $sorted_terms as $id => $name ) { ?>

					<li><a href="<?php echo get_term_link( $id, 'isa_docs_category' ); ?>" title="<?php echo esc_attr( $name ); ?>"><?php echo $name; ?></a></li>

				<?php } ?>

				</ul>
				
				<?php
			}
			do_action( 'organized_docs_main_content_bottom' ); ?>
			</div><!-- .isa-docs-archive-content -->
			</div><!-- .docs-entry-content -->
		</article>
		</div><!-- #docs-content -->
	</section><!-- #docs-primary -->

	<?php
	/* Get buffer content */
	$sc = ob_get_contents();

	/* Clean the buffer */
	ob_end_clean();

	/* Return shortcode's content */
	return $sc;

}