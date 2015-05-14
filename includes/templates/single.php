<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
 * @version 2.0.3
 * @since 2.0
 */
get_header(); 
global $Isa_Organized_Docs; 
$schema = '';
$itemprop_name = '';
$article_body = '';
	
if ( ! get_option('od_disable_microdata') ) {
	$schema = ' itemscope itemtype="http://schema.org/TechArticle"';
	$itemprop_name = ' itemprop="name"';
	$article_body = ' itemprop="articleBody"';
} ?>
<div id="docs-primary" <?php if($schema) echo $schema; ?>>
<div id="docs-content" role="main">
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
	echo $Isa_Organized_Docs->organized_docs_single_section_heading();
	echo $Isa_Organized_Docs->organized_docs_content_nav();
	wp_enqueue_style('organized-docs');
	if ( ! get_option('od_hide_print_link') ) { ?>
		<p id="odd-print-button">
		<?php if ( ! get_option('od_hide_printer_icon') ) {
					if ( ! get_option( 'od_dont_load_fa' ) ) {
						wp_enqueue_style( 'font-awesome','//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
					} ?>
					<i class="fa fa-print"></i>
		<?php } ?>
		<a href="javascript:window.print()" class="button"><?php _e( 'Print', 'organized-docs' ); ?></a>
		</p>
	<?php } ?>
	
	<header class="docs-entry-header">
	<h1 class="entry-title" <?php if($itemprop_name) echo $itemprop_name; ?>><?php the_title(); ?></h1>
	</header>

	<div class="docs-entry-content" <?php if($article_body) echo $article_body; ?>>
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
	<?php $Isa_Organized_Docs->organized_docs_post_nav(); 
	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
	?>
</article><!-- #post-## -->
</div><!-- #docs-content -->
<?php $sidebar = $Isa_Organized_Docs->get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>
</div><!-- #docs-primary -->
<?php get_footer();