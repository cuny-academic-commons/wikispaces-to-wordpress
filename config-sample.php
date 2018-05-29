<?php
/**
 * Configuration.
 *
 * Uncomment any variable to use it.
 */

/*
 * This is the base URL filepath where your new exported files reside.
 *
 * If your Wikispaces HTML ZIP export contains a 'files' subdirectory and you
 * set this variable to example.com, then you should upload all items from the
 * 'files' subdirectory to http://example.com/files/.
 *
 * Once this is set, the script will change all Wikispaces file links to the
 * new URL.
 */
//$files_url = 'https://example.com/';

/*
 * If you want to import your Wikispaces content into an existing WordPress
 * network site, set this variable.
 *
 * This is used to tell WP-CLI which site to import the contents into.
 *
 * It's best to create a new sub-site instead of using an existing one.
 */
//$multisite_url = 'https://subsite.example.com';

/*
 * The WordPress post type you want to use for the Wikispaces content.
 *
 * Defaults to 'page'.
 */
//$post_type = 'page';