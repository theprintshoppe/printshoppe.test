<?php
/*
Plugin Name: wp2print
Description: wp2print brings full Web-to-Print functions to Wordpress/WooCommerce
Version: 3.1.27
Author: Print Science
Bitbucket Plugin URI: printsciencewp2print/wp2print
WC tested up to: 3.4.0
*/

if (!defined('ABSPATH')) exit;

if (!session_id()) { @session_start(); }

// settings
$siteurl = get_option('siteurl');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
	$siteurl = str_replace('http:', 'https:', $siteurl);
}

ini_set('max_execution_time', '7200');
ini_set('memory_limit', '512M');
ini_set('post_max_size', '1024M');
ini_set('max_input_vars', '5000');

define('PRINT_PRODUCTS_PLUGIN_DIR', dirname(__FILE__));
define('PRINT_PRODUCTS_PLUGIN_URL', $siteurl.'/wp-content/plugins/wp2print/');
define('PRINT_PRODUCTS_TEMPLATES_DIR', PRINT_PRODUCTS_PLUGIN_DIR . '/templates/');
define('PRINT_PRODUCTS_API_SERVER_URL', 'http://license.printscience.net');
define('PRINT_PRODUCTS_API_SECRET_KEY', 'ZKu9g9cKmNZhXuY5Jy6M');

if (class_exists('WooCommerce')) {
	define('IS_WOOCOMMERCE', true);
} else {
	define('IS_WOOCOMMERCE', false);
}

$designer_installed = false;
$print_products_settings = get_option('print_products_settings');
$print_products_plugin_options = get_option('print_products_plugin_options');
$print_products_plugin_aec = get_option('print_products_plugin_aec');
$print_products_file_upload_target = get_option("print_products_file_upload_target");
$print_products_amazon_s3_settings = get_option("print_products_amazon_s3_settings");

include(PRINT_PRODUCTS_PLUGIN_DIR . '/wp2print-functions.php');

include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-settings.php');

if (a12() && IS_WOOCOMMERCE) {
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-woo.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-product-classes.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-checkout-process.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-price-matrix-types.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-price-matrix-values.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-price-matrix-sku.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-price-matrix-settings.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-sort-order.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-orders-missing-files.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-users-groups.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-attribute-fields.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-simple-submit.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-polylang.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-sso.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-orders-proof.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-wwof.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-email-quote.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-vendor.php');
	include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-create-order.php');
	if (print_products_is_allow_aec()) {
		include(PRINT_PRODUCTS_PLUGIN_DIR . '/inc/wp2print-aec-orders.php');
	}
}

// plugin translation
add_action('plugins_loaded', 'print_products_load_textdomain');
function print_products_load_textdomain() {
	load_plugin_textdomain('wp2print', false, trailingslashit( WP_LANG_DIR ) . 'plugins/');
	load_plugin_textdomain('wp2print', false, plugin_basename( PRINT_PRODUCTS_PLUGIN_DIR ) . '/languages'); 
	if (is_dir(trailingslashit( WP_LANG_DIR ) . 'loco/plugins')) {
		load_plugin_textdomain('wp2print', false, trailingslashit( WP_LANG_DIR ) . 'loco/plugins'); 
	}
}

// install plugin data
register_activation_hook(__FILE__, 'print_products_install');
function print_products_install() {
	global $wpdb;
	$wp2print_data = get_plugin_data(__FILE__);
	include(dirname(__FILE__).'/wp2print-install.php');
}

// front-end part
add_action('wp_head', 'print_products_header');
function print_products_header() {
	if (a12()) {
		echo '<link href="' . PRINT_PRODUCTS_PLUGIN_URL . 'css/wp2print.css" rel="stylesheet" type="text/css" />' . "\n";
	}
}

add_action('wp_footer', 'print_products_footer');
function print_products_footer() {
	if (a12()) {
		echo '<link href="' . PRINT_PRODUCTS_PLUGIN_URL . 'css/colorbox.css" rel="stylesheet" type="text/css" />' . "\n";
		echo '<script type="text/javascript">var wp2print_siteurl = "'.home_url('/').'";</script>' . "\n";
		echo '<script type="text/javascript" src="' . PRINT_PRODUCTS_PLUGIN_URL . 'js/jquery.colorbox.min.js"></script>' . "\n";
		echo '<script type="text/javascript" src="' . PRINT_PRODUCTS_PLUGIN_URL . 'js/wp2print.js"></script>' . "\n";
	}
}

