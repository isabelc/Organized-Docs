<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
 * @version 2.6
 * @since 2.0
 */

get_header();
global $Isa_Organized_Docs; 
?>
<div id="docs-primary" <?php echo odocs_schema_markup()['type']; ?>>
<div id="docs-content" role="main">
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	do_action( 'organized_docs_single_top' );
	echo $Isa_Organized_Docs->organized_docs_single_section_heading();
	echo $Isa_Organized_Docs->organized_docs_content_nav();
	do_action( 'organized_docs_single_after_nav');
	wp_enqueue_style('organized-docs');
	if ( ! get_option('od_hide_print_link') ) { ?>
		<p id="odd-print-button">
		<?php if ( ! get_option('od_hide_printer_icon') ) { ?>
				<span>&#9113; </span>
		<?php } ?>
		<a href="javascript:window.print()" class="button"><?php _e( 'Print', 'organized-docs' ); ?></a>
		</p>
	<?php } ?>
	
	<header class="docs-entry-header">
		<h1 class="entry-title" <?php echo odocs_schema_markup()['name']; ?>><?php the_title(); ?></h1>
		<?php odocs_updated_on( 'above' ); ?>
	</header>

	<div class="docs-entry-content" <?php echo odocs_schema_markup()['body']; ?>>
		<?php
		$content = apply_filters( 'the_content', $post->post_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		echo $content;

		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'organized-docs' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		) );
		?>
	</div><!-- .docs-entry-content -->

	<?php
	odocs_updated_on( 'below' );
	do_action( 'organized_docs_single_after_content' );
	$Isa_Organized_Docs->organized_docs_post_nav(); 

	if ( ! get_option( 'od_close_comments' ) ) {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
	?>
</article><!-- #post-## -->
</div><!-- #docs-content -->
<?php $sidebar = odocs_get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>
</div><!-- #docs-primary -->
<?php get_footer();