<?php
/**
 * The Docs Sidebar for Single Docs
 * @package	Organized Docs
 * @version 2.1.1
 * @since 2.0
 */
?>
<div id="docs-content-sidebar" class="docs-content-sidebar widget-area" role="complementary"><ul>
<?php if( ! dynamic_sidebar( 'isa_organized_docs' ) ) {
		the_widget('DocsSectionContents', array('title' => 'Table of Contents'), array(
			'before_widget' => '<li id="docs_section_contents-1" class="widget widget_docs_section_contents">',
	        'after_widget' => '</li>',
	        'before_title' => '<h3 class="widget-title">',
	        'after_title' => '</h3>'
		));
} ?>
</ul></div>