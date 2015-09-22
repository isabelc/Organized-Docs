<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
 * @version 2.2
 * @since 2.0
 */
get_header(); 
global $Isa_Organized_Docs; 
$schema = '';
$itemprop_name = '';
$article_body = '';
$schema_date = '';
$schema_img = '';

if ( ! get_option('od_disable_microdata') ) {

	if ( has_post_thumbnail() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) );
		$imgurl = $image[0];
	} else {
		$imgurl = plugins_url( '/organized-docs.png', dirname( __FILE__ ) );
	}
	$schema_img = apply_filters( 'od_single_schema_img', '<meta itemprop="image" content="' . $imgurl . '">' );

	$schema = ' itemscope itemtype="http://schema.org/' . apply_filters( 'od_single_schema_type', 'TechArticle' ) . '"';
	$itemprop_name = ' itemprop="headline"';
	$article_body = apply_filters( 'od_single_schema_itemprop_body', ' itemprop="articleBody"' );
	$schema_date = apply_filters( 'od_single_schema_date', '<meta itemprop="datePublished" content="' . get_the_time('c') . '">' );
} ?>
<div id="docs-primary" <?php if($schema) echo $schema; ?>>
<div id="docs-content" role="main">
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	do_action( 'organized_docs_single_top' );
	echo $Isa_Organized_Docs->organized_docs_single_section_heading();
	echo $Isa_Organized_Docs->organized_docs_content_nav();
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
		<h1 class="entry-title" <?php if($itemprop_name) echo $itemprop_name; ?>><?php the_title(); ?></h1>
		<?php $Isa_Organized_Docs->updated_on( 'above' ); ?>
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

	<?php
	$Isa_Organized_Docs->updated_on( 'below' );
	$Isa_Organized_Docs->organized_docs_post_nav(); 

	if ( ! get_option( 'od_close_comments' ) ) {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
	echo $schema_date;
	echo $schema_img;
	?>
</article><!-- #post-## -->
</div><!-- #docs-content -->
<?php $sidebar = $Isa_Organized_Docs->get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>
</div><!-- #docs-primary -->
<?php get_footer();