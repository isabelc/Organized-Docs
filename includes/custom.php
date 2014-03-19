<?php
/**
 * Override Twenty Fourteen navigation for Docs
 */
function twentyfourteen_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}

	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'organized-docs' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) {
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'organized-docs' ) );
			} elseif ( 'isa_docs' == get_post_type() ) {

				global $post;
				$term_list = wp_get_post_terms($post->ID, 'isa_docs_category', array("fields" => "slugs"));

				// get_posts in same custom taxonomy
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
				foreach ($postlist as $thepost) {
				   $ids[] = $thepost->ID;
				}
				
				// get and echo previous and next post in the same taxonomy        
				$thisindex = array_search($post->ID, $ids);
				$previd = $ids[$thisindex-1];
				$nextid = $ids[$thisindex+1];
	
				
				if ( !empty($previd) ) {
				   echo '<span class="meta-nav"><a rel="prev" href="' . get_permalink($previd). '">' . __( 'Previous', 'organized-docs' ) . '</a></span>';
				}
				if ( !empty($nextid) ) {
				   echo '<span class="meta-nav"><a rel="next" href="' . get_permalink($nextid). '">' . __( 'Next', 'organized-docs' ) . '</a></span>';
				}

			} else {
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Post</span>%title', 'organized-docs' ));
				next_post_link( '%link', __( '<span class="meta-nav">Next Post</span>%title', 'organized-docs' ));
			}
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}