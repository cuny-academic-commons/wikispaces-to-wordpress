<?php
/** CONFIG ***************************************************************/

$files_url     = '';
$source_dir    = __DIR__ . '/source/';
$post_type     = 'page';
$multisite_url = '';

$config = __DIR__ . '/config.php';
if ( file_exists( $config ) ) {
	require $config;
}

$output_dir = __DIR__ . '/output/';

/** END CONFIG ***********************************************************/


require __DIR__ . '/vendor/autoload.php';
use DiDom\Document;
use DiDom\Query;

$files = scandir( $source_dir );
$files = array_diff( $files, array( '..', '.' ) );

$txt = array();
$i = 0;

foreach ( $files as $file ) {
	// We only want files with the .html extension.
	if ( ! endsWith( $file, '.html' ) ) {
		continue;
	}

	$filename = $file;
	$file = file_get_contents( $source_dir . $file );

	// Parse the HTML file for only the wiki content and save contents to file.
	$dom = new Document( $file );
	$content = $dom->find( 'div.wiki' );

	file_put_contents( $output_dir . $i . '.txt', modifyContent( $content[0]->innerHtml(), $files_url ) );

	// Set the post title to the HTML filename.
	$txt[$i] = array(
		'title'   => str_replace( '.html', '', $filename ),
	);

	++$i;
}

$cmd = '';
foreach ( $txt as $j => $val ) {
	$cmd .= 'wp post create ./output/' . $j . '.txt --post_type=' . $post_type . ' --post_status=publish --post_title="' . $val['title'] . '"';

	if ( ! empty( $multisite_url ) ) {
		$cmd .= ' --url=' . $multisite_url;
	}

	$cmd .= "\n";
}

file_put_contents( $output_dir . 'cmd.txt', $cmd );

echo 'All done! Check /output/cmd.txt for the generated WP-CLI commands';

/** UTILITY **************************************************************/

/**
 * Determine if a string ends with a certain string.
 *
 * @param  string $haystack String to search.
 * @param  string $needl    String to see if it ends in $haystack.
 * @return bool
 */
function endsWith( $haystack, $needle ) {
	// search forward starting from end minus needle length characters
	return $needle === "" || ( ( $temp = strlen( $haystack ) - strlen( $needle ) ) >= 0 && strpos( $haystack, $needle, $temp ) !== false );
}

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

/**
 * Modifies the existing Wikispaces content to do the following:
 *
 * - Replace internal file URLs to new uploaded URLs
 * - Replace internal wiki links with 'data-wiki-link-title' attribute. This
 *   will replaced later on with a wp-cli script.
 *
 * @param string $content  Blog content.
 * @param string $filesurl URL root path for the new files.
 */
function modifyContent( $content, $filesurl ) {
	// Replace internal file links with new, uploaded URLs.
	if ( ! empty( $filesurl ) && false !== substr( $content, 'href="files/' ) ) {
		$content = str_replace( 'href="files/', 'href="' . $filesurl . 'files/', $content );
	}

	/*
	 * Search for internal wiki links and replace with "data-wiki-link-title"
	 * attribute temporarily.
	 *
	 * We will later replace the "data-wiki-link-title" attribute with the
	 * generated WordPress permalink later on with a wp-cli script.
	 */
	$wiki_link_refs = strposall( $content, 'class="wiki_link" href="' );
	if ( empty( $wiki_link_refs ) ) {
		return $content;
	}

	$search = $replace = array();
	foreach( $wiki_link_refs as $ref ) {
		$end_pos  = strpos( $content, '"', $ref + 24 + 1 );
		$link_ref = substr( $content, $ref + 24, $end_pos - ( $ref + 24 ) );

		// Skip anchors and external links.
		if ( 0 === strpos( $link_ref, 'http' ) || 0 === strpos( $link_ref, '#' ) ) {
		} else {
			$search[] = 'class="wiki_link" href="' . $link_ref . '"';
			$replace[] = 'data-wiki-link-title="' . str_replace( '.html', '', urldecode( $link_ref ) ) . '"';
		}
	}

	if ( ! empty( $search ) ) {
		$content = str_replace( $search, $replace, $content );
	}

	return $content;
}