<?php

/**
 *
 * @link              https://eriktdesign.com
 * @since             1.0.0
 * @package           Navcv
 *
 * @wordpress-plugin
 * Plugin Name:       ETD Dynamic Shortcodes
 * Plugin URI:        https://navigators.org
 * Description:       Create and edit text replacement shortcodes.
 * Version:           1.0.0
 * Author:            Erik Teichmann
 * Author URI:        https://eriktdesign.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       etd
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ETD_DYNAMIC_SHORTCODES', '1.0.0' );

/**
 * The core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-etd-dynamic-shortcodes.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_etd_dynamic_shortcodes() {

	$plugin = new ETD_Dynamic_Shortcodes();

}
run_etd_dynamic_shortcodes();