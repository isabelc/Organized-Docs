<?php
/**
 * Plugin Name: Organized Docs
 * Plugin URI: http://isabelcastillo.com/docs/category/organized-docs-wordpress-plugin
 * Description: Easily create organized documentation for multiple products, organized by product, and by subsections within each product.
 * Version: 1.1.4
 * Author: Isabel Castillo
 * Author URI: http://isabelcastillo.com
 * License: GPL2
 * Text Domain: organized-docs
 * Domain Path: languages
 * 
 * Copyright 2013 - 2014 Isabel Castillo
 * 
 * This file is part of Organized Docs plugin.
 * 
 * Organized Docs plugin is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * Organized Docs plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Organized Docs; if not, If not, see <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>.
 */

if(!class_exists('Isa_Organized_Docs')) {
class Isa_Organized_Docs{
	public function __construct() {
			if( ! defined('ISA_ORGANIZED_DOCS_PATH')) {
				define( 'ISA_ORGANIZED_DOCS_PATH', plugin_dir_path(__FILE__) );
			}
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_filter( 'plugin_action_links', array( $this, 'support_link' ), 2, 2 );
			add_action( 'init', array( $this, 'setup_docs_taxonomy'), 0 );
			add_action( 'init', array( $this, 'create_docs_cpt') );
			add_action( 'init', array( $this, 'create_docs_menu_item') );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );
			add_action( 'wp_head', array( $this, 'version' ) );
 			add_filter( 'the_title', array( $this, 'suppress_docs_title' ), 40, 2 );
			add_filter( 'the_content', array( $this, 'single_doc_content_filter' ) ); 
			add_action( 'widgets_init', array( $this, 'register_widgets') );
			add_filter( 'sidebars_widgets', array( $this, 'sidebar_switch' ) );
			add_filter( 'template_include', array( $this, 'docs_template' ) );
			add_action( 'wp_loaded', array( $this, 'sidebar' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_filter( 'parse_query', array( $this, 'sort_asc' ) );
			add_filter( 'manage_edit-isa_docs_columns', array( $this, 'manage_edit_docs_columns') );
			add_action( 'manage_isa_docs_posts_custom_column', array( $this, 'manage_docs_columns' ), 10, 2 );

			add_action( 'isa_docs_category_add_form_fields', array( $this, 'odocs_taxonomy_new_meta_field'), 10, 2 );
			add_action( 'isa_docs_category_edit_form_fields', array( $this, 'odocs_taxonomy_edit_meta_field'), 10, 2 );
			add_action( 'edited_isa_docs_category', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
			add_action( 'create_isa_docs_category', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );

    }

	/** 
	* Only upon plugin activation, flush rewrite rules for custom post types.
	*
	* @since 1.0
	*/
	public static function activate() { 

		self::setup_docs_taxonomy();
		self::create_docs_cpt();
		// Clear the permalinks
		flush_rewrite_rules();
	}

	/** 
	* Upon plugin deactivation, flush rewrite rules
	*
	* @since 1.0
	*/
	public static function deactivate() { 
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/** 
	* display support link on plugin page
	*
	* @since 1.0
	* @return void
	*/
	public function support_link($actions, $file) {
	$od_path    = plugin_basename(__FILE__);
	if(false !== strpos($file, $od_path))
	 $actions['settings'] = '<a href="http://isabelcastillo.com/docs/category/organized-docs-wordpress-plugin" target="_blank">'. __( 'Setup Instructions', 'organized-docs' ) . '</a>';

	return $actions; 
	}
	/** 
	* Load plugin's textdomain
	*
	* @since 1.0
	* @return void
	*/

	public function load_textdomain() {
		load_plugin_textdomain( 'organized-docs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	/** 
	* Store plugin name and version as options
	*
	* @since 1.0
	* @return void
	*/
	public function admin_init(){
		$plugin_data = get_plugin_data( __FILE__, false );
		update_option( 'isa_organized_docs_plugin_version', $plugin_data['Version'] );
		update_option( 'isa_organized_docs_plugin_name', $plugin_data['Name'] );
	}

	/** 
	* Add meta generator tag with plugin name and version to head
	*
	* @since 1.0
	* @return string meta element name=generator
	*/
	public function version(){
		echo '<meta name="generator" content="' . get_option( 'isa_organized_docs_plugin_name' ) . ' ' . get_option( 'isa_organized_docs_plugin_version' ) . '" />' . "\n";
	}

	/** 
	 * Add isa_docs CPT.
	 * @since 1.0
	 */
	
	public function create_docs_cpt() {

			    	$args = array(
			        	'label' => __( 'Docs','organized-docs' ),
			        	'singular_label' => __('Doc','organized-docs'),
			        	'public' => true,
			        	'show_ui' => true,
			        	'capability_type' => 'post',
			        	'hierarchical' => false,
			        	'rewrite' => array(
								'slug' => __( 'docs', 'organized-docs' ),
								'with_front' => false,
	
						),
			        	'exclude_from_search' => false,
		        		'labels' => array(
							'name' => __( 'Docs','organized-docs' ),
							'singular_name' => __( 'Doc','organized-docs' ),
							'add_new' => __( 'Add New','organized-docs' ),
							'add_new_item' => __( 'Add New Doc','organized-docs' ),
							'all_items' => __( 'All Docs','organized-docs' ),
							'edit' => __( 'Edit','organized-docs' ),
							'edit_item' => __( 'Edit Doc','organized-docs' ),
							'new_item' => __( 'New Doc','organized-docs' ),
							'view' => __( 'View Doc','organized-docs' ),
							'view_item' => __( 'View Doc','organized-docs' ),
							'search_items' => __( 'Search Docs','organized-docs' ),
							'not_found' => __( 'No docs found','organized-docs' ),
							'not_found_in_trash' => __( 'No docs found in Trash','organized-docs' ),
							'parent' => __( 'Parent Docs','organized-docs' ),
						),
			        	'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
					'has_archive' => true,
					'menu_icon'=> 'dashicons-book',
			        );
	
		    	register_post_type( 'isa_docs' , $args );
	
	} // end create_docs_cpt

	/** 
	 * Add stylesheet
	 * @since 1.0
	 */
	public function enqueue() {
		wp_enqueue_style( 'organized-docs', plugins_url( 'includes/organized-docs.css' , __FILE__ ) );
		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
	}

	/**
	 * adds Docs menu item to wp_menu_nav
	 * @since 1.0
	 */
	
	function docs_menu_link($items, $args) {
		$newitems = $items;
		$newitems .= '<li class="docs"><a title="'. __('Docs', 'organized-docs') . '" href="'. get_post_type_archive_link( 'isa_docs' ) .'">' . __( apply_filters( 'organized_docs_menu_label', 'Docs' ), 'organized-docs' ) . '</a></li>';
		return $newitems;
	}

	/** 
	 * Adds Docs menu item to wp_page_menu.
	 * @since 1.0
	 */
	public function docs_page_menu_link( $menu ) {
		$newmenu = $menu;
		$newitems = '<li class="docs"><a title="'. __('Docs', 'organized-docs') . '" href="'. get_post_type_archive_link( 'isa_docs' ) .'">'. __( apply_filters( 'organized_docs_menu_label', 'Docs' ), 'organized-docs' ) . '</a></li>';
	    $newmenu = str_replace( '</ul></div>', $newitems . '</ul></div>', $newmenu );
	    return $newmenu;
	}

	/**
	 * Allow the creation of the docs menu item
	 * @since 1.0
	 */
	
	public function create_docs_menu_item() {
		add_filter('wp_nav_menu_items', array( $this, 'docs_menu_link' ), 10, 2);
		add_filter( 'wp_page_menu', array( $this, 'docs_page_menu_link' ), 95 );
	}
	
	/**
	 * Remove default title on single Docs, to add it later below the docs menu
	 *
	 * @uses is_single()
	 * @uses get_post_type()
	 * @uses in_the_loop()
	 * @since 1.0
	 */
	function suppress_docs_title( $title, $id ) {

	    if ( is_single() && ( 'isa_docs' == get_post_type() ) && in_the_loop() ) {
	        return '';
	    }
	    return $title;
	}

	/**
	 * Add isa-docs-item-title and submenu to content for isa_docs custom post type Single 
	 *
	 * @uses is_single()
	 * @uses get_post_type()
	 * @since 1.0
	 */
	public function single_doc_content_filter( $content ) {

	    if ( is_single() && ( 'isa_docs' == get_post_type() ) ) {
			global $post;
			$docscontent = $this->organized_docs_section_heading();
			$docscontent .= $this->organized_docs_content_nav();
			$docscontent .= '<p id="odd-print-button"><i class="fa fa-print"></i> <a href="javascript:window.print()" class="button">Print</a></p>';
 			$docscontent .= '<h1 class="entry-title">' . single_post_title('', false) . '</h1>';
			$docscontent .= $content;
			return $docscontent;
		} else {
			return $content;
		}
	}

	/**
	 * Make docs archive use our docs template
	 * @since 1.0
	 */
	public function docs_template( $template ) {
		if( is_tax( 'isa_docs_category' ) || is_post_type_archive( 'isa_docs' ) ) {
			if ( file_exists( get_stylesheet_directory() . '/taxonomy-isa_docs_category.php' ) ) {
				return get_stylesheet_directory() . '/taxonomy-isa_docs_category.php';
			} else {
				return plugin_dir_path( __FILE__ ) . 'includes/docs-template.php';
			}
		}
	    return $template;

	} // end docs_template()

	/**
	 * Returns the top category item as a heading for docs. Fetched one way from docs main archive, a different way from taxonomy 'isa_docs_download', and fetched yet a different way from single.
	 * @since 1.0
	 */
	public function organized_docs_section_heading() {

		global $post, $data;

		if ( is_tax( 'isa_docs_category' ) ) {
		
			// get top level parent term on custom taxonomy archive
			$heading = '<h2 id="isa-docs-item-title" class="entry-title">';
			$taxonomy = get_query_var( 'taxonomy' );
		    $queried_object = get_queried_object();
		    $curr_term_id =  (int) $queried_object->term_id;

			$top_level_parent_term_id = $this->isa_term_top_parent_id( $curr_term_id );
			$top_term = get_term( $top_level_parent_term_id, 'isa_docs_category' );
		
			$top_term_link = get_term_link( $top_term );
			$top_term_name = $top_term->name;
		
			$heading .= '<a href="' . $top_term_link  . '" title="' . esc_attr( $top_term_name ) . '">' . $top_term_name . '</a>';
			$heading .= '</h2>';



		} elseif ( is_post_type_archive( 'isa_docs' ) ) { 
			$heading = apply_filters( 'od_docs_main_title', '<h1 id="isa-docs-main-title" class="entry-title">Docs</h1>' );

		} elseif ( is_single() ) {

			// get top level parent term on single

			$doc_categories = wp_get_object_terms( $post->ID, 'isa_docs_category' );
			$first_cat = $doc_categories[0]; // first category
			$curr_term_id = $first_cat->term_id;
			$top_level_parent_term_id = $this->isa_term_top_parent_id( $curr_term_id );
		
			$top_term = get_term( $top_level_parent_term_id, 'isa_docs_category' );
		
			$top_term_link = get_term_link( $top_term );
			$top_term_name = $top_term->name;
		
			$heading = '<h2 id="isa-docs-item-title">';
			$heading .= '<a href="' . $top_term_link  . '" title="' . esc_attr( $top_term_name ) . '">' . $top_term_name . '</a>';
			$heading .= '</h2>';			
		
		}
	
		return $heading;

	} // end organized_docs_section_title()


	/**
	 * Returns dynamic menu for Docs. Lists only subterms of top level parent of current term, on Docs taxonomy archive and on Docs single, but nothing on the main Docs parent archive.  
	 * @since 1.0
	 */
	public function organized_docs_content_nav() { 

		if ( is_post_type_archive( 'isa_docs' ) ) 
			return;

		$docs_menu = '<div id="organized-docs-menu" class="navbar nav-menu"><ul>';

		if ( is_tax( 'isa_docs_category' ) ) {
		
			// need regular current term id, only used to compare w/ top level term id later
			$taxonomy = get_query_var( 'taxonomy' );
		    $queried_object = get_queried_object();
		    $curr_term_id =  (int) $queried_object->term_id;
			$top_level_parent_term_id = $this->isa_term_top_parent_id( $curr_term_id );
		

		} else { // if is single, get top level term id on single
				global $post;
				$doc_categories = wp_get_object_terms( $post->ID, 'isa_docs_category' );
				$first_cat = $doc_categories[0]; // first category
				$curr_term_id = $first_cat->term_id;
				// need regular current cat id, only used to compare w/ top level cat id
				$top_level_parent_term_id = $this->isa_term_top_parent_id( $curr_term_id );
		}
				
		/** make sure current term id is integer. will be compared to child cats in menu below */
		$curr_term_id_as_int = (int)$curr_term_id;
		
		/** proceed with getting children of top level term, not simply children of current term, unless, of course, the current term is a top level parent **/
		
		// get term children
					
		$termchildren =  get_term_children( $top_level_parent_term_id, 'isa_docs_category' );
	
		foreach ( $termchildren as $child ) { 

				$termobject = get_term_by( 'id', $child, 'isa_docs_category' );

		        $docs_menu .= '<li class="menu-item';
		
				// if current term id matches an id of a child term in menu, then give it active class
		
				if ( $termobject->term_id == $curr_term_id_as_int ) {
								
					$docs_menu .= ' active-docs-item current_page_item';
						
			} // if no match, proceed without class
		
			$docs_menu .= '"><a href="' . get_term_link( $termobject ) . '" title="' . esc_attr( $termobject->name ) . '">' . $termobject->name . '</a></li>';
		
		}		
		
		$docs_menu .= '</ul></div>';

		return $docs_menu;
	
	} // end organized_docs_content_nav()

	/** 
	* Returns ID of top-level parent term of the passed term, or returns the passed term if it is a top-level term.
	*
	* @param    string      $termid      Term ID to be checked
	* @return   string      $termParent  ID of top-level parent term
	* @since 1.0
	*/
	public function isa_term_top_parent_id( $termid ) {
		$termParent = '';
		while ($termid) {
			$term = get_term( $termid, 'isa_docs_category' );

			$termid = $term->parent; // assign parent ID (if exists) to $termid
			  // the while loop will continue whilst there is a $termid
			  // when there is no longer a parent $termid will be NULL so we can assign our $termParent
			$termParent = $term->term_id;
		}
		return $termParent;
	}
	
	/**
	 * register widget
	 * @since 1.0
	 */
	public function register_widgets() {
	
		include ISA_ORGANIZED_DOCS_PATH . 'includes/widget.php';
		register_widget('DocsSectionContents');
	
	} // end register_widget

	/**
	 * register sidebar for docs
	 * @since 1.0
	 */
	function sidebar(){
		register_sidebar(array(
	        'id' => 'isa_organized_docs',
	        'name' => __( 'Docs Widget Area', 'organized-docs' ),
	        'description' => __( 'Sidebar for single Organized Docs article', 'organized-docs' ),
	        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	        'after_widget' => '</li>',
	        'before_title' => '<h3 class="widgettitle">',
	        'after_title' => '</h3>'
	    ));
	}

	/** 
	* Switch out default sidebar for our custom Docs sidebar, only on single Docs. Exclude sidebar-1 for Twenty Thirteen theme to avoid having footer show a duplicate Docs widget.
	* @todo Rather than exclude sidebar-1, may possibly have to exclude some custom widget id for custom themes
	* @uses is_single()
	* @uses get_post_type()
	* @since 1.0
	* @return void
	*/

	public function sidebar_switch($widgets) {

		$theme = wp_get_theme();

	    if ( is_single() &&
			( 'isa_docs' == get_post_type() ) &&
			isset( $widgets['isa_organized_docs'] )
		 ) {

			$keys_array = $this->list_sidebar_ids();
			foreach( $keys_array as $key ) {
			    if( isset( $widgets[$key] ) ) {

					// only do if Twenty Thirteen is not active while key=sidebar-1
					if ( 
(
						( ( 'Twenty Thirteen' == $theme->name ) || ( 'Twenty Thirteen' == $theme->parent_theme ) || ( 'Twenty Fourteen' == $theme->name ) || ( 'Twenty Fourteen' == $theme->parent_theme ) ) &&
						( 'sidebar-1' == $key )
) ||

(
						( ( 'Twenty Fourteen' == $theme->name ) || ( 'Twenty Fourteen' == $theme->parent_theme ) ) &&
						( 'sidebar-3' == $key )
)


					   ) {
							continue;
						}


					$widgets[$key] = $widgets['isa_organized_docs'];

				} // end if( isset( $widgets[$key] )
			}// end foreach( $keys_array

		    return $widgets;
		} else {
			// not on singe Docs, get regular sidebar
		    return $widgets;
		}
	}

	/**
	 * get all current registered sidebars
	 *
	 * @since 1.0
	 * @return array
	 */
	
	public function get_all_sidebars(){
			
			global $wp_registered_sidebars;		
			$allsidebars = $wp_registered_sidebars;
			ksort($allsidebars);
			$themesidebars = array();
			foreach( $allsidebars as $key => $sb ){
					$themesidebars[$key] = $sb;
			}
			
			return $themesidebars;
	}

	/**
	* gets all current registered sidebar ids
	*
	* @since 1.0
	* @return array
	*/
	public function list_sidebar_ids(){
	
		$allsidebars_array = $this->get_all_sidebars();
		$theme_sidebar_ids = array();
		foreach( $allsidebars_array as $key => $values ){
	
			// exclude 'isa_organized_docs', grab the id's of the rest
			if ( $key != 'isa_organized_docs' ) {
				// add this id to array
				$theme_sidebar_ids[] = $key;
			}
		}
	
		return $theme_sidebar_ids;
	}

	/**
	 * Registers the custom taxonomies for the docs custom post type
	 *
	 * @since 1.0
	 * @return void
	 */
	public function setup_docs_taxonomy() {
		$category_labels = array(
			'name' => __( 'Categories', 'organized-docs' ),
			'singular_name' =>__( 'Category', 'organized-docs' ),
			'search_items' => __( 'Search Categories', 'organized-docs' ),
			'all_items' => __( 'All Categories', 'organized-docs' ),
			'parent_item' => __( 'Parent Category', 'organized-docs' ),
			'parent_item_colon' => __( 'Parent Category:', 'organized-docs' ),
			'edit_item' => __( 'Edit Category', 'organized-docs' ),
			'update_item' => __( 'Update Category', 'organized-docs' ),
			'add_new_item' => __( 'Add New Category', 'organized-docs' ),
			'new_item_name' => __( 'New Category Name', 'organized-docs' ),
			'menu_name' => __( 'Categories', 'organized-docs' ),
		);
		
		$category_args = apply_filters( 'isa_docs_category_args', array(
			'hierarchical'		=> true,
			'labels'			=>	apply_filters('isa_docs_category_labels', $category_labels),
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'			=> array(
										'slug'		=> 'docs/category', 
										'with_front'	=> false,
										'hierarchical'	=> true ),
		)
		);
		register_taxonomy( 'isa_docs_category', array('isa_docs'), $category_args );
		register_taxonomy_for_object_type( 'isa_docs_category', 'isa_docs' );
	}

	public function sort_asc($query) {
		if( is_tax( 'isa_docs_category' ) ) {
		    $query->query_vars['order'] = 'ASC';
	    }
    }

	/**
	 * Add Parent column to Docs admin
	 * @since 1.0
	 */
	public function manage_edit_docs_columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Name', 'organized-docs' ),
			'taxonomy-isa_docs_category'	=> __( 'Categories', 'organized-docs' ),
			'parentcat' => __('Parent', 'organized-docs'),
			'comments' => '<div class="comment-grey-bubble" title="Comments"></div>',
			'date' => __('Date', 'organized-docs')
		);
	
		return $columns;
	}
 
	/** 
	 * Add data to Parent column in Docs admin
	 * @since 1.0
	 */
	public function manage_docs_columns( $column, $post_id ) {
		global $post;
		switch( $column ) {
			case 'parentcat' :
				// get the parent cat
				$doc_categories = wp_get_object_terms( $post_id, 'isa_docs_category' );

				/* @test */
				if(!empty($doc_categories)){
					if(!is_wp_error( $doc_categories )){
			

						$first_cat = $doc_categories[0]; // first category
		
						$curr_term_id = $first_cat->term_id;
						$top_level_parent_term_id = $this->isa_term_top_parent_id( $curr_term_id );
							
						$top_term = get_term( $top_level_parent_term_id, 'isa_docs_category' );
					
						$top_term_slug = $top_term->slug;
						$top_term_name = $top_term->name;
		
						$path = 'edit.php?post_type=isa_docs&isa_docs_category=' . $top_term_slug;
						$top_term_sort_link = admin_url( $path );
		
						echo '<a href="' . $top_term_sort_link  . '" title="' . esc_attr( $top_term_name ) . '">' . $top_term_name . '</a>';
					}
				} // @test end
			
				break;
			default :
				break;
		}
	}


/* @test begin */

	/** 
	 * To the "Add New Category" page for Docs categories, add a field for sort order.
	 * @since 1.1.5
	 */

	public function odocs_taxonomy_new_meta_field() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[subheading_sort_order]"><?php _e( 'Sort Order Number for Sub-heading', 'organized-docs' ); ?></label>
			<input type="text" name="term_meta[subheading_sort_order]" id="term_meta[subheading_sort_order]" value="">
			<p class="description"><?php _e( 'If this is a Sub-heading, give this Sub-heading a number to order it under its Parent. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new docs later without having to change all current numbers. <em>Leave blank if this is is not a sub heading.</em>', 'organized-docs' ); ?></p>
		</div>
	<?php
	}

	/** 
	 * To the "Edit Category" page for Docs categories, add the sort order field and populate any saved value for it.
	 * @since 1.1.5
	 */
	public function odocs_taxonomy_edit_meta_field($term) {
 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); 

		$value = isset($term_meta['subheading_sort_order']) ? esc_attr( $term_meta['subheading_sort_order'] ) : '';

?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[subheading_sort_order]"><?php _e( 'Sort Order Number', 'organized-docs' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[subheading_sort_order]" id="term_meta[subheading_sort_order]" value="<?php echo $value; ?>">
				<p class="description"><?php _e( 'If this is a Sub-heading, give this Sub-heading a number to order it under its Parent. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new docs later without having to change all current numbers. <em>Leave blank if this is is not a sub heading.</em>','organized-docs' ); ?></p>
			</td>
		</tr>
	<?php
	}

	/** 
	 * Save sort order fields callback function.
	 * @since 1.1.5
	 */
	public function save_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_option( "taxonomy_$t_id" );
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			// Save the option array.
			update_option( "taxonomy_$t_id", $term_meta );
		}
	}  



	/**
	 * Sort terms by a custom term_meta key
	 * 
	 * @param array $term_ids to sort
	 * @param int|string $term_meta_key key that holds our custom term_meta value
	 * @return array
	 * @since 1.1.5
	 */
	public function sort_terms( $term_ids, $term_meta_key ) {

		$ordered_terms = array();
		$new_order_numbers = array();
		$unordered_terms = array();
		$no_order = array();

		// get sort order numbers for all term ids
		foreach ( $term_ids as $term_id ) {

			if ( $taxonomy_sort = get_option( "taxonomy_$term_id" ) ) {
	
				$sort_value = isset($taxonomy_sort[$term_meta_key]) ? esc_attr( $taxonomy_sort[$term_meta_key] ) : '';
	
				if ( ! empty($sort_value) )  {
					// have sort order
					$ordered_terms[] = $term_id;
					$new_order_numbers[] = ( int ) $sort_value;

				}
	
	
			} else {
				// This catches any terms that don't have subheading_sort_order set
				$unordered_terms[] = $term_id;
				$no_order[] = 99999999; // need this so i have an equal number of keys and values for later
			}
		}
		
		// Only sort by subheading_sort_order if there are items to sort, otherwise return the original array
		if ( count( $ordered_terms ) > 0 ) {


			// if we have any unordered, add them to the end of list
			if ( count( $unordered_terms ) > 0 ) {

				// build keys list, adding unordered terms to the end of list




// @test replace				$comma_term_list = implode(",", $unordered_terms);
// @test replace				array_push( $ordered_terms, $comma_term_list );


/************************************************************

***********  instead of using the 2 lines above, do this instead. push 1 at at time:

*/




			// @test add each unordered term to the end of list of terms


				foreach ( $unordered_terms as $unordered_term ) {

					array_push( $ordered_terms, $unordered_term );


				}

			// @test END. @TODO note to isa: i have not run this yet. MUST @TODO THIS WITH THE NEXT SECTION .


				
	
				// build values list, adding unordered terms to the end of list

				$comma_order_list = implode(",", $unordered_terms);
				array_push( $new_order_numbers, $comma_order_list );

			}

			// combine term ids with their order numbers.
			$new_ordered_terms = array_combine($ordered_terms, $new_order_numbers);

			// sort by value of order number ASC
			asort($new_ordered_terms);

			return $new_ordered_terms;

		} else {

			// no items to sort to return the original array
			return $term_ids;
		}

	}


}
}
$Isa_Organized_Docs = new Isa_Organized_Docs();
register_deactivation_hook(__FILE__, array('Isa_Organized_Docs', 'deactivate')); 
register_activation_hook(__FILE__, array('Isa_Organized_Docs', 'activate'));