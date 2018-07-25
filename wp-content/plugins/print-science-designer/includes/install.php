<?php
function ps_install() {
    global $wpdb;

	$wpdb->query("CREATE TABLE IF NOT EXISTS " . API_INFO_TABLE . " (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR( 255 ) NOT NULL,
		api_key VARCHAR( 255 ) NOT NULL,
		version VARCHAR( 255 ) NOT NULL,
		url VARCHAR( 255 ) NOT NULL,
		image_url VARCHAR( 255 ) NOT NULL,
		window_type VARCHAR( 255 ) NOT NULL,
		background_color VARCHAR( 255 ) NOT NULL,
		opacity VARCHAR( 255 ) NOT NULL,
		margin VARCHAR( 255 ) NOT NULL,
		show_pdf TINYINT NULL DEFAULT '0',
		saved_projects_page INT NULL
	)");
	$wpdb->query("ALTER TABLE " . API_INFO_TABLE . " ADD show_pdf TINYINT NULL DEFAULT '0'");
	$wpdb->query("ALTER TABLE " . API_INFO_TABLE . " ADD saved_projects_page INT NULL");
            
    $wpdb->query("CREATE TABLE IF NOT EXISTS " . CART_DATA_TABLE . " (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		uniqueID VARCHAR( 255 ) NULL,
		printImage TEXT NULL,
		editURL VARCHAR( 255 ) NULL,
		cart_item_key VARCHAR(100) NULL,
		sessionKey VARCHAR(100) NULL
	)");
	$wpdb->query("ALTER TABLE " . CART_DATA_TABLE . " CHANGE printImage printImage TEXT NULL DEFAULT NULL");
	$wpdb->query("ALTER TABLE " . CART_DATA_TABLE . " ADD cart_item_key VARCHAR(100) NULL");
	$wpdb->query("ALTER TABLE " . CART_DATA_TABLE . " ADD sessionKey VARCHAR(100) NULL");

    $wpdb->query("CREATE TABLE IF NOT EXISTS " . SAVED_PROJECTS_TABLE . " (
		id INT NOT NULL AUTO_INCREMENT,
		user_id INT NULL DEFAULT '0',
		session_key VARCHAR(100) NULL,
		product_id INT NOT NULL,
		variation_id INT NULL,
		quantity INT NULL DEFAULT '0',
		price FLOAT NULL DEFAULT '0',
		image_url TEXT NULL,
		adata TEXT NULL,
		PRIMARY KEY (id),
		KEY user_id (user_id)
	)");

	// copy translation files
	$plugins_lang_folder = $_SERVER['DOCUMENT_ROOT'].'/wp-content/languages/plugins';
	if (is_dir($plugins_lang_folder)) {
		$langpofiles = glob(dirname(__FILE__).'/language/*.po');
		if (count($langpofiles)) {
			foreach($langpofiles as $langpofile) {
				@copy($langpofile, $plugins_lang_folder.'/'.basename($langpofile));
			}
		}
		$langmofiles = glob(dirname(__FILE__).'/language/*.mo');
		if (count($langmofiles)) {
			foreach($langmofiles as $langmofile) {
				@copy($langmofile, $plugins_lang_folder.'/'.basename($langmofile));
			}
		}
	}
	$saved_projects_page = personalize_get_option('saved_projects_page');
	if (!$saved_projects_page) {
		$new_page = array();
		$new_page['post_title'] = 'Saved Projects';
		$new_page['post_content'] = '[print-science-designer-saved-projects]';
		$new_page['post_status'] = 'publish';
		$new_page['post_type'] = 'page';
		$new_page_id = wp_insert_post($new_page);
		personalize_update_option('saved_projects_page', $new_page_id);
	}
}
?>