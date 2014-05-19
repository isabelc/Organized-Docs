<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
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
<div id="primary" <?php if($schema) echo $schema; ?>>
<div id="content" role="main">
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php 
	echo $Isa_Organized_Docs->organized_docs_section_heading();
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
	
	<header class="entry-header">
	<h1 class="entry-title" <?php if($itemprop_name) echo $itemprop_name; ?>><?php the_title(); ?></h1>
	</header>

	<div class="entry-content" <?php if($article_body) echo $article_body; ?>>
		<?php
		echo wpautop( $post->post_content );
		wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'organized-docs' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		) );
		?>
	</div><!-- .entry-content -->
	
	<?php /* begin Docs prev/next post navigation */
	$term_list = wp_get_post_terms($post->ID, 'isa_docs_category', array("fields" => "slugs"));
	// get_posts in same custom taxonomy

	// sort terms by chosen orderby
	$single_sort_by = get_option('od_single_sort_by');
	$orderby_order = get_option('od_single_sort_order');
			
	if ( 'date' == $single_sort_by ) {
		$orderby = 'date';
	} elseif ( 'title - alphabetical' == $single_sort_by ) {
		$orderby = 'title';
	} else {
		$orderby = 'meta_value_num';
	}
			
	$postlist_args = array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'isa_docs',
			'meta_key'			=> '_odocs_meta_sortorder_key',
			'orderby'			=> $orderby,
			'order'				=> $orderby_order,
			'isa_docs_category' => $term_list[0]
	); 
	$postlist = get_posts( $postlist_args );
				
	// get ids of posts retrieved from get_posts

	$ids = array();
	$titles = array();
	foreach ($postlist as $thepost) : setup_postdata( $thepost );
		$ids[] = $thepost->ID;
		$titles[] = $thepost->post_title;
	endforeach; 
	wp_reset_postdata();
			
	// get and echo previous and next post in the same taxonomy        
	$thisindex = array_search($post->ID, $ids);
	$previd = $ids[$thisindex-1];
	$nextid = $ids[$thisindex+1];

	$anchor_prev = get_option( 'od_title_on_nav_links' ) ? '&larr; ' . $titles[$thisindex-1] : __( '&larr; Previous', 'organized-docs' );
	$anchor_next = get_option( 'od_title_on_nav_links' ) ? $titles[$thisindex+1] . ' &rarr;' : __( 'Next &rarr;', 'organized-docs' ); ?>
	
	<nav class="navigation post-navigation" role="navigation"><h1 class="screen-reader-text"><?php _e( 'Post navigation', 'organized-docs' ); ?></h1><div class="nav-links">

	<?php if ( !empty($previd) ) { ?>
			<span class="meta-nav"><a rel="prev" href="<?php echo get_permalink($previd); ?>"><?php echo $anchor_prev; ?></a></span>
	<?php }
	if ( !empty($nextid) ) { ?>
		<span class="meta-nav"><a rel="next" href="<?php echo get_permalink($nextid); ?>"><?php echo $anchor_next; ?></a></span>
	<?php } ?>
	</div></nav>
</article><!-- #post-## -->
</div><!-- #content -->
<?php $sidebar = $Isa_Organized_Docs->get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>
</div><!-- #primary -->
<?php get_footer();