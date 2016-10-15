<?php

/**
 * @package UT_shortcode
 * @version 0.1
 */
/*
Plugin Name: Polymer addon pack
Plugin URI: http://www.utrepo.com
Description: Designed for Polymer HK.
Author: uTorrent
Version: 0.1
Author URI: http://www.utrepo.com
*/

define( 'UTSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define ('UTSC_PLUGIN_URL', plugins_url( '', __FILE__ ) );

require_once UTSC_PLUGIN_DIR . 'mobile_detect.php';


function button_style_shortcode($atts, $content = null) {
	$a = shortcode_atts( array( 
			'title' => 'Submit' 
	), $atts );

	$content = 'Sample Page';

	return '<a href="' . 
		esc_url( get_permalink( get_page_by_title( $content ) ) ) . '">' . 
		esc_attr($a['title']) . '</span>';
}

function slider_shortcode($slidertype) {

}

function post_list ($atts) {
	extract( shortcode_atts( array(
		'category' => 'uncategorized', 
		'total' => 5,
		'layout' => 'hor',
		'title' => '',
		'color' => null,
		),
	$atts ));

	// available var: category, total
	$args = array(
		'category_name' => $category,
		'posts_per_page' => $total,
		'color' => $color,
		'layout' => $layout,

	);

	$post_array = get_posts( $args );

	// color schema
	$html_color = '';
	if ($color) $html_color = ' style="border-color:' . esc_html($color) . '"';

	$return_string = '';

	// define block class
	$block_class = 'post-list-block '; $nothumb_index = null;
	switch ($layout) {
		case 'feature':
			$block_class .= 'block-feature';
			break;
		default:
			$block_class .= 'block-horizontal';
			break;
	}

	// before html
	$return_string .= '<div class="' . $block_class . '"><ul' . $html_color . '>';

	// Generate title
	if ($layout === 'feature') {
		if ($title != '') $return_string .= '<div class="entry-title">' . esc_html($title) . '</div>';
	} else {
		if ($title != '') $return_string .= '<h1 class="page-title">' . esc_html($title) . '</h1>';
	}



	// content html
	foreach ($post_array as $index => $post) {
		$html_class = ( $index == 0 ) ? 'post-feature': 'post-child';

		$return_string .= '<li class="' . $html_class . '">';

		if (is_null($nothumb_index) or $nothumb_index >= $index) {

			if ( has_post_thumbnail($post -> ID) )
				// thumbnail size
				$thumbnail_size = ( $index == 0 ) ? array(400, 300) : array(75, 75) ;

				$return_string .= 
					'<div class="post-image">' . 
						'<a href="' . esc_url( get_permalink( $post ) ) . '">' . 
						get_the_post_thumbnail( $post -> ID, $thumbnail_size ) . '</a>' . 
					'</div>' ;
		}

		$return_string .= '<div class="post-description">';

		$return_string .= 
			'<h2 class="entry-title" itemprop="headline">'. 
			'<a href="' . esc_url( get_permalink( $post ) ) . '">' . $post -> post_title . '</a></h2>' ;

		$return_string .= 
			'<div class="entry-meta">' . 
				mysql2date('Y-m-d', $post->post_date ) . '&nbsp;' . 
				'by ' .	'<a href="' . get_author_posts_url($post -> post_author) . '">'. 
				get_the_author_meta('display_name', $post -> post_author) . '</a>' . 
			'</div>';

		// post content
		if ($layout === 'feature' and $index == 0) $return_string .= '<p class="entry-postcontent">' . 
			wp_trim_words($post->post_content, 60) .
			'</p>';

		$return_string .= ( $index == 0 ) ? '': '<div class="clearfix"></div>';

		$return_string .= '</div>';
		$return_string .= '</li>';
	}

	// after html
	$return_string .= '</ul></div>';


	wp_enqueue_style ( 'post-list-css',
			UTSC_PLUGIN_URL . '/libs/style.css' );

	return $return_string;
	
}

function tag_popular ($atts) {

}

function webfont_load() {
/*
	Loading webfont for non-mobile devices, 
	disable loading on admin page 
*/	
	$detect = new Mobile_Detect;

	if ( ! $detect->isMobile() && ! is_admin() )
		wp_enqueue_style ( 'noto-sans',
			UTSC_PLUGIN_URL . '/libs/webfont.css' );

}


add_shortcode( 'ubutton', 'button_style_shortcode' );
add_shortcode( 'post-list', 'post_list' );


add_action('init', 'webfont_load');