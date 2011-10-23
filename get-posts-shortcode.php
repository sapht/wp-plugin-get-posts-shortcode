<?php
/*
Plugin Name: Get posts shortcode
Description: Usage: [getposts category=hejsan]
Version: 0
Author: Daniel Hjerth
*/

function only_new_articles ( $where = '' ) {
	$where .= " AND post_date > '2010-10-29 00:00:00'";
	return $where;
}

function with_date ( $q ) {
	global $wpdb;
	$q .= ', DATE_FORMAT(' . $wpdb->db . '.post_date, "%M %d, %Y") as mydate';
	return $q;
}

// add_action('pre_get_posts', );

function shortcode_getposts ( $attributes, $content ) { 
	if ( isset($attributes['_mydate']) ) {
		$with_date = true;
		add_filter( "posts_fields_request", 'with_date');
		unset ( $attributes['_mydate'] );
	}

	$p = query_posts ( $attributes );

	if ( $with_date ) {
		remove_filter ("posts_fields_request", 'with_date' );
	}

	if ( !empty ( $p ) ) {
		$r = sprintf ( '<ul class="post-list%s">',
			( isset ( $attributes['category_name'] ) ? 
				" {$attributes['category_name']}" :
				'' 
			));

		foreach ( $p as $v ) {
			$r .= sprintf ('<li>%s<a href="%s">%s</a><br />%s</li>',
				( isset ( $with_date ) ? "<span class='mydate'>{$v->mydate}<br /></span>" : '' ),
				$v->post_name,
				$v->post_title,
				( strlen ($v->post_excerpt) ? $v->post_excerpt : find_first_paragraph($v->post_content) )
			);
		}
		$r .= '</ul>';
	}

	return isset ( $r ) ? $r : '<!-- get_posts: no posts found -->';
}

function find_first_paragraph( $text ) {
	return substr ( $text, 0, strpos($text, '.') + 1 );
}

add_shortcode('get_posts', 'shortcode_getposts');
?>
