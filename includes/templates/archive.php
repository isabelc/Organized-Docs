<?php
/**
* The template for displaying the main Docs page
* @package	Organized Docs
* @version 2.5
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
<section id="docs-primary" class="docs-content-area" <?php if($schema) echo $schema; ?>>
	<div id="docs-content" class="docs-site-content" role="main">
		<article <?php post_class('docs-archive-template'); ?>>
			<?php do_action( 'organized_docs_main_content_before' ); ?>
			<div class="docs-entry-content">
			<h1 id="isa-docs-main-title" class="entry-title" <?php if($itemprop_name) echo $itemprop_name; ?>>
				<?php 
				$custom_title = get_option('od_change_main_docs_title');
				$page_title = $custom_title ? sanitize_text_field( $custom_title ) : __('Docs', 'organized-docs');
				echo $page_title; ?>
			</h1>
			<?php wp_enqueue_style('organized-docs'); ?>
			
			<div class="isa-docs-archive-content">
			<?php do_action( 'organized_docs_main_content_after_nav' ); 
			
			global $Isa_Organized_Docs;
			$sorted_terms = $Isa_Organized_Docs->get_sorted_main_item_terms();
			$count = count( $sorted_terms );
			if ( $count > 0 ) { ?>
				
				<ul id="organized-docs-main">

				<?php foreach ( $sorted_terms as $id => $name ) { ?>
					<li>
					<?php
					/* If the term only has 1 post under it, then link directly to the post.
					 * Otherwise, link to the tax archive page. */

					if ( $Isa_Organized_Docs->count_cat_posts( $id ) < 2 ) {

						$args = array(
							'post_type'			=> 'isa_docs',
							'tax_query'			=> array(
									array(
										'taxonomy' => 'isa_docs_category',
										'terms'    => $id,
									),
							),
						);
						$the_post = get_posts( $args );
						?>
						<a href="<?php esc_url( the_permalink( $the_post[0] ) ); ?>"><?php echo esc_html( $the_post[0]->post_title ); ?></a>
						<?php

					} else { ?>
	
						<a href="<?php echo esc_url( get_term_link( $id, 'isa_docs_category' ) ); ?>"><?php echo esc_html( $name ); ?></a>

					<?php } ?>

					</li>
				
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
<?php get_footer();