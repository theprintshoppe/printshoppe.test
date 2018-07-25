<?php
add_shortcode('print-science-designer', 'personalize_shortcode_html');
function personalize_shortcode_html($attr) {
	global $post;
	ob_start();
	unset($_SESSION['pdo-session-key']);
	$product_id = $attr['product_id'];
	$template_id = $attr['template_id'];
	$return_page_id = $attr['return_page_id'];

	$poclass = 'personalize';
	$window_type = personalize_get_window_type();
	if ($window_type == 'Modal Pop-up window') {
		$poclass = 'personalize personalizep';
	}
	?>
	<div class="print-designer-online">
		<form method="POST">
			<input type="hidden" name="pdoprocess" value="true">
			<input type="hidden" name="page_id" value="<?php echo $post->ID; ?>" class="pdo-page-id">
			<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" class="pdo-product-id">
			<input type="hidden" name="template_id" value="<?php echo $template_id; ?>" class="pdo-template-id">
			<input type="hidden" name="return_page_id" value="<?php echo $return_page_id; ?>" class="pdo-return-page-id">
			<button class="button pdo-process <?php echo $poclass; ?>"><?php _e('Design online', 'personalize'); ?></button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode('print-science-designer-download', 'personalize_shortcode_download_html');
function personalize_shortcode_download_html($attr) {
	ob_start();
	if (isset($_SESSION['pdo-session-key']) && strlen($_SESSION['pdo-session-key'])) {
		$api_response = personalize_get_response_from_api($_SESSION['pdo-session-key']);
		$img_urls = $api_response['img_urls'];
		$pdf_urls = $api_response['pdf_urls']; ?>
		<div class="print-designer-online">
			<ul>
				<?php foreach($img_urls as $img_url) { ?>
					<li><a rel="prettyPhoto" href="<?php echo $img_url; ?>" data-rel="prettyPhoto[pdoimg]"><img src="<?php echo $img_url; ?>" alt=""></a></li>
				<?php } ?>
			</ul>
			<?php foreach($pdf_urls as $pdf_url) { ?>
				<a href="<?php echo $pdf_url; ?>" class="button pdo-download"><?php _e('Download PDF', 'personalize'); ?></a>
			<?php } ?>
		</div>
	<?php
	}
	return ob_get_clean();
}

add_action('wp_loaded', 'personalize_shortcode_process');
function personalize_shortcode_process() {
    global $wpdb, $personalize_settings, $api_info;
	$window_type = personalize_get_window_type();
	if (isset($_REQUEST['pdoprocess']) && $_REQUEST['pdoprocess'] == 'true' && strlen($_REQUEST['product_id'])) {
		$page_id = $_REQUEST['page_id'];
		$product_id = $_REQUEST['product_id'];
		$template_id = $_REQUEST['template_id'];
		$return_page_id = $_REQUEST['return_page_id'];
		$pdopopup = $_REQUEST['pdopopup'];

		$current_page_url = get_permalink($page_id);
		$success_url = $current_page_url;
		if ($return_page_id) {
			$success_url = get_permalink($return_page_id);
		}

		$TemplatexML = new xmlrpcval(null, 'null');
		if (strlen($template_id)) {
			$TemplatexML = php_xmlrpc_encode($template_id);
		}
		$username = $api_info->username;
		$api_key = $api_info->api_key;
		$apiUrl = $api_info->url;
		$image_url = $api_info->image_url;
		$window_type = $api_info->window_type;
		$background_color = $api_info->background_color;
		$opacity = $api_info->opacity;
		$margin = $api_info->margin;
		$successUrl = add_query_arg(array('pdopopup' => $pdopopup), $success_url);
		$failUrl = add_query_arg(array('pdofail' => '1', 'pdopopup' => $pdopopup), $current_page_url);
		$cancelUrl = add_query_arg(array('pdocancel' => '1', 'pdopopup' => $pdopopup), $current_page_url);
		$locale = apply_filters( 'plugin_locale', get_locale(), 'personalize' );
		$localLang = explode("_", $locale);
		$comment = 'Design online via shortcode';

		$function = new xmlrpcmsg('beginPersonalization', array(
			php_xmlrpc_encode($username),
			php_xmlrpc_encode($api_key),
			php_xmlrpc_encode($product_id),
			php_xmlrpc_encode($successUrl),
			php_xmlrpc_encode($failUrl),
			php_xmlrpc_encode($cancelUrl),
			php_xmlrpc_encode($comment),
			php_xmlrpc_encode($localLang[0]),
			$TemplatexML
		));
		$client = new xmlrpc_client($apiUrl);
		$response = $client->send($function);
		if (!$response->errno) {
			$sessionkey = $response->value()->arrayMem(0)->scalarval();
			$preview_url = $response->value()->arrayMem(1)->scalarval();
			$_SESSION['pdo-session-key'] = $sessionkey;
			wp_redirect($preview_url);
			exit;
		} else {
			$error = str_replace(array('<','>'), '', $response->errstr);
			wp_die($error);
		}
	}
	if (isset($_REQUEST['pdopopup']) && $_REQUEST['pdopopup'] == '1') {
		if ($window_type == 'Modal Pop-up window') {
			$request_url = $_SERVER['REQUEST_URI'];
			$request_url = remove_query_arg(array('pdopopup'), $request_url); ?>
			<script>
				window.parent.location = '<?php echo $request_url; ?>';
			</script>
			<?php
			exit;
		}
	}
}
?>