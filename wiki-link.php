<?php
/**
 * WP-CLI script that searches for "data-wiki-link-title" attributes and adds
 * the proper WordPress permalink after a Wikispaces conversion.
 */

/**
 * Wrapper function for strpos() to return all positions of a certain string.
 *
 * @param  string $haystack Same usage as strpos().
 * @param  string $needle   Same usage as strpos().
 * @param  int    $offset   Same usage as strpos().
 * @param  array  $retval   Array containing all positions.
 * @return array
 */
function strposall( $haystack = '', $needle = '', $offset = 0, $retval = array() ) {
	if ( false !== strpos( $haystack, $needle, $offset ) ) {
		$pos = strpos( $haystack, $needle, $offset );
		$retval[] = $pos;
		$pos = $pos + 1;

		if ( false !== strpos( $haystack, $needle, $pos ) ) {
			$pos = strpos( $haystack, $needle, $pos );
			$retval[] = $pos;
			$pos = $pos + 1;
			$retval = strposall( $haystack, $needle, $pos, $retval );
		}
	}
	return $retval;
}

$q = new WP_Query( array(
	's'                      => 'data-wiki-link-title',
	'post_type'              => 'page',
	'nopaging'               => true,
	'update_post_term_cache' => false,
	'no_found_rows'          => true,
	'orderby'                => 'none'
) );

if ( empty( $q->posts ) ) {
	echo 'No conversion needed!';
	return;
}

foreach ( $q->posts as $post ) {
	$wiki_link_refs = strposall( $post->post_content, 'data-wiki-link-title="' );
	if ( empty( $wiki_link_refs ) ) {
		continue;
	}

	$search = $replace = array();
	foreach( $wiki_link_refs as $ref ) {
		$end_pos  = strpos( $post->post_content, '"', $ref + 22 + 1 );
		$title = substr( $post->post_content, $ref + 22, $end_pos - ( $ref + 22 ) );

		$page = get_page_by_title( $title );
		if ( ! empty( $page->post_title ) ) {
			$search[]  = 'data-wiki-link-title="' . $title . '"';
			$replace[] = 'href="' . get_permalink( $page->ID ) . '"';
		}
	}

	if ( ! empty( $search ) ) {
		$content = str_replace( $search, $replace, $post->post_content );

		wp_update_post( array(
			'ID'           => $post->ID,
			'post_content' => $content
		) );
	}
}

echo 'All done!';