<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
 * @version 2.4.1
 * @since 2.0
 */
get_header(); 
global $Isa_Organized_Docs; 
$schema = '';
$schema_main_entity = '';
$itemprop_name = '';
$article_body = '';
$schema_date = '';
$schema_img = '';
$pub = '';

if ( ! get_option('od_disable_microdata') ) {
	$schema = ' itemscope itemtype="http://schema.org/' . apply_filters( 'od_single_schema_type', 'TechArticle' ) . '"';
	$schema_main_entity = '<meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="' . esc_url( get_the_permalink() ) . '" />';	
	$itemprop_name = ' itemprop="headline"';
	$article_body = apply_filters( 'od_single_schema_itemprop_body', ' itemprop="articleBody"' );
	$schema_date = apply_filters( 'od_single_schema_date', '<meta itemprop="datePublished" content="' . get_the_time('c') . '">' );

	if ( has_post_thumbnail() ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id() );
		$img_url = $image[0];
		$width = $image[1];
		$height = $image[2];
	} else {
		$img_url = apply_filters( 'od_schema_img', plugins_url( '/organized-docs.png', dirname( __FILE__ ) ) );
		$width = apply_filters( 'od_schema_img_width', '128' );
		$height = apply_filters( 'od_schema_img_height', '128' );
	}
	$schema_img = '<span itemprop="image" itemscope itemtype="https://schema.org/ImageObject"><meta itemprop="url" content="' . esc_attr( $img_url ) . '"><meta itemprop="width" content="' . esc_attr( $width ) . '"><meta itemprop="height" content="' . esc_attr( $height ) . '"></span>';

	$pub_logo = apply_filters( 'od_schema_pub_logo', false );
	$pub_logo_width = apply_filters( 'od_schema_pub_logo_width', '' );
	$pub_logo_height = apply_filters( 'od_schema_pub_logo_height', '' );
	$pub_name = apply_filters( 'od_schema_pub_name', '' );

	if ( ! empty( $pub_logo ) ) {
		$pub = '<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization"><meta itemprop="name" content="' . esc_attr( $pub_name ) . '"><span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject"><meta itemprop="url" content="' . esc_attr( $pub_logo ) . '"><meta itemprop="width" content="' . esc_attr( $pub_logo_width ) . '"><meta itemprop="height" content="' . esc_attr( $pub_logo_height ) . '"></span></span>';

	}
} ?>
<div id="docs-primary" <?php if($schema) echo $schema; ?>>
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
	do_action( 'organized_docs_single_after_content' );
	$Isa_Organized_Docs->updated_on( 'below' );
	$Isa_Organized_Docs->organized_docs_post_nav(); 

	$author_name = apply_filters( 'od_author_name', get_the_author() );
	if ( apply_filters( 'od_display_author', false ) ) {
		$author = 'By <span itemprop="name">' . esc_html( $author_name ) . '</span>';
	} else {
		$author = '<meta itemprop="name" content="' . esc_attr( $author_name ) . '">';
	}
	$schema_auth = '<span id="od-author" itemprop="author" itemscope itemtype="http://schema.org/Person">' . $author . '</span>';

	echo $schema_date;
	echo $schema_img;
	echo $schema_main_entity;
	echo $pub;
	echo $schema_auth;

	if ( ! get_option( 'od_close_comments' ) ) {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	}
	?>
</article><!-- #post-## -->
</div><!-- #docs-content -->
<?php $sidebar = $Isa_Organized_Docs->get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>
</div><!-- #docs-primary -->
<?php get_footer();