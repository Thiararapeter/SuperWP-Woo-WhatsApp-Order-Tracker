<?php
/**
 * SuperWP Woo WhatsApp Order Tracker
 *
 * @package       SUPERWPWOT
 * @author        Thiarara SuperWP
 * @license       gplv2-or-later
 * @version       1.0.02
 *
 * @wordpress-plugin
 * Plugin Name:   SuperWP Woo WhatsApp Order Tracker
 * Plugin URI:    https://github.com/Thiararapeter/SuperWP-Woo-WhatsApp-Order-Tracker
 * Description:   WooCommerce WhatsApp Order Tracking Form with admin settings, color sliders, and multiple shortcodes.
 * Version:       1.0.04
 * Author:        Thiarara SuperWP
 * Author URI:    https://profiles.wordpress.org/thiarara/
 * Text Domain:   superwp-woo-whatsapp-order-tracker
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with SuperWP Woo WhatsApp Order Tracker. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function SUPERWPWOT() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define( 'SUPERWPWOT_NAME',			'SuperWP Woo WhatsApp Order Tracker' );

// Plugin version
define( 'SUPERWPWOT_VERSION',		'1.0.02' );

// Plugin Root File
define( 'SUPERWPWOT_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'SUPERWPWOT_PLUGIN_BASE',	plugin_basename( SUPERWPWOT_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'SUPERWPWOT_PLUGIN_DIR',	plugin_dir_path( SUPERWPWOT_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'SUPERWPWOT_PLUGIN_URL',	plugin_dir_url( SUPERWPWOT_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once SUPERWPWOT_PLUGIN_DIR . 'core/class-superwp-woo-whatsapp-order-tracker.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Thiarara SuperWP
 * @since   1.0.02
 * @return  object|Superwp_Woo_Whatsapp_Order_Tracker
 */
function SUPERWPWOT() {
	return Superwp_Woo_Whatsapp_Order_Tracker::instance();
}

require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Thiararapeter/SuperWP-Woo-WhatsApp-Order-Tracker',
	__FILE__,
	'SuperWP Woo WhatsApp Order Tracker'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

SUPERWPWOT();
