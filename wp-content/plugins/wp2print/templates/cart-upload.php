<?php
$file_upload_target = get_option("print_products_file_upload_target");
$amazon_s3_settings = get_option("print_products_amazon_s3_settings");
$email_options = get_option("print_products_email_options");

$upload_to = 'host';
$plupload_url = get_bloginfo('url').'/index.php?ajaxupload=artwork';
if ($file_upload_target == 'amazon' && $amazon_s3_settings['s3_access_key'] && $amazon_s3_settings['s3_secret_key']) {
	$upload_to = 'amazon';

	$s3_data = print_products_amazon_s3_get_data($amazon_s3_settings, $file_upload_max_size);
	$plupload_url = $s3_data['amazon_url'];
	$amazon_file_url = $s3_data['amazon_file_url'];
	$multiparams = $s3_data['multiparams'];
}
?>
<div class="print-products-area" style="position:absolute;left:-20000px;">
	<div id="upload-artwork" class="upload-artwork-block" style="margin:30px 30px 0; border:1px solid #C1C1C1; padding:20px; width:600px; height:400px;">
		<p style="margin:0 0 12px;"><?php _e('Please select PDF file(s)', 'wp2print'); ?>:</p>
		<div id="filelist" class="ua-files-list" style="padding:10px 0; border-top:1px solid #C1C1C1; border-bottom:1px solid #C1C1C1;">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
		<div id="uacontainer" class="artwork-buttons">
			<a id="pickfiles" href="javascript:;" class="artwork-select"><?php _e('Select files', 'wp2print'); ?></a>
			<a id="uploadfiles" href="javascript:;" class="artwork-upload"><?php _e('Upload files', 'wp2print'); ?></a>
			<img src="<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>images/ajax-loading.gif" class="upload-loading">
		</div>
		<div class="clear"></div>
		<form method="POST" class="cart-upload-form">
		<input type="hidden" name="cart_upload_action" value="save">
		<input type="hidden" name="cart_item_key" class="cart-item-key">
		<input type="hidden" name="artwork_files" class="artwork-files">
		<input type="hidden" name="redirect_to" value="<?php the_permalink(); ?>">
		</form>
	</div>
</div>
<script type="text/javascript" src="<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>js/plupload/plupload.full.min.js"></script>
<script type="text/javascript">
function wp2print_cart_upload_button(cikey) {
	jQuery('.cart-upload-form .cart-item-key').val(cikey);
	jQuery.colorbox({inline:true, href:"#upload-artwork"});
}
jQuery(document).ready(function() {
	var uploader = new plupload.Uploader({
		runtimes : 'html5,flash,silverlight,html4',
		file_data_name: 'file',
		browse_button : 'pickfiles', // you can pass an id...
		container: document.getElementById('uacontainer'), // ... or DOM Element itself
		flash_swf_url : '<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>js/plupload/Moxie.swf',
		silverlight_xap_url : '<?php echo PRINT_PRODUCTS_PLUGIN_URL; ?>js/plupload/Moxie.xap',
		drop_element: document.getElementById('upload-artwork'), // ... or DOM Element itself
		url : '<?php echo $plupload_url; ?>',
		dragdrop: true,
		filters : {
			max_file_size : '2mb'
		},
		<?php if ($upload_to == 'amazon') { ?>
		multipart: true,
		<?php echo $multiparams; ?>
		<?php } ?>
		init: {
			PostInit: function() {
				jQuery('#filelist').html('').hide();
				jQuery('#uploadfiles').hide();

				document.getElementById('uploadfiles').onclick = function() {
					uploader.start();
					jQuery('#uploadfiles').attr('disabled', 'disabled');
					jQuery('.upload-loading').show();
					return false;
				};
			},
			FilesAdded: function(up, files) {
				var ucounterror = false;
				jQuery('#filelist').show();
				plupload.each(files, function(file) {
					document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
				});
				jQuery('#uploadfiles').removeAttr('disabled');
				jQuery('#uploadfiles').show();
			},
			UploadProgress: function(up, file) {
				document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			},
			<?php if ($upload_to == 'amazon') { ?>
			BeforeUpload: function(up, file) {
				var regex = /(?:\.([^.]+))?$/;
				for (var i = 0; i < up.files.length; i++) {
					if (file.id == up.files[i].id) {
						var ext = regex.exec(up.files[i].name)[1];
						if (ext == 'pdf') {
							up.settings.multipart_params['Content-Type'] = 'application/pdf';
						} else {
							up.settings.multipart_params['Content-Type'] = file.type;
						}
					}
				}
			},
			<?php } ?>
			FileUploaded: function(up, file, response) {
				<?php if ($upload_to == 'amazon') { ?>
					var ufileurl = '<?php echo $amazon_file_url; ?>'+file.name;
				<?php } else { ?>
					var ufileurl = response['response'];
				<?php } ?>
				if (ufileurl != '') {
					var artworkfiles = jQuery('.cart-upload-form .artwork-files').val();
					if (artworkfiles != '') { artworkfiles += ';'; }
					artworkfiles += ufileurl;
					jQuery('.cart-upload-form .artwork-files').val(artworkfiles);
				}
			},
			UploadComplete: function(files) {
				jQuery('.upload-loading').hide();
				jQuery.colorbox.close();
				jQuery('.cart-upload-form').submit();
			},
			Error: function(up, err) {
				alert("<?php _e('Upload error', 'wp2print'); ?>: "+err.message); // err.code
			}
		}
	});
	uploader.init();
});
</script>