add_action('wp_enqueue_scripts', 'print_products_enqueue_scripts');
function print_products_enqueue_scripts() {
	global $woocommerce;
	if (a12() && IS_WOOCOMMERCE) {
		wp_enqueue_script('prettyPhoto', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js', array('jquery'), $woocommerce->version, true);
		wp_enqueue_script('prettyPhoto-init', $woocommerce->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init.min.js', array('jquery'), $woocommerce->version, true);
		wp_enqueue_style('woocommerce_prettyPhoto_css', $woocommerce->plugin_url() . '/assets/css/prettyPhoto.css');
	}
}

// admin part
add_action('admin_menu', 'print_products_admin_menu');
function print_products_admin_menu() {
	add_options_page(__('wp2print', 'wp2print'), __('wp2print', 'wp2print'), 'manage_options', 'print-products-settings', 'print_products_settings');
	if (a12() && IS_WOOCOMMERCE) {
		add_submenu_page('edit.php?post_type=product', __('Price Matrix Options', 'wp2print'), __('Price Matrix Options', 'wp2print'), 'manage_options', 'print-products-price-matrix-options', 'print_products_price_matrix_types');
		add_submenu_page('edit.php?post_type=product', __('Price Matrix Values', 'wp2print'), __('Price Matrix Values', 'wp2print'), 'manage_options', 'print-products-price-matrix-values', 'print_products_price_matrix_values');
		add_submenu_page('edit.php?post_type=product', __('Printing SKU Matrix', 'wp2print'), __('Printing SKU Matrix', 'wp2print'), 'manage_options', 'print-products-price-matrix-sku', 'print_products_price_matrix_sku');
		add_submenu_page('edit.php?post_type=product', __('Attributes Options', 'wp2print'), __('Attributes Options', 'wp2print'), 'manage_options', 'print-products-attributes-options', 'print_products_attributes_options');
		add_submenu_page('edit.php?post_type=product', __('Attribute sort order', 'wp2print'), __('Attribute sort order', 'wp2print'), 'manage_options', 'print-products-sort-order', 'print_products_sort_order_page');
	}
}

add_action('admin_print_scripts', 'print_products_admin_print_scripts');
function print_products_admin_print_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-sortable');
	if (isset($_GET['page']) && (($_GET['page'] == 'print-products-settings' && isset($_GET['tab']) && $_GET['tab'] == 'vendor') || ($_GET['page'] == 'print-products-create-order' && isset($_GET['step']) && $_GET['step'] == '2'))) {
		$default_location = wc_get_customer_default_location();
		wp_enqueue_script('wc-admin-order-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-order.min.js', array('wc-admin-meta-boxes', 'wc-backbone-modal', 'selectWoo', 'wc-clipboard'), WC_VERSION);
		wp_localize_script('wc-admin-order-meta-boxes', 'woocommerce_admin_meta_boxes_order', array(
			'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
			'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
			'default_country'        => isset( $default_location['country'] ) ? $default_location['country'] : '',
			'default_state'          => isset( $default_location['state'] ) ? $default_location['state'] : '',
			'placeholder_name'       => esc_attr__( 'Name (required)', 'woocommerce' ),
			'placeholder_value'      => esc_attr__( 'Value (required)', 'woocommerce' ),
		));
		wp_register_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION);
		wp_enqueue_style('woocommerce_admin_styles');
	}
}

add_action('admin_footer', 'print_products_admin_footer');
function print_products_admin_footer() {
	global $pagenow, $post;
	if (a12()) {
		echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">' . "\n";
		echo '<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>' . "\n";
		echo '<link href="' . plugins_url('css/colorbox.css', __FILE__) . '" rel="stylesheet" type="text/css" />' . "\n";
		echo '<link href="' . plugins_url('css/wp2print-admin.css', __FILE__) . '?ver='.time().'" rel="stylesheet" type="text/css" />' . "\n";
		echo '<script>var order_print_label = "'.__('Print', 'wp2print').'";</script>';
		echo '<script type="text/javascript" src="' . plugins_url('js/jquery.colorbox.min.js', __FILE__) . '"></script>' . "\n";
		echo '<script type="text/javascript" src="' . plugins_url('js/wp2print-admin.js?ver='.time().'', __FILE__) . '"></script>' . "\n";
		?>
		<script type="text/javascript">
			jQuery('#menu-posts-product ul li').each(function(){
				var ahtml = jQuery(this).find('a').html();
				if (ahtml == '<?php _e('Price Matrix Options', 'wp2print'); ?>' || ahtml == '<?php _e('Price Matrix Values', 'wp2print'); ?>' || ahtml == '<?php _e('Printing SKU Matrix', 'wp2print'); ?>') {
					jQuery(this).hide();
				}
			});
		</script>
		<?php
		if ($pagenow == 'post.php' && isset($_GET['action']) && $_GET['action'] == 'edit' && $post->post_type == 'shop_order') {
			$print_products_jobticket_options = get_option("print_products_jobticket_options");
			$exclude_prices = 0;
			if ($print_products_jobticket_options && $print_products_jobticket_options['exclude_prices'] == 1) { $exclude_prices = 1; }
			include PRINT_PRODUCTS_TEMPLATES_DIR . 'admin-order-proof-upload.php';
			if ($exclude_prices == 1) {
				echo '<script>jQuery("body").addClass("print-no-prices");</script>';
			}
		}
		if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'product' && isset($_GET['page']) && $_GET['page'] == 'product_attributes') {
			$print_products_settings = get_option('print_products_settings'); ?>
			<script>
			jQuery('.attributes-table .row-actions a.delete').each(function(){
				var ahref = jQuery(this).attr('href');
				if (ahref.indexOf('&delete=<?php echo $print_products_settings['size_attribute']; ?>&') > 0 || ahref.indexOf('&delete=<?php echo $print_products_settings['colour_attribute']; ?>&') > 0 || ahref.indexOf('&delete=<?php echo $print_products_settings['material_attribute']; ?>&') > 0 || ahref.indexOf('&delete=<?php echo $print_products_settings['page_count_attribute']; ?>&') > 0) {
					jQuery(this).remove();
				}
			});
			</script>
			<?php
		}
	}
}

