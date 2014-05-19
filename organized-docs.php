<?php
/**
 * Plugin Name: Organized Docs
 * Plugin URI: http://isabelcastillo.com/docs/category/organized-docs-wordpress-plugin
 * Description: Easily create organized documentation for multiple products, organized by product, and by subsections within each product.
 * Version: 2.0-rc-1.1
 * Author: Isabel Castillo
 * Author URI: http://isabelcastillo.com
 * License: GPL2
 * Text Domain: organized-docs
 * Domain Path: languages
 * 
 * Copyright 2013 - 2014 Isabel Castillo
 * 
 * This file is part of Organized Docs.
 * 
 * Organized Docs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Organized Docs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Organized Docs. If not, see <http://www.gnu.org/licenses/>.
 */
if(!class_exists('Isa_Organized_Docs')) {
class Isa_Organized_Docs{
	public function __construct() {
			if( ! defined('ISA_ORGANIZED_DOCS_PATH')) {
				define( 'ISA_ORGANIZED_DOCS_PATH', plugin_dir_path(__FILE__) );
			}
			add_action( 'admin_init', array( $this, 'settings_api_init' ) );
			add_filter( 'plugin_action_links', array( $this, 'support_link' ), 2, 2 );
			add_action( 'init', array( $this, 'setup_docs_taxonomy'), 0 );
			add_action( 'init', array( $this, 'create_docs_cpt') );
			add_action( 'init', array( $this, 'create_docs_menu_item') );
			add_action( 'init', array( $this, 'update_docs_sort_order_post_meta' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue') );
			add_action( 'widgets_init', array( $this, 'register_widgets') );
			add_filter( 'template_include', array( $this, 'docs_template' ) );
			add_action( 'wp_loaded', array( $this, 'sidebar' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_filter( 'parse_query', array( $this, 'sort_single_docs' ) );
			add_filter( 'manage_edit-isa_docs_columns', array( $this, 'manage_edit_docs_columns') );
			add_action( 'manage_isa_docs_posts_custom_column', array( $this, 'manage_docs_columns' ), 10, 2 );
			add_action( 'isa_docs_category_add_form_fields', array( $this, 'odocs_taxonomy_new_meta_field'), 10, 2 );
			add_action( 'isa_docs_category_edit_form_fields', array( $this, 'odocs_taxonomy_edit_meta_field'), 10, 2 );
			add_action( 'edited_isa_docs_category', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
			add_action( 'create_isa_docs_category', array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
			add_action( 'add_meta_boxes', array( $this, 'add_sort_order_box' ) );
			add_action( 'save_post', array( $this, 'save_postdata' ) );
			add_action('admin_menu', array( $this, 'submenu_page' ) );
			add_action('wp_head', array( $this, 'dynamic_css' ) );
			add_filter( 'the_posts', array( $this, 'close_comments' ) );
    }

	/** 
	* Only upon plugin activation, flush rewrite rules for custom post types.
	*/
	public static function activate() { 

		self::setup_docs_taxonomy();
		self::create_docs_cpt();
		flush_rewrite_rules();
	}

	/** 
	* Upon plugin deactivation, flush rewrite rules
	*/
	public static function deactivate() { 
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/** 
	* display support link on plugin page
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
	* @return void
	*/

	public function load_textdomain() {
		load_plugin_textdomain( 'organized-docs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/** 
	 * Add isa_docs CPT.
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
/* translators: URL slug */
							'slug' => _x( 'docs', 'URL slug', 'organized-docs' ),
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
	 */
	public function enqueue() {
		wp_enqueue_style( 'organized-docs', plugins_url( 'includes/organized-docs.css' , __FILE__ ) );
		
		// @todo enqueue in footer, also don't load if option disabled
		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
	}

	/**
	 * adds Docs menu item to wp_menu_nav
	 */
	
	function docs_menu_link($items, $args) {
		$newitems = $items;
		$newitems .= '<li class="docs"><a title="'. __('Docs', 'organized-docs') . '" href="'. get_post_type_archive_link( 'isa_docs' ) .'">' . __( apply_filters( 'organized_docs_menu_label', 'Docs' ), 'organized-docs' ) . '</a></li>';
		return $newitems;
	}

	/** 
	 * Adds Docs menu item to wp_page_menu.
	 */
	public function docs_page_menu_link( $menu ) {
		$newmenu = $menu;
		$newitems = '<li class="docs"><a title="'. __('Docs', 'organized-docs') . '" href="'. get_post_type_archive_link( 'isa_docs' ) .'">'. __( apply_filters( 'organized_docs_menu_label', 'Docs' ), 'organized-docs' ) . '</a></li>';
	    $newmenu = str_replace( '</ul></div>', $newitems . '</ul></div>', $newmenu );
	    return $newmenu;
	}

	/**
	 * Allow the creation of the docs menu item
	 */
	
	public function create_docs_menu_item() {
		add_filter('wp_nav_menu_items', array( $this, 'docs_menu_link' ), 10, 2);
		add_filter( 'wp_page_menu', array( $this, 'docs_page_menu_link' ), 95 );
	}
	/**
	 * Get the custom template if is set
	 * @since 1.2.3
	 */
	 
	function get_template_hierarchy( $template ) {
	 
		$template_slug = rtrim( $template, '.php' );
		$template = $template_slug . '.php';
	 
		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( 'organized-docs/' . $template ) ) {
			$file = $theme_file;
		} else {
			$file = dirname( __FILE__ ) . '/includes/templates/' . $template;
		}
		return $file;
	}
	
	/**
	 * Returns template file
	 */
	public function docs_template( $template ) {
		if ( is_tax( 'isa_docs_category' ) ) {
			return $this->get_template_hierarchy( 'taxonomy' );
		} elseif ( is_post_type_archive( 'isa_docs' ) ) {
			return $this->get_template_hierarchy( 'archive' );
		} elseif (is_singular('isa_docs')) {
			return $this->get_template_hierarchy( 'single' );
		} else {
			return $template;		
		}
	}

	/**
	 * Returns the top category item as a heading for docs. Fetched one way from docs main archive, a different way from taxonomy 'isa_docs_download', and fetched yet a different way from single.
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
		
		// get term children and sort them by custom sort oder
		$termchildren =  get_term_children( $top_level_parent_term_id, 'isa_docs_category' );
		$sorted_termchildren = $this->sort_terms( $termchildren, 'subheading_sort_order' );
	
		foreach ( $sorted_termchildren as $sorted_termchild_id => $sorted_termchild_order ) { 

				$termobject = get_term_by( 'id', $sorted_termchild_id, 'isa_docs_category' );
				$docs_menu .= '<li class="menu-item';
		
				// if current term id matches an id of a child term in menu, then give it active class
				if ( $termobject->term_id == $curr_term_id_as_int ) {
					$docs_menu .= ' active-docs-item current_page_item';
				}
				$docs_menu .= '"><a href="' . get_term_link( $termobject ) . '" title="' . esc_attr( $termobject->name ) . '">' . $termobject->name . '</a></li>';
		
		}		
		
		$docs_menu .= '</ul></div>';

		return $docs_menu;
	
	} // end organized_docs_content_nav()

	/** 
	* Returns ID of top-level parent term of the passed term, or returns the passed term if it is a top-level term.
	* @param    string      $termid      Term ID to be checked
	* @return   string      $termParent  ID of top-level parent term
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
	 */
	public function register_widgets() {
	
		include ISA_ORGANIZED_DOCS_PATH . 'includes/widget.php';
		register_widget('DocsSectionContents');
	
	}

	/**
	 * register sidebar for docs
	 */
	public function sidebar(){
		register_sidebar(array(
	        'id' => 'isa_organized_docs',
	        'name' => __( 'Docs Widget Area', 'organized-docs' ),
	        'description' => __( 'Sidebar for single Organized Docs article', 'organized-docs' ),
	        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	        'after_widget' => '</li>',
	        'before_title' => '<h3 class="widget-title">',
	        'after_title' => '</h3>'
	    ));
	}

	/**
	 * Registers the custom taxonomies for the docs custom post type
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
			'labels'			=> apply_filters('isa_docs_category_labels', $category_labels),
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'			=> array(
/* translators: URL slug */
						'slug' => _x( 'docs/category', 'URL slug', 'organized-docs' ),
						'with_front'	=> false,
						'hierarchical'	=> true ),
		)
		);
		register_taxonomy( 'isa_docs_category', array('isa_docs'), $category_args );
		register_taxonomy_for_object_type( 'isa_docs_category', 'isa_docs' );
	}

	/**
	 * Add Parent column to Docs admin
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
	 */
	public function manage_docs_columns( $column, $post_id ) {
		global $post;
		switch( $column ) {
			case 'parentcat' :
				// get the parent cat
				$doc_categories = wp_get_object_terms( $post_id, 'isa_docs_category' );

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
				}
				break;
			default :
				break;
		}
	}

	/** 
	 * To the "Add New Category" page for Docs categories, add sort order fields.
	 * @since 1.1.5
	 */

	public function odocs_taxonomy_new_meta_field() {
		// this will add the custom meta field to the add new term page
		?>
		<div class="form-field">
			<label for="term_meta[main_doc_item_sort_order]"><?php _e( 'Sort Order Number for a Top-level Item', 'organized-docs' ); ?></label>
			<input type="text" name="term_meta[main_doc_item_sort_order]" id="term_meta[main_doc_item_sort_order]" value="">
			<p class="description"><?php _e( 'If this is a Top-level Item (a main Docs Item, i.e. the item that has docs), give this item a number to order it on the main Docs page. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new Top-level Items later without having to change all current numbers.', 'organized-docs' ) . ' <em>' . _e( 'Leave blank if this is is not a Top-level Item.', 'organized-docs' ) . '</em>'; ?></p>
		</div>
		<div class="form-field">
			<label for="term_meta[subheading_sort_order]"><?php _e( 'Sort Order Number for Sub-heading', 'organized-docs' ); ?></label>
			<input type="text" name="term_meta[subheading_sort_order]" id="term_meta[subheading_sort_order]" value="">
			<p class="description"><?php _e( 'If this is a Sub-heading, give this Sub-heading a number to order it under its Parent. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new sub-headings later without having to change all current numbers.', 'organized-docs' ) . ' <em>' . _e( 'Leave blank if this is is not a sub-heading.', 'organized-docs' ) . '</em>'; ?></p>
		</div>
	<?php
	}

	/** 
	 * To the "Edit Category" page for Docs categories, add sort order fields and populate any saved values for them.
	 * @since 1.1.5
	 */
	public function odocs_taxonomy_edit_meta_field($term) {
 
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		// retrieve the existing value(s) for this meta field. This returns an array
		$term_meta = get_option( "taxonomy_$t_id" ); 

		$value_sub = isset($term_meta['subheading_sort_order']) ? esc_attr( $term_meta['subheading_sort_order'] ) : '';
		$value_main = isset($term_meta['main_doc_item_sort_order']) ? esc_attr( $term_meta['main_doc_item_sort_order'] ) : '';

?>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[main_doc_item_sort_order]"><?php _e( 'Sort Order Number for a Top-level Item', 'organized-docs' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[main_doc_item_sort_order]" id="term_meta[main_doc_item_sort_order]" value="<?php echo $value_main; ?>">
				<p class="description"><?php _e( 'If this is a Top-level Item (a main Docs Item, i.e. the item that has docs), give this item a number to order it on the main Docs page. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new Top-level Items later without having to change all current numbers.', 'organized-docs' ) . ' <em>' . _e( 'Leave blank if this is is not a Top-level Item.', 'organized-docs' ) . '</em>'; ?></p>
			</td>
		</tr>
		<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[subheading_sort_order]"><?php _e( 'Sort Order Number for Sub-heading', 'organized-docs' ); ?></label></th>
			<td>
				<input type="text" name="term_meta[subheading_sort_order]" id="term_meta[subheading_sort_order]" value="<?php echo $value_sub; ?>">
				<p class="description"><?php _e( 'If this is a Sub-heading, give this Sub-heading a number to order it under its Parent. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new sub-headings later without having to change all current numbers.', 'organized-docs' ) . ' <em>'. _e( 'Leave blank if this is is not a sub-heading.', 'organized-docs' ) . '</em>'; ?></p>
			</td>
		</tr>
	<?php
	}

	/** 
	 * Save taxonomy sort order fields callback function.
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
		$no_order_numbers = array();

		// get sort order numbers for all term ids
		foreach ( $term_ids as $term_id ) {

			$sort_value = '';
			if ( $taxonomy_sort = get_option( "taxonomy_$term_id" ) ) {
				// get sort value
				$sort_value = isset($taxonomy_sort[$term_meta_key]) ? esc_attr( $taxonomy_sort[$term_meta_key] ) : '';
			}

			if ( ! empty($sort_value) )  {
				// has sort order
				$ordered_terms[] = $term_id;
				$new_order_numbers[] = ( int ) $sort_value;
			} else {
				// sort value is empty
				$unordered_terms[] = $term_id;
				$no_order_numbers[] = 99999999; // need this in order to have equal count of keys and values for later
			}
		}
		
		// Only sort by sort order if there are items to sort, otherwise return the original array
		if ( count( $ordered_terms ) > 0 ) {

			// if we have any unordered, add them to the end of list
			if ( count( $unordered_terms ) > 0 ) {

				// build keys list, adding unordered terms to the end of list
				foreach ( $unordered_terms as $unordered_term ) {
					array_push( $ordered_terms, $unordered_term );
				}

				// build values list, adding unordered term values to the end of list
				foreach ( $no_order_numbers as $no_order_number ) {
					array_push( $new_order_numbers, $no_order_number );
				}

			}

			// combine term ids with their order numbers.
			$new_ordered_terms = array_combine($ordered_terms, $new_order_numbers);

			// sort by value of order number ASC
			asort($new_ordered_terms);
			return $new_ordered_terms;

		} else {

			// No items to sort, so return the original array
			// but add the id as the key since we'll need id as key later.
			$keys = $term_ids;
			$values = $term_ids;
			if( $keys && $values ) {
				return array_combine($keys, $values);
			}
		}

	}

	/**
	 * Adds a sort-order meta box to the Docs edit screen.
	 * @since 1.1.5
	 */
	public function add_sort_order_box() {
		add_meta_box(
			'odocs_sectionid',
			__( 'Sort Order', 'organized-docs' ),
			array( $this, 'odocs_sort_oder_box' ),
			'isa_docs',
			'side',
			'core'
		);
	}

	/**
	 * Prints the sort-order meta box content.
	 * @param WP_Post $post The object for the current post/page.
	 * @since 1.1.5
	 */
	public function odocs_sort_oder_box( $post ) {
	
		wp_nonce_field( 'odocs_sort_oder_box', 'odocs_sort_oder_box_nonce' );
	
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, '_odocs_meta_sortorder_key', true );
			
		echo '<label for="odocs_single_sort_order">';
		       _e( "Give this Doc a number to order it under its Sub-heading. Number 1 will appear first, while greater numbers appear lower. Numbers do not have to be consecutive; for example, you could number them like, 10, 20, 35, 45, etc. This would leave room in between to insert new Docs later without having to change all current numbers.", 'organized-docs' );
		echo '</label> ';
		echo '<input type="text" id="odocs_single_sort_order" name="odocs_single_sort_order" value="' . esc_attr( $value ) . '" size="25" />';
	
	}

	/**
	 * When the post is saved, saves our custom data.
	 * @param int $post_id The ID of the post being saved.
	 * @since 1.1.5
	 */
	public function save_postdata( $post_id ) {
	
		/*
		* We need to verify this came from the our screen and with proper authorization,
		* because save_post can be triggered at other times.
		*/
	
		if ( ! isset( $_POST['odocs_sort_oder_box_nonce'] ) )
			return $post_id;
		$nonce = $_POST['odocs_sort_oder_box_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'odocs_sort_oder_box' ) )
			return $post_id;
	
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		
		// Check the user's permissions.
		if ( 'isa_docs' == $_POST['post_type'] ) {
			
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
			  
		} else {
			
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
			
		// OK, its safe for us to save the data now.
		$odocs_data = sanitize_text_field( $_POST['odocs_single_sort_order'] );
		$odocs_sortorder = empty($odocs_data) ? 999999 : $odocs_data;
		update_post_meta( $post_id, '_odocs_meta_sortorder_key', $odocs_sortorder );
	}
	/**
	 * Sort Single Docs on the regular archive page by custom chosen order
	 * @uses is_tax()
	 * @since 1.1.5
	 */
	public function sort_single_docs($query) {
		if( is_tax('isa_docs_category') && $query->is_main_query() && isset( $query->query_vars['meta_key'] ) ) {

			// orderby custom option
			$single_sort_by = get_option('od_single_sort_by');
			$single_sort_by_order = get_option('od_single_sort_by_order');// @todo make option
				
			if ( 'date' == $single_sort_by ) {
				$orderby = 'date';
			} elseif ( 'title - alphabetical' == $single_sort_by ) {
				$orderby = 'title';
			} else {
				$orderby = 'meta_value_num';
			}
			
			if ( 'descending' == single_sort_by_order ) {
				$orderby_order = 'DESC';
			} else {
				$orderby_order = 'ASC';
			}
			
			$query->query_vars['orderby'] = $orderby;
			$query->query_vars['meta_key'] = '_odocs_meta_sortorder_key';
			$query->query_vars['order'] = $orderby_order;
		}
		return $query;
	}


	/**
	 * Add submenu page
	 * @since 1.1.9
	 */

	public function submenu_page() {
		add_submenu_page( 'edit.php?post_type=isa_docs', __( 'Organized Docs Settings', 'organized-docs' ), __('Settings', 'organized-docs'), 'manage_options', 'organized-docs-settings', array( $this, 'settings_page_callback' ) ); 
	}

	/**
	 * HTML output for the submenu page
	 * @since 1.1.9
	 */
	public function settings_page_callback() {
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>' . __( 'Organized Docs Settings', 'organized-docs' ) . '</h2>'; ?>
			<form method="POST" action="options.php">
			<?php
			settings_fields( 'organized-docs-settings' );
			do_settings_sections( 'organized-docs-settings' );
			submit_button(); ?>
			</form>
		<?php echo '</div>';
	}

	/**
	 * Add the settings
	 * @since 1.1.9
	 */
	public function settings_api_init() {
	 	
	 	add_settings_section(
			'od_main_setting_section',
			__( 'Main Settings', 'organized-docs' ),
			array( $this, 'main_setting_section_callback' ),
			'organized-docs-settings'
		);

	 	add_settings_section(
			'od_single_post_setting_section',
			__( 'Single Post Settings', 'organized-docs' ),
			array( $this, 'single_setting_section_callback' ),
			'organized-docs-settings'
		);

	 	add_settings_section(
			'od_uninstall_setting_section',
			__( 'Uninstall Settings', 'organized-docs' ),
			array( $this, 'uninstall_setting_section_callback' ),
			'organized-docs-settings'
		);
	 	add_settings_field(
			'od_hide_printer_icon',
			__( 'Remove Printer Icon', 'organized-docs' ),
			array( $this, 'hide_printer_icon_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_hide_printer_icon' );
	 	add_settings_field(
			'od_hide_print_link',
			__( 'Remove Print Link', 'organized-docs' ),
			array( $this, 'hide_print_link_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_hide_print_link' );
	 	add_settings_field(
			'od_title_on_nav_links',
			__( 'Title on nav links?', 'organized-docs' ),
			array( $this, 'title_nav_links_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_title_on_nav_links' );
	 	add_settings_field(
			'od_enable_manage_comments',
			__( 'Enable Comments Control', 'organized-docs' ),
			array( $this, 'enable_manage_comments_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_enable_manage_comments' );


	 	add_settings_field(
			'od_close_comments',
			__( 'Disable Comments on Single Docs?', 'organized-docs' ),
			array( $this, 'close_comments_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_close_comments' );

	 	add_settings_field( // @todo @new
			'od_single_sort_by',
			__( 'Sort Single Docs By ...', 'organized-docs' ),
			array( $this, 'single_sort_by_setting_callback' ),
			'organized-docs-settings',
			'od_single_post_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_single_sort_by' );


	 	add_settings_field(
			'od_delete_data_on_uninstall',
			__( 'Remove Data on Uninstall?', 'organized-docs' ),
			array( $this, 'delete_data_setting_callback' ),
			'organized-docs-settings',
			'od_uninstall_setting_section'
		);
	 	register_setting( 'organized-docs-settings', 'od_delete_data_on_uninstall' );

	}

	/**
	 * Main Settings section callback
	 * @since 1.2.0
	 */
	public function main_setting_section_callback() {
		echo '<p>' . __('This section is pending future options.', 'organized-docs') . '</p>';
	}

	/**
	 * Single Docs Posts Settings section callback
	 * @since 1.2.2
	 */
	public function single_setting_section_callback() {
		echo '<p>' . __('These settings are for the single Docs posts.', 'organized-docs') . '</p>';
	}

	/**
	 * Uninstall Settings section callback
	 * @since 1.1.9
	 */
	public function uninstall_setting_section_callback() {
 		echo '<p>' . __('This setting refers to when you uninstall (delete) the plugin. This does not refer to simply deactivating the plugin.', 'organized-docs') . '</p>';
	}

	/**
	 * Callback function for setting to hide printer icon
	 * @since 1.2.0
	 */
	public function hide_printer_icon_setting_callback() {
		echo '<label for="od_hide_printer_icon"><input name="od_hide_printer_icon" id="od_hide_printer_icon" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'od_hide_printer_icon' ), false ) . ' /> ' . __( 'Check this box to remove only the printer icon from single Docs, but leave the Print link.', 'organized-docs' ) . '</label>';
	}

	/**
	 * Callback function for setting to hide print link
	 * @since 1.2.0
	 */
	public function hide_print_link_setting_callback() {
		echo '<label for="od_hide_print_link"><input name="od_hide_print_link" id="od_hide_print_link" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'od_hide_print_link' ), false ) . ' /> ' . __( 'Check this box to remove the Print link altogether from single Docs.', 'organized-docs' ) . '</label>';
	}

	/**
	 * Callback function for setting to show title on nav links
	 * @since 1.2.2
	 */
	public function title_nav_links_setting_callback() {
		echo '<label for="od_title_on_nav_links"><input name="od_title_on_nav_links" id="od_title_on_nav_links" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'od_title_on_nav_links' ), false ) . ' /> ' . __( 'Check this box to show the post titles instead of "Previous" and "Next" on the nav links on the bottom of single Docs.', 'organized-docs' ) . '</label>';
	}

	/**
	 * Callback function for setting to enable comments control
	 * @since 1.2.2
	 */
	public function enable_manage_comments_setting_callback() {
		echo '<label for="od_enable_manage_comments"><input name="od_enable_manage_comments" id="od_enable_manage_comments" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'od_enable_manage_comments' ), false ) . ' /> ' . __( 'Check this box to turn on the ability to use the next option. This is here because managing comments is heavy. You should turn this off after you see that your next option has taken effect.', 'organized-docs' ) . '</label>';

	}

	/**
	 * Callback function for setting to disable comments
	 * @since 1.2.2
	 */
	public function close_comments_setting_callback() {

		$html = '<input type="radio" id="od_close_comments_false" name="od_close_comments" value="1"' . checked( 1, get_option( 'od_close_comments' ), false ) . '/>';
		$html .= '<label for="od_close_comments_false">No</label><br /><br/ >';
		$html .= '<input type="radio" id="od_close_comments_true" name="od_close_comments" value="2"' . checked( 2, get_option( 'od_close_comments' ), false ) . '/>';
		$html .= '<label for="od_close_comments_true">Yes</label>';
		echo $html;
	}

	/**
	 * Callback function for setting to sort single docs
	 * @since 1.2.3
	 */
	public function single_sort_by_setting_callback() {
		$selected_option = get_option('od_single_sort_by');
		$items = array("custom sort order number", "title - alphabetical", "date");
		echo "<select id = 'od_single_sort_by' name = 'od_single_sort_by'>";

		foreach($items as $item) {
			$selected = ( $selected_option == $item ) ? ' selected = "selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}

		echo "</select>";
		echo '<p class="description">Choose how to sort single docs.</p>';
	}

	/**
	 * Callback function for setting to remove data on uninstall
	 * @since 1.1.9
	 */
	public function delete_data_setting_callback() {
		echo '<label for="od_delete_data_on_uninstall"><input name="od_delete_data_on_uninstall" id="od_delete_data_on_uninstall" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'od_delete_data_on_uninstall' ), false ) . ' /> ' . __( 'Check this box if you would like Organized Docs to completely remove all of its data when the plugin is deleted. This would include all Docs posts, Docs categories, subheadings, and sort order numbers.', 'organized-docs' ) . '</label>';
	}

	/**
	 * Dynamic CSS based on settings and themes
	 * @since 1.2.1
	 */
	public function dynamic_css() {
		$theme = wp_get_theme();
		echo '<style>';
		if( ( 'Twenty Fourteen' == $theme->name ) || ( 'Twenty Fourteen' == $theme->parent_theme ) ) {
			echo 'body.single-isa_docs .entry-content {max-width: 100%;margin-right: 0px;}';
		}
		if( ( 'Twenty Twelve' == $theme->name ) || ( 'Twenty Twelve' == $theme->parent_theme ) ) {
			echo '.nav-single {display:none;}';
		}
		echo '</style>';
	}

	/**
	 * Close comments on Docs
	 * @since 1.2.2
	 */
	public function close_comments( $posts ) {
		if ( !is_single() || ! get_option( 'od_enable_manage_comments' ) ) {
			return $posts;
		}
		if ( 'isa_docs' == get_post_type($posts[0]->ID) ) {

			if ( get_option( 'od_close_comments' ) == 2 ) {
				$status = 'closed';
			} else {
				$status = 'open';
			}

			$posts[0]->comment_status = $status;
			$posts[0]->ping_status    = $status;
			wp_update_post( $posts[0] );
		}
		return $posts;
	}

	/**
	 * For backwards compatibility, give all single Docs posts a default sort-order number of 99999
	 * @since 1.1.8
	 * @todo remove this back compatibility in version 2.1
	 */
	public function update_docs_sort_order_post_meta() {
		global $post;
		// Run this update only once
		if (	get_option( 'odocs_update_sortorder_postmeta' ) != 'completed' ) {
			$args = array(	'post_type' => 'isa_docs', 
				'posts_per_page' => -1,
			);
			$all_docs = get_posts( $args );

			foreach ($all_docs as $doc) {
				$sort_order_value_check = get_post_meta( $doc->ID, '_odocs_meta_sortorder_key', true );

				// if sort order value is empty, assign a default value
				if( empty( $sort_order_value_check ) ) {
					update_post_meta($doc->ID, '_odocs_meta_sortorder_key', 99999);
				}
			}
			wp_reset_postdata();

			// for cleanup, remove these options
			delete_option( 'isa_organized_docs_plugin_version' );
			delete_option( 'isa_organized_docs_plugin_name' );
			delete_option( 'odocs_update_custom_tax_terms_meta' );
			delete_option( 'odocs_bugfix_update_term_meta' );
			delete_option( 'odocs_update_sortorder_meta' );
			delete_option(	'odocs_update_sortorder_post_meta' );

			update_option( 'odocs_update_sortorder_postmeta', 'completed' );
		}
	}
}
}
$Isa_Organized_Docs = new Isa_Organized_Docs();
register_deactivation_hook(__FILE__, array('Isa_Organized_Docs', 'deactivate')); 
register_activation_hook(__FILE__, array('Isa_Organized_Docs', 'activate'));
