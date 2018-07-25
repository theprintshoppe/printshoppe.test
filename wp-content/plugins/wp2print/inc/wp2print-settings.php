<?php
$print_products_settings_error_message = '';
add_action('wp_loaded', 'print_products_activation_process');
function print_products_activation_process() {
	global $print_products_settings_error_message;
	if (isset($_POST['print_products_settings_submit'])) {
		switch ($_POST['print_products_settings_submit']) {
			case "license":
				$actval = 1;
				$license_key = trim($_POST['license_key']);
				$slm_action = trim($_POST['slm_action']);
				if ($slm_action == 'slm_deactivate') { $actval = 2; }
				if (strlen($license_key)) {
					$data = array ();
					$data['secret_key'] = PRINT_PRODUCTS_API_SECRET_KEY;
					$data['slm_action'] = $slm_action;
					$data['license_key'] = $license_key;
					$data['registered_domain'] = $_SERVER['SERVER_NAME'];
					$data['item_reference'] = 'wp2print plugin';

					// send data to activation server
					$ch = curl_init(PRINT_PRODUCTS_API_SERVER_URL);
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$response = json_decode(curl_exec($ch));
					$response_result = $response->result;
					$response_message = $response->message;
					if ($actval == 1) {
						if ($response_result == 'success') {
							$home_url = $_SERVER['SERVER_NAME'];
							$license_activation = $license_key.':'.md5($license_key.$home_url);
							update_option('print_products_license_activation', $license_activation);
							wp_redirect('options-general.php?page=print-products-settings&tab=license&activate='.$actval);
							exit;
						} else {
							$print_products_settings_error_message = $response_message;
						}
					} else {
						delete_option('print_products_license_activation');
						wp_redirect('options-general.php?page=print-products-settings&tab=license&activate='.$actval);
						exit;
					}
				}
			break;
			case "fuploads":
				$file_upload_target = $_POST['file_upload_target'];
				$file_upload_max_size = $_POST['file_upload_max_size'];
				$amazon_s3_settings = array(
					's3_access_key' => trim($_POST['s3_access_key']),
					's3_secret_key' => trim($_POST['s3_secret_key']),
					's3_bucketname' => trim($_POST['s3_bucketname']),
					's3_region' => trim($_POST['s3_region']),
					's3_access' => trim($_POST['s3_access']),
					's3_path' => trim($_POST['s3_path'])
				);
				update_option("print_products_file_upload_target", $file_upload_target);
				update_option("print_products_file_upload_max_size", $file_upload_max_size);
				update_option("print_products_amazon_s3_settings", $amazon_s3_settings);

				wp_redirect('options-general.php?page=print-products-settings&tab=fuploads&success=true');
				exit;
			break;
			case "infoform":
				$info_form_options = $_POST['info_form_options'];

				update_option("print_products_info_form_options", $info_form_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=infoform&success=true');
				exit;
			break;
			case "options":
				$print_products_plugin_options = $_POST['print_products_plugin_options'];

				update_option("print_products_plugin_options", $print_products_plugin_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=options&success=true');
				exit;
			break;
			case "api":
				$print_products_plugin_api = $_POST['print_products_plugin_api'];

				update_option("print_products_plugin_api", $print_products_plugin_api);

				wp_redirect('options-general.php?page=print-products-settings&tab=api&success=true');
				exit;
			break;
			case "aec":
				$print_products_plugin_aec = $_POST['print_products_plugin_aec'];

				update_option("print_products_plugin_aec", $print_products_plugin_aec);

				wp_redirect('options-general.php?page=print-products-settings&tab=aec&success=true');
				exit;
			break;
			case "email":
				$print_products_email_options = $_POST['print_products_email_options'];

				update_option("print_products_email_options", $print_products_email_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=email&success=true');
				exit;
			break;
			case "jobticket":
				$exclude_prices = (int)$_POST['print_products_jobticket_options']['exclude_prices'];
				$print_products_jobticket_options = array('exclude_prices' => $exclude_prices);

				update_option("print_products_jobticket_options", $print_products_jobticket_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=jobticket&success=true');
				exit;
			break;
			case "emailquote":
				$print_products_email_quote_options = $_POST['print_products_email_quote_options'];

				update_option("print_products_email_quote_options", $print_products_email_quote_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=emailquote&success=true');
				exit;
			break;
			case "vendor":
				$print_products_vendor_options = array(
					'shipping_address' => $_POST['shipping_address'],
					'billing_address' => $_POST['billing_address'],
					'use_billing' => $_POST['use_billing'],
					'email_subject' => $_POST['email_subject'],
					'email_header' => $_POST['email_header'],
					'email_top_text' => $_POST['email_top_text']
				);

				update_option("print_products_vendor_options", $print_products_vendor_options);

				wp_redirect('options-general.php?page=print-products-settings&tab=vendor&success=true');
				exit;
			break;
		}
	}
}


function print_products_settings() {
	global $print_products_settings_error_message;
	$tab = $_GET['tab'];
	if (!strlen($tab)) { $tab = 'license'; }

	$print_products_license_activation = get_option('print_products_license_activation');
	if ($print_products_license_activation) {
		$ppla = explode(':', $print_products_license_activation);
		$print_products_license_key = $ppla[0];
	}
	?>
	<div class="wrap wp2print-wrap wp2print-settings-wrap">
		<?php screen_icon(); ?>
		<h2><?php _e('wp2print Settings', 'wp2print'); ?></h2><br />
		<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
			<a href="options-general.php?page=print-products-settings&tab=license" class="nav-tab<?php if ($tab == 'license') { echo ' nav-tab-active'; } ?>"><?php _e('License', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=fuploads" class="nav-tab<?php if ($tab == 'fuploads') { echo ' nav-tab-active'; } ?>"><?php _e('File uploads', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=infoform" class="nav-tab<?php if ($tab == 'infoform') { echo ' nav-tab-active'; } ?>"><?php _e('Simple Submit Form', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=options" class="nav-tab<?php if ($tab == 'options') { echo ' nav-tab-active'; } ?>"><?php _e('Options', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=api" class="nav-tab<?php if ($tab == 'api') { echo ' nav-tab-active'; } ?>"><?php _e('Single Sign-on', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=aec" class="nav-tab<?php if ($tab == 'aec') { echo ' nav-tab-active'; } ?>"><?php _e('RapidQuote', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=email" class="nav-tab<?php if ($tab == 'email') { echo ' nav-tab-active'; } ?>"><?php _e('Email Options', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=jobticket" class="nav-tab<?php if ($tab == 'jobticket') { echo ' nav-tab-active'; } ?>"><?php _e('Job-ticket', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=emailquote" class="nav-tab<?php if ($tab == 'emailquote') { echo ' nav-tab-active'; } ?>"><?php _e('Email Quote', 'wp2print'); ?></a>
			<a href="options-general.php?page=print-products-settings&tab=vendor" class="nav-tab<?php if ($tab == 'vendor') { echo ' nav-tab-active'; } ?>"><?php _e('Vendor assignment', 'wp2print'); ?></a>
		</h2>
		<?php if ($tab == 'license') {
			$license_key = $print_products_license_key;
			if ($_POST['print_products_activation_action'] == 'true') { $license_key = trim($_POST['license_key']); }
			?>
			<form action="options-general.php?page=print-products-settings" method="POST">
			<input type="hidden" name="print_products_settings_submit" value="license">
			<?php if (strlen($print_products_settings_error_message)) { ?>
				<div id="message" class="error fade"><p style="color:#FF0000;"><?php echo $print_products_settings_error_message; ?></p></div>
			<?php } else if ($_GET['activate'] == '1') { ?>
				<div id="message" class="updated fade"><p><?php _e('License Key was successfully activated.', 'wp2print'); ?></p></div>
			<?php } else if ($_GET['activate'] == '2') { ?>
				<div id="message" class="updated fade"><p><?php _e('License Key was successfully deactivated.', 'wp2print'); ?></p></div>
			<?php } ?>
			<?php if ($print_products_license_key) { ?>
				<p><?php _e('You can deactivate the license key for `wp2print` plugin.', 'wp2print'); ?></p>
			<?php } else { ?>
				<p><?php _e('Please enter the license key for `wp2print` plugin to activate it.', 'wp2print'); ?></p>
			<?php } ?>
			<table>
				<tr>
					<td><?php _e('License Key', 'wp2print'); ?>:
					<?php print_products_help_icon('license_key'); ?></td>
					<td><input type="text" name="license_key" value="<?php echo $license_key; ?>" style="width:250px;"></td>
					<td>
						<?php if ($print_products_license_key) { ?>
							<input type="hidden" name="slm_action" value="slm_deactivate">
							<input type="submit" class="button-primary" value="<?php _e('Deactivate', 'wp2print') ?>" />
						<?php } else { ?>
							<input type="hidden" name="slm_action" value="slm_activate">
							<input type="submit" class="button-primary" value="<?php _e('Activate', 'wp2print') ?>" />
						<?php } ?>
					</td>
				</tr>
			</table>
			</form>
		<?php } else if ($tab == 'fuploads') {
			$file_upload_target = get_option("print_products_file_upload_target");
			$file_upload_max_size = get_option("print_products_file_upload_max_size");
			$amazon_s3_settings = get_option("print_products_amazon_s3_settings");
			$s3_path_vals = array('date', 'username', 'date/username', 'username/date');
			$s3_region_vals = array(
				'us-east-1' => 'US East (N. Virginia)',
				'us-east-2' => 'US East (Ohio)',
				'us-west-1' => 'US West (N. California)',
				'us-west-2' => 'US West (Oregon)',
				'ca-central-1' => 'Canada (Central)',
				'eu-central-1' => 'EU (Frankfurt)',
				'eu-west-1' => 'EU (Ireland)',
				'eu-west-2' => 'EU (London)',
				'eu-west-3' => 'EU (Paris)',
				'ap-northeast-1' => 'Asia Pacific (Tokyo)',
				'ap-northeast-2' => 'Asia Pacific (Seoul)',
				'ap-southeast-1' => 'Asia Pacific (Singapore)',
				'ap-southeast-2' => 'Asia Pacific (Sydney)',
				'ap-south-1' => 'Asia Pacific (Mumbai)',
				'sa-east-1' => 'South America (Sao Paulo)'
			);
			$s3_access_vals = array('public' => __('Public', 'wp2print'), 'private' => __('Private', 'wp2print'));
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="fuploads">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('File uploads settings were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('File upload target', 'wp2print'); ?>:
				<?php print_products_help_icon('file_upload_target'); ?></td>
				<td><select name="file_upload_target">
					<option value="host"><?php _e('Host server', 'wp2print'); ?></option>
					<option value="amazon"<?php if ($file_upload_target == 'amazon') { echo ' SELECTED'; } ?>><?php _e('Amazon S3', 'wp2print'); ?></option>
				</select></td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Access Key', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_access_key'); ?></td>
				<td><input type="text" name="s3_access_key" value="<?php echo $amazon_s3_settings['s3_access_key']; ?>" style="width:400px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Secret Key', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_secret_key'); ?></td>
				<td><input type="password" name="s3_secret_key" value="<?php echo $amazon_s3_settings['s3_secret_key']; ?>" style="width:400px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Bucketname', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_bucketname'); ?></td>
				<td><input type="text" name="s3_bucketname" value="<?php echo $amazon_s3_settings['s3_bucketname']; ?>" style="width:400px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Region', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_region'); ?></td>
				<td>
					<select name="s3_region">
						<option value=""><?php _e('v2 signature', 'wp2print'); ?></option>
						<?php foreach($s3_region_vals as $rkey => $rval) { $s = ''; if ($rkey == $amazon_s3_settings['s3_region']) { $s = ' SELECTED'; } ?>
							<option value="<?php echo $rkey; ?>"<?php echo $s; ?>><?php echo $rval; ?></option>
						<?php } ?>
					</select>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Path', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_path'); ?></td>
				<td>
					<select name="s3_path">
						<option value="">-- <?php _e('Select Path', 'wp2print'); ?> --</option>
						<?php foreach($s3_path_vals as $s3_path_val) { $s = ''; if ($s3_path_val == $amazon_s3_settings['s3_path']) { $s = ' SELECTED'; } ?>
							<option value="<?php echo $s3_path_val; ?>"<?php echo $s; ?>><?php echo $s3_path_val; ?></option>
						<?php } ?>
					</select>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('S3 Files Access', 'wp2print'); ?>:
				<?php print_products_help_icon('s3_access'); ?></td>
				<td>
					<select name="s3_access">
						<?php foreach($s3_access_vals as $akey => $aval) { $s = ''; if ($akey == $amazon_s3_settings['s3_access']) { $s = ' SELECTED'; } ?>
							<option value="<?php echo $akey; ?>"<?php echo $s; ?>><?php echo $aval; ?></option>
						<?php } ?>
					</select>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('File upload max size', 'wp2print'); ?>, Mb:
				<?php print_products_help_icon('file_upload_max_size'); ?></td>
				<td><input type="number" name="file_upload_max_size" value="<?php echo (int)$file_upload_max_size; ?>" min="1" style="width:60px;">
				</td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'infoform') {
			$print_products_info_form_options = get_option("print_products_info_form_options");
			$countries = print_products_info_form_get_countries();
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="infoform">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Form settings were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('Form title', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_form_title'); ?></td>
				<td><input type="text" name="info_form_options[form_title]" value="<?php echo $print_products_info_form_options['form_title']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Form success text', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_form_success_text'); ?></td>
				<td><textarea name="info_form_options[form_success_text]" style="width:450px;height:150px;"><?php echo $print_products_info_form_options['form_success_text']; ?></textarea>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Default country', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_default_country'); ?></td>
				<td>
					<select name="info_form_options[default_country]" style="width:450px;">
						<option value="">-- <?php _e('Select country', 'wp2print'); ?> --</option>
						<?php foreach($countries as $ckey => $cval) { $s = ''; if ($ckey == $print_products_info_form_options['default_country']) { $s = ' SELECTED'; } ?>
							<option value="<?php echo $ckey; ?>"<?php echo $s; ?>><?php echo $cval; ?></option>
						<?php } ?>
					</select>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Enable State field', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_enable_state_field'); ?></td>
				<td><input type="checkbox" name="info_form_options[enable_state_field]" value="1"<?php if ($print_products_info_form_options['enable_state_field']) { echo ' CHECKED'; } ?>></td>
			  </tr>
			  <tr>
				<td><?php _e('State field label', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_state_field_label'); ?></td>
				<td><input type="text" name="info_form_options[state_field_label]" value="<?php echo $print_products_info_form_options['state_field_label']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Zip field label', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_zip_field_label'); ?></td>
				<td><input type="text" name="info_form_options[zip_field_label]" value="<?php echo $print_products_info_form_options['zip_field_label']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Customer email subject', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_customer_email_subject'); ?></td>
				<td><input type="text" name="info_form_options[customer_email_subject]" value="<?php echo $print_products_info_form_options['customer_email_subject']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Customer email heading', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_customer_email_heading'); ?></td>
				<td><input type="text" name="info_form_options[customer_email_heading]" value="<?php echo $print_products_info_form_options['customer_email_heading']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Customer email content', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_customer_email_content'); ?></td>
				<td><textarea name="info_form_options[customer_email_content]" style="width:450px;height:150px;"><?php echo $print_products_info_form_options['customer_email_content']; ?></textarea>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Admin email subject', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_admin_email_subject'); ?></td>
				<td><input type="text" name="info_form_options[admin_email_subject]" value="<?php echo $print_products_info_form_options['admin_email_subject']; ?>" style="width:450px;">
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Admin email heading', 'wp2print'); ?>:
				<?php print_products_help_icon('infoform_admin_email_heading'); ?></td>
				<td><input type="text" name="info_form_options[admin_email_heading]" value="<?php echo $print_products_info_form_options['admin_email_heading']; ?>" style="width:450px;">
				</td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'options') {
			$print_products_plugin_options = get_option("print_products_plugin_options");
			$dfc_types = array('icons' => __('Icons', 'wp2print'), 'filenames' => __('Filenames', 'wp2print')); ?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="options">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('Buttons CSS class', 'wp2print'); ?>:
				<?php print_products_help_icon('options_butclass'); ?></td>
				<td><input type="text" name="print_products_plugin_options[butclass]" value="<?php echo $print_products_plugin_options['butclass']; ?>" style="width:300px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Display files in cart as', 'wp2print'); ?>:
				<?php print_products_help_icon('options_dfincart'); ?></td>
				<td><select name="print_products_plugin_options[dfincart]">
					<?php foreach($dfc_types as $tkey => $tval) { ?>
						<option value="<?php echo $tkey; ?>"<?php if ($tkey == $print_products_plugin_options['dfincart']) { echo ' SELECTED'; } ?>><?php echo $tval; ?></option>
					<?php } ?>
				</td>
			  </tr>
			  <tr>
				<td><?php _e('Display attributes help icon', 'wp2print'); ?>:
				<?php print_products_help_icon('options_ahelpicon'); ?></td>
				<td><input type="checkbox" name="print_products_plugin_options[ahelpicon]" value="1"<?php if ($print_products_plugin_options['ahelpicon'] == 1) { echo ' CHECKED'; } ?>></td>
			  </tr>
			  <tr>
				<td><?php _e('Allow users to modify group', 'wp2print'); ?>:
				<?php print_products_help_icon('options_allowmodifygroup'); ?></td>
				<td><input type="checkbox" name="print_products_plugin_options[allowmodifygroup]" value="1"<?php if ($print_products_plugin_options['allowmodifygroup'] == 1) { echo ' CHECKED'; } ?>></td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'api') {
			$print_products_plugin_api = get_option("print_products_plugin_api");
			$dfc_types = array('icons' => __('Icons', 'wp2print'), 'filenames' => __('Filenames', 'wp2print')); ?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="api">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('Enable Single Sign-on', 'wp2print'); ?>:
				<?php print_products_help_icon('api_enable'); ?></td>
				<td><input type="checkbox" name="print_products_plugin_api[enable]" value="1"<?php if ($print_products_plugin_api['enable']) { echo ' CHECKED'; } ?>></td>
			  </tr>
			  <tr><td colspan="2" height="5"></td></tr>
			  <tr>
				<td><?php _e('API Key', 'wp2print'); ?>:
				<?php print_products_help_icon('api_key'); ?></td>
				<td><input type="text" name="print_products_plugin_api[key]" value="<?php echo $print_products_plugin_api['key']; ?>" style="width:400px;"></td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'aec') {
			$print_products_plugin_aec = get_option("print_products_plugin_aec");
			$dfc_types = array('icons' => __('Icons', 'wp2print'), 'filenames' => __('Filenames', 'wp2print'));
			$dimunits = array('m', 'cm', 'mm', 'in', 'yd', 'ft');
			if (!$print_products_plugin_aec['aec_dimensions_unit']) {
				$print_products_plugin_aec['aec_dimensions_unit'] = print_products_get_dimension_unit();
				update_option("print_products_plugin_aec", $print_products_plugin_aec);
			}
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="aec">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td width="170"><?php _e('Coverage % Ranges', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_coverage_ranges'); ?></td>
				<td><input type="text" name="print_products_plugin_aec[aec_coverage_ranges]" value="<?php echo $print_products_plugin_aec['aec_coverage_ranges']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Dimensions unit', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_dimensions_unit'); ?></td>
				<td><select name="print_products_plugin_aec[aec_dimensions_unit]">
					<option value="">-- <?php _e('Select unit', 'wp2print'); ?> --</option>
					<?php foreach($dimunits as $dimunit) { ?>
						<option value="<?php echo $dimunit; ?>"<?php if ($dimunit == $print_products_plugin_aec['aec_dimensions_unit']) { echo ' SELECTED'; } ?>><?php echo $dimunit; ?></option>
					<?php } ?>
				</select></td>
			  </tr>
					
			  <tr>
				<td><?php _e('Enable size modification in Low-cost option pop-up', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_enable_size'); ?></td>
				<td><input type="checkbox" name="print_products_plugin_aec[aec_enable_size]" value="1"<?php if ($print_products_plugin_aec['aec_enable_size']) { echo ' CHECKED'; } ?>></td>
			  </tr>
			  <tr>
				<td><?php _e('Pay Now button text', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_pay_now_text'); ?></td>
				<td><input type="text" name="print_products_plugin_aec[pay_now_text]" value="<?php echo $print_products_plugin_aec['pay_now_text']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Subject', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_order_email_subject'); ?></td>
				<td><input type="text" name="print_products_plugin_aec[order_email_subject]" value="<?php echo $print_products_plugin_aec['order_email_subject']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Message', 'wp2print'); ?>:
				<?php print_products_help_icon('aec_order_email_message'); ?></td>
				<td><textarea name="print_products_plugin_aec[order_email_message]" style="width:500px;height:150px;"><?php echo $print_products_plugin_aec['order_email_message']; ?></textarea><br /><?php _e('Use', 'wp2print'); ?>: {PAGE-DETAIL-MATRIX}, {TOTAL-PRICE}, {PAY-NOW-LINK}, {PROJECT-NAME}
				</td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'email') {
			$print_products_email_options = get_option("print_products_email_options");
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="email">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Email options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td colspan="2" class="pp-head-td"><?php _e('Approval order email', 'wp2print'); ?>:&nbsp;</td>
			  </tr>
			  <tr>
				<td><?php _e('Email Subject', 'wp2print'); ?>:
				<?php print_products_help_icon('email_order_proof_subject'); ?></td>
				<td><input type="text" name="print_products_email_options[order_proof_subject]" value="<?php echo $print_products_email_options['order_proof_subject']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Message', 'wp2print'); ?>:
				<?php print_products_help_icon('email_order_proof_message'); ?></td>
				<td><textarea name="print_products_email_options[order_proof_message]" style="width:500px;height:150px;"><?php echo $print_products_email_options['order_proof_message']; ?></textarea><br /><?php _e('Use', 'wp2print'); ?>: [ORDERS_AWAITING_APPROVAL_LINK]
				</td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'jobticket') {
			$print_products_jobticket_options = get_option("print_products_jobticket_options");
			if (!$print_products_jobticket_options) { $print_products_jobticket_options = array('exclude_prices' => 0);}
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="jobticket">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Job-ticket options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('Job-ticket excludes prices', 'wp2print'); ?>:
				<?php print_products_help_icon('jobticket_exclude_prices'); ?></td>
				<td><input type="checkbox" name="print_products_jobticket_options[exclude_prices]" value="1"<?php if ($print_products_jobticket_options['exclude_prices'] == 1) { echo ' CHECKED'; } ?>></td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'emailquote') {
			$print_products_email_quote_options = get_option("print_products_email_quote_options");
			?>
			<form method="POST">
			<input type="hidden" name="print_products_settings_submit" value="emailquote">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Email quote options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td><?php _e('Enable Widget', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_enable'); ?></td>
				<td><input type="checkbox" name="print_products_email_quote_options[enable]" value="1"<?php if ($print_products_email_quote_options['enable'] == 1) { echo ' CHECKED'; } ?>></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Subject', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_subject'); ?></td>
				<td><input type="text" name="print_products_email_quote_options[subject]" value="<?php echo $print_products_email_quote_options['subject']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Message Heading', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_heading'); ?></td>
				<td><input type="text" name="print_products_email_quote_options[heading]" value="<?php echo $print_products_email_quote_options['heading']; ?>" style="width:500px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Message Top Text', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_toptext'); ?></td>
				<td><textarea name="print_products_email_quote_options[toptext]" style="width:500px;height:100px;"><?php echo $print_products_email_quote_options['toptext']; ?></textarea></td>
			  </tr>
			  <tr>
				<td><?php _e('Message Bottom Text', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_bottomtext'); ?></td>
				<td><textarea name="print_products_email_quote_options[bottomtext]" style="width:500px;height:100px;"><?php echo $print_products_email_quote_options['bottomtext']; ?></textarea></td>
			  </tr>
			  <tr>
				<td><?php _e('Disable widget in Private Stores', 'wp2print'); ?>:
				<?php print_products_help_icon('emailquote_disable_private'); ?></td>
				<td><input type="checkbox" name="print_products_email_quote_options[disable_private]" value="1"<?php if ($print_products_email_quote_options['disable_private'] == 1) { echo ' CHECKED'; } ?>></td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } else if ($tab == 'vendor') {
			$print_products_vendor_options = get_option("print_products_vendor_options");
			$shipping_address = $print_products_vendor_options['shipping_address'];
			$billing_address = $print_products_vendor_options['billing_address'];
			$use_billing = $print_products_vendor_options['use_billing'];
			$address_fields = array(
				'company' => array(
					'label' => __('Company', 'woocommerce'),
					'type'  => 'text'
				),
				'address_1' => array(
					'label' => __('Address 1', 'woocommerce'),
					'type'  => 'text'
				),
				'address_2' => array(
					'label' => __('Address 2', 'woocommerce'),
					'type'  => 'text'
				),
				'city' => array(
					'label' => __('City', 'woocommerce'),
					'type'  => 'text'
				),
				'postcode' => array(
					'label' => __('Postcode', 'woocommerce'),
					'type'  => 'text'
				),
				'country' => array(
					'label'   => __('Country', 'woocommerce'),
					'type'    => 'select',
					'style'   => 'width:95%;',
					'class'   => 'js_field-country select short',
					'options' => array('' => __('Select a country&hellip;', 'woocommerce') ) + WC()->countries->get_shipping_countries()
				),
				'state' => array(
					'label' => __('State', 'woocommerce'),
					'class' => 'js_field-state select short',
					'type'  => 'text',
					'style' => 'width:95%;'
				)
			);
			?>
			<form method="POST" class="print-products-settings-vendor-form">
			<input type="hidden" name="print_products_settings_submit" value="vendor">
			<?php if($_GET['success'] == 'true') { ?>
				<div id="message" class="updated fade"><p><?php _e('Vendor options were successfully saved.', 'wp2print'); ?></p></div>
			<?php } ?>
			<table style="width:auto;">
			  <tr>
				<td style="font-weight:700;"><?php _e('Printshop Address for Vendor shipments', 'wp2print'); ?>
				<?php print_products_help_icon('vendor_shipping_address'); ?></td>
				<td style="font-weight:700;"><?php _e('Printshop Address for Vendor billing', 'wp2print'); ?>
				<?php print_products_help_icon('vendor_billing_address'); ?></td>
			  </tr>
			  <tr>
			    <td valign="top">
					<div class="edit_address" style="width:280px;">
						<?php foreach ($address_fields as $key => $field) {
							$field['id'] = '_shipping_' . $key;
							$field['name'] = 'shipping_address[' . $key . ']';
							$field['value'] = $shipping_address[$key];
							if ($field['type'] == 'select') {
								woocommerce_wp_select($field);
							} else {
								woocommerce_wp_text_input($field);
							}
							?>
						<?php } ?>
					</div>
				</td>
			    <td valign="top">
					<div class="edit_address" style="width:280px;">
						<?php foreach ($address_fields as $key => $field) {
							$field['id'] = '_billing_' . $key;
							$field['name'] = 'billing_address[' . $key . ']';
							$field['value'] = $billing_address[$key];
							if ($field['type'] == 'select') {
								woocommerce_wp_select($field);
							} else {
								woocommerce_wp_text_input($field);
							}
							?>
						<?php } ?>
					</div>
				</td>
			  </tr>
			</table>
			<table style="width:auto;">
			  <tr>
				<td><input type="checkbox" name="use_billing" value="1"<?php if ($use_billing == 1) { echo ' CHECKED'; } ?>></td>
				<td><?php _e('Use printshop billing address in place of customer billing address', 'wp2print'); ?>
				<?php print_products_help_icon('vendor_use_billing'); ?></td>
			  </tr>
			</table><br>
			<table style="width:auto;">
			  <tr>
				<td colspan="2" style="font-weight:700;"><?php _e('Vendor email options', 'wp2print'); ?></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Subject', 'wp2print'); ?>:
				<?php print_products_help_icon('vendor_email_subject'); ?></td>
				<td><input type="text" name="email_subject" value="<?php echo $print_products_vendor_options['email_subject']; ?>" style="width:460px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Header', 'wp2print'); ?>:
				<?php print_products_help_icon('vendor_email_header'); ?></td>
				<td><input type="text" name="email_header" value="<?php echo $print_products_vendor_options['email_header']; ?>" style="width:460px;"></td>
			  </tr>
			  <tr>
				<td><?php _e('Email Top Text', 'wp2print'); ?>:
				<?php print_products_help_icon('vendor_email_top_text'); ?></td>
				<td><input type="text" name="email_top_text" value="<?php echo $print_products_vendor_options['email_top_text']; ?>" style="width:460px;"></td>
			  </tr>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'wp2print'); ?>" /></p>
			</form>
		<?php } ?>
	</div>
	<?php
}
?>