add_action('admin_notices', 'print_products_admin_notices');
function print_products_admin_notices() {
	$notices = array();
	$all_plugins = get_plugins();
	if (!isset($all_plugins['woocommerce/woocommerce.php'])) {
		$notices[] = __('`Woocommerce plugin` is required for `wp2print` plugin. Please install it.', 'wp2print');
	} else if (!IS_WOOCOMMERCE) {
		$notices[] = __('`Woocommerce plugin` is required for `wp2print` plugin. Please activate it.', 'wp2print');
	}
	if (count($notices)) { ?>
		<div id="message" class="error fade">
			<p><?php echo implode('</p><p>', $notices); ?></p>
		</div>
	<?php
	}
	if (!a12()) {
		if ($_GET['page'] != 'print-products-activation') { ?>
			<div id="message" class="error fade">
				<p><?php echo sprintf(__("Please visit <a href='%s'>this page</a> and enter your license code.", 'wp2print'), 'options-general.php?page=print-products-settings'); ?></p>
			</div>
			<?php
		}
	}
}

add_action('after_setup_theme', 'print_products_remove_admin_bar');
function print_products_remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
}

// init actions
add_action('init', 'print_products_init');
function print_products_init() {
	if (function_exists('get_plugin_data')) {
		$wp2print_data = get_plugin_data(__FILE__);
		// check plugin version
		$wp2print_version = get_option('wp2print_version');
		if ($wp2print_version != $wp2print_data['Version']) {
			deactivate_plugins(plugin_basename(__FILE__));
			activate_plugin(__FILE__);
		}
	}

	// add admin lite role for woo orders
	$adminlite = get_role('adminlite');
	$administrator = get_role('administrator');
	if (!$adminlite) {
		$capabilities = array(
			'read' => true,
			'edit_files' => true,
			'edit_shop_order' => true,
			'read_shop_order' => true,
			'delete_shop_order' => true,
			'edit_shop_orders' => true,
			'edit_others_shop_orders' => true,
			'publish_shop_orders' => true,
			'read_private_shop_orders' => true,
			'delete_shop_orders' => true,
			'delete_private_shop_orders' => true,
			'delete_published_shop_orders' => true,
			'delete_others_shop_orders' => true,
			'edit_private_shop_orders' => true,
			'edit_published_shop_orders' => true
		);
		add_role('adminlite', __('Admin Lite'), $capabilities);
		$adminlite = get_role('adminlite');
	}
	if (!$adminlite->has_cap('rapid_quotes')) {
		$adminlite->add_cap('rapid_quotes');
	}
	if (!$administrator->has_cap('rapid_quotes')) {
		$administrator->add_cap('rapid_quotes');
	}
}

add_action('wp_loaded', 'print_products_wp_loaded');
function print_products_wp_loaded() {
	global $designer_installed;
	print_products_clear_cart_data();
	// check print-science-designer plugin
	if (function_exists('personalize_init')) {
		$designer_installed = true;
	}
	// product price with tax
	if (isset($_POST['AjaxAction'])) {
		switch ($_POST['AjaxAction']) {
			case 'product-get-price-with-tax':
				print_products_ajax_get_price_with_tax();
			break;
			case 'email-quote-send':
				print_products_ajax_email_quote_send();
			break;
		}
		exit;
	}
}

// Amazon S3 Classes
$amazonS3Client = false;
if ($print_products_file_upload_target == 'amazon' && $print_products_amazon_s3_settings['s3_access'] == 'private' && $print_products_amazon_s3_settings['s3_access_key'] && $print_products_amazon_s3_settings['s3_secret_key']) {
	require PRINT_PRODUCTS_PLUGIN_DIR . '/vendor/aws-autoloader.php';
	if (strlen($print_products_amazon_s3_settings['s3_region'])) {
		$amazonS3Client = Aws\S3\S3Client::factory(array('region' => $print_products_amazon_s3_settings['s3_region'], 'signature' => 'v4', 'credentials' => new Aws\Common\Credentials\Credentials($print_products_amazon_s3_settings['s3_access_key'], $print_products_amazon_s3_settings['s3_secret_key'])));
	} else {
		$amazonS3Client = Aws\S3\S3Client::factory(array('credentials' => new Aws\Common\Credentials\Credentials($print_products_amazon_s3_settings['s3_access_key'], $print_products_amazon_s3_settings['s3_secret_key'])));
	}
}
?>