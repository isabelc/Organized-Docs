<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

$option_name = 'odocs_update_sortorder_meta';

$terms = get_terms( 'isa_docs_category' );

$args = array(	'post_type' => 'isa_docs', 
			'posts_per_page' => -1,
);
$all_docs = get_posts( $args );

// For Single site
if ( !is_multisite() ) {
	delete_option( $option_name );

	foreach ( $terms as $single_term_object ) {
		$t_id = $single_term_object->term_id;
		delete_option( "taxonomy_$t_id" );
	}
	
	foreach ($all_docs as $doc) {
		wp_delete_post($doc->ID, true);
	}
} 
// For Multisite
else {
	// For regular options.
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		delete_option( $option_name );

		foreach ( $terms as $single_term_object ) {
			$t_id = $single_term_object->term_id;
			delete_option( "taxonomy_$t_id" );
		}
	
		foreach ($all_docs as $doc) {
			wp_delete_post($doc->ID, true);
		}
	}
	switch_to_blog( $original_blog_id );

	// For site options.
	delete_site_option( $option_name );  
}