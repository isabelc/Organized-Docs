<?php

/**
 * Override Twenty Thirteen navigation for Docs
 * @todo remove this in 2016
 */
function twentythirteen_post_nav() {
	twentyfourteen_post_nav();
}

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
	if ( 'isa_docs' == get_post_type() ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'organized-docs' ); ?></h1>
		<div class="nav-links">
			<?php
			if ( is_attachment() ) {
				previous_post_link( '%link', __( '<span class="meta-nav">Published In</span>%title', 'organized-docs' ) );
			} else {
				previous_post_link( '%link', __( '<span class="meta-nav">Previous Post</span>%title', 'organized-docs' ));
				next_post_link( '%link', __( '<span class="meta-nav">Next Post</span>%title', 'organized-docs' ));
			}
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}