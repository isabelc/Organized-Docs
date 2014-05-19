<?php
/**
 * The template for displaying Organized Docs Single posts.
 * @package	Organized Docs
 * @since 1.2.3
 */
get_header(); 
global $Isa_Organized_Docs; ?>
<div id="primary">
<div id="content" role="main">
<!-- @test this is new templates/single.php -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php echo $Isa_Organized_Docs->organized_docs_section_heading();
	echo $Isa_Organized_Docs->organized_docs_content_nav();
	if ( ! get_option('od_hide_print_link') ) { ?>
		
		<p id="odd-print-button">
		
		<?php if ( ! get_option('od_hide_printer_icon') ) { ?>
			<i class="fa fa-print"></i>
		<?php } ?>
		
		<a href="javascript:window.print()" class="button"><?php _e( 'Print', 'organized-docs' ); ?></a>
		</p>

	<?php } ?>
	
	<header class="entry-header">
	<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	
	<?php /* begin Docs prev/next post navigation */
	$term_list = wp_get_post_terms($post->ID, 'isa_docs_category', array("fields" => "slugs"));
	// get_posts in same custom taxonomy
	// @todo sort terms by chosen order, whether date, alphabetical, or sort number.
	$postlist_args = array(
			'posts_per_page'		=> -1,
			'orderby'				=> 'meta_value_num',
			'meta_key'			=> '_odocs_meta_sortorder_key',
			'order'				=> 'ASC',
			'post_type'			=> 'isa_docs',
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

	$anchor_prev = get_option( 'od_title_on_nav_links' ) ? $titles[$thisindex-1] : __( 'Previous', 'organized-docs' );
	$anchor_next = get_option( 'od_title_on_nav_links' ) ? $titles[$thisindex+1] : __( 'Next', 'organized-docs' ); ?>
	
	<nav class="navigation post-navigation" role="navigation"><h1 class="screen-reader-text"><?php _e( 'Post navigation', 'organized-docs' ); ?></h1><div class="nav-links">

	<?php if ( !empty($previd) ) { ?>
			<span class="meta-nav"><a rel="prev" href="<?php echo get_permalink($previd); ?>"><?php echo $anchor_prev; ?></a></span>
	<?php }
	if ( !empty($nextid) ) { ?>
		<span class="meta-nav"><a rel="next" href="<?php echo get_permalink($nextid); ?>"><?php echo $anchor_next; ?></a></span>
	<?php } ?>
	</div></nav>
	<!-- end nav -->

</article><!-- #post-## -->
</div><!-- #content -->
<?php // @test
$sidebar = $Isa_Organized_Docs->get_template_hierarchy( 'sidebar' );
include_once $sidebar; ?>

</div><!-- #primary -->
<?php get_footer();