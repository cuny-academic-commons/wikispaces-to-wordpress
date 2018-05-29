# Wikispaces to WordPress
[Wikispaces](https://wikispaces.com) is a wiki-hosting platform that is shutting down in late 2018.

For those wanting to migrate to WordPress, this is a set of command-line tools to convert a Wikispaces export over into a WordPress site.

**Note:** This only converts HTML files over to WordPress pages.  This does not import any Wikispaces discussions, events or anything else.

### Prerequisites

- Knowledge of the command-line
- [WP-CLI](https://wp-cli.org)
- [Composer](https://getcomposer.org/)
- [Wikispaces export in HTML format](http://helpcenter.wikispaces.com/customer/portal/articles/2920537-classroom-and-free-wikis)

### How to use?

#### Installation

1. Clone this repo or download and extract the contents somewhere.
2. In your command-line prompt, navigate to the location where you extracted the contents and run `composer install` to install our dependencies.
3. Open your Wikispaces HTML export ZIP file and extract all HTML files into the `source` directory.  For example, `source/Test.html` would be a valid example.  Your HTML files should not reside in subdirectories.

#### Configuration

4. Rename `config-sample.php` to `config.php` and open the file to view what you can configure for the converter script.  Please spend a few minutes going over these options.

```
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
```
5. Once you are satisfied, run our converter script in the command-line with `php converter.php` 
6. This will create a bunch of text files in the `output` directory.

#### WP-CLI

7. Copy the `output` directory to the root of your WordPress install.
8. Open the `output/cmd.txt` file.  This should have a list of WP-CLI commands that you'll use to import the Wikispaces content into your WordPress site.  Copy the entire contents of that file.
9. In your command-line prompt, ensure you are in the root of your WordPress install.  Next, paste the contents into your command-line prompt.  This should start the process of importing each post into your WordPress site.
10. Lastly, you'll want to make sure that internal links to your Wikispaces content will use WordPress permalinks.<br><br>To do this, copy the `wiki-link.php` file from this repository to the root of your WordPress install and run the following WP-CLI command:<br>```wp eval-file wiki-link.php```<br><br>**Note:** If you imported the Wikispaces content into a specific site on WP multisite install, you'll want to append the `--url=https://subsite.example.com` parameter to let WP-CLI know which site to use. (Change `subsite.example.com` to whatever site you are using.)

Congrats! You've now converted your Wikispaces content into your new WordPress site!

### Thanks

- [DiDom](https://github.com/Imangazaliev/DiDOM) - A PHP script for parsing HTML files
