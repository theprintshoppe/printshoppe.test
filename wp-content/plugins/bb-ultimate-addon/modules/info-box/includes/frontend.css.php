<?php

	global $post;
	$converted = get_post_meta( $post->ID,'_uabb_converted', true );

$settings->prefix_color = UABB_Helper::uabb_colorpicker( $settings, 'prefix_color' );
$settings->title_color = UABB_Helper::uabb_colorpicker( $settings, 'title_color' );
$settings->subhead_color = UABB_Helper::uabb_colorpicker( $settings, 'subhead_color' );
$settings->link_color = UABB_Helper::uabb_colorpicker( $settings, 'link_color' );

$settings->bg_color = UABB_Helper::uabb_colorpicker( $settings, 'bg_color', true );
$settings->img_bg_hover_color = UABB_Helper::uabb_colorpicker( $settings, 'img_bg_hover_color', true );

$settings->icon_hover_color = UABB_Helper::uabb_colorpicker( $settings, 'icon_hover_color' );
$settings->uabb_border_color = UABB_Helper::uabb_colorpicker( $settings, 'uabb_border_color' );
$settings->icon_bg_hover_color = UABB_Helper::uabb_colorpicker( $settings, 'icon_bg_hover_color', true );
$settings->icon_border_hover_color = UABB_Helper::uabb_colorpicker( $settings, 'icon_border_hover_color' );
$settings->img_border_hover_color = UABB_Helper::uabb_colorpicker( $settings, 'img_border_hover_color' );

$settings->img_size = ( trim($settings->img_size) !== '' ) ? $settings->img_size : '150';
$settings->icon_size = ( trim($settings->icon_size) !== '' ) ? $settings->icon_size : '30';
$settings->icon_bg_size = ( trim($settings->icon_bg_size) !== '' ) ? $settings->icon_bg_size : '30';
$settings->icon_border_width = ( trim($settings->icon_border_width) !== '' ) ? $settings->icon_border_width : '1';
$settings->img_border_width = ( trim($settings->img_border_width) !== '' ) ? $settings->img_border_width : '1';
?>

.fl-node-<?php echo $id; ?> {
	width: 100%;
}
<?php

/* Render Butto */
if($settings->cta_type == 'button') {
	FLBuilder::render_module_css('uabb-button', $id, array(
		/* General Section */
        'text'              => $settings->btn_text,
        
        /* Link Section */
        'link'              => $settings->btn_link,
        'link_target'       => $settings->btn_link_target,
        
        /* Style Section */
        'style'             => $settings->btn_style,
        'border_size'       => $settings->btn_border_size,
        'transparent_button_options' => $settings->btn_transparent_button_options,
        'threed_button_options'      => $settings->btn_threed_button_options,
        'flat_button_options'        => $settings->btn_flat_button_options,

        /* Colors */
        'bg_color'          => $settings->btn_bg_color,
        'bg_color_opc'          => $settings->btn_bg_color_opc,
        'bg_hover_color'    => $settings->btn_bg_hover_color,
        'bg_hover_color_opc'    => $settings->btn_bg_hover_color_opc,
        'text_color'        => $settings->btn_text_color,
        'text_hover_color'  => $settings->btn_text_hover_color,
        'hover_attribute'	=> $settings->hover_attribute,

        /* Icon */
        'icon'              => $settings->btn_icon,
        'icon_position'     => $settings->btn_icon_position,
        
        /* Structure */
        'width'              => $settings->btn_width,
        'custom_width'       => $settings->btn_custom_width,
        'custom_height'      => $settings->btn_custom_height,
        'padding_top_bottom' => $settings->btn_padding_top_bottom,
        'padding_left_right' => $settings->btn_padding_left_right,
        'border_radius'      => $settings->btn_border_radius,
        'align'              => '',
        'mob_align'          => '',

        /* Typography */
        'font_size'                   => ( isset($settings->btn_font_size) ) ? $settings->btn_font_size : '',
        'line_height'                 => ( isset($settings->btn_line_height) ) ? $settings->btn_line_height : '',
        'line_height_unit'            => $settings->btn_line_height_unit,
        'font_size_unit'              => $settings->btn_font_size_unit,
        'font_size_unit_medium'       => $settings->btn_font_size_unit_medium,
        'line_height_unit_medium'     => $settings->btn_line_height_unit_medium,
        'font_size_unit_responsive'   => $settings->btn_font_size_unit_responsive,
        'line_height_unit_responsive' => $settings->btn_line_height_unit_responsive,
        'font_family'       		  => $settings->btn_font_family,
        'transform'                   => $settings->btn_transform,
        'letter_spacing'              => $settings->btn_letter_spacing,
	));
}
/* Render Separator */
if( $settings->enable_separator == 'block' ) {
	FLBuilder::render_module_css('uabb-separator', $id, array(
		'color'			=> $settings->separator_color,
		'height'		=> ( trim($settings->separator_height) !== '' ) ? $settings->separator_height : '1',
		'width'			=> ( trim($settings->separator_width) !== '' ) ? $settings->separator_width : '100',
		'alignment'		=> $settings->separator_alignment,
		'style'			=> $settings->separator_style
	));
?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-separator {
		margin-top: <?php echo ( trim( $settings->separator_margin_top ) !== '' ) ? $settings->separator_margin_top : '20'; ?>px;
		margin-bottom: <?php echo ( trim( $settings->separator_margin_bottom ) !== '' ) ? $settings->separator_margin_bottom : '20'; ?>px;
	}
<?php }
/* Render Image Icon CSS */
/* CSS "$settings" Array */
 
 /* Icon Image Render */
if( $settings->image_type != 'none' ) {

	/* CSS "$settings" Array */
 	$imageicon_array = array(
      
      	/* General Section */
      	'image_type' => $settings->image_type,
 
      	/* Icon Basics */
      	'icon' => $settings->icon,
      	'icon_size' => $settings->icon_size,
      	'icon_align' => '',
 
      	/* Image Basics */
      	'photo_source' => $settings->photo_source,
      	'photo' => $settings->photo,
      	'photo_url' => $settings->photo_url,
      	'img_size' => $settings->img_size,
      	'responsive_img_size' => $settings->responsive_img_size,
      	'img_align' => '',
      	'photo_src' => ( isset( $settings->photo_src ) ) ? $settings->photo_src : '' ,
 
      	/* Icon Style */
      	'icon_style' => $settings->icon_style,
      	'icon_bg_size' => $settings->icon_bg_size,
      	'icon_border_style' => $settings->icon_border_style,
      	'icon_border_width' => $settings->icon_border_width,
      	'icon_bg_border_radius' => $settings->icon_bg_border_radius,
 
      	/* Image Style */
      	'image_style' => $settings->image_style,
      	'img_bg_size' => $settings->img_bg_size,
      	'img_border_style' => $settings->img_border_style,
      	'img_border_width' => $settings->img_border_width,
      	'img_bg_border_radius' => $settings->img_bg_border_radius,
 		
 		/* Preset Color variable new */
      	'icon_color_preset' => $settings->icon_color_preset,

      	/* Icon Colors */ 
      	'icon_color' => $settings->icon_color,
      	'icon_hover_color' => $settings->icon_hover_color,
      	'icon_bg_color' => $settings->icon_bg_color,
      	'icon_bg_color_opc' => $settings->icon_bg_color_opc,
      	'icon_bg_hover_color' => $settings->icon_bg_hover_color,
      	'icon_bg_hover_color_opc' => $settings->icon_bg_hover_color_opc,
      	'icon_border_color' => $settings->icon_border_color,
      	'icon_border_hover_color' => $settings->icon_border_hover_color,
      	'icon_three_d' => $settings->icon_three_d,
 
      	/* Image Colors */
      	'img_bg_color' => $settings->img_bg_color,
      	'img_bg_color_opc' => $settings->img_bg_color_opc,
      	'img_bg_hover_color' => $settings->img_bg_hover_color,
      	'img_bg_hover_color_opc' => $settings->img_bg_hover_color_opc,
      	'img_border_color' => $settings->img_border_color,
      	'img_border_hover_color' => $settings->img_border_hover_color,
 	);
 
 	/* CSS Render Function */ 
 	FLBuilder::render_module_css( 'image-icon', $id, $imageicon_array );
}
?>

<?php if( $settings->icon_style == 'simple' ) : ?>
	.fl-node-<?php echo $id; ?> .uabb-icon-wrap .uabb-icon i {
		<?php if( $settings->img_icon_position == 'above-title' || $settings->img_icon_position == 'below-title' ) : ?>
		width: auto;
		<?php endif; ?>
		<?php if( $settings->img_icon_position == 'right' || $settings->img_icon_position == 'right-title' ) : ?>
		direction: rtl;
		<?php endif; ?>
	}
<?php endif; ?>

<?php
/* Code Can Be Removed */
/*if ( $settings->image_type == 'icon' ) { ?>
	<?php if( $settings->img_icon_position == 'left-title' || $settings->img_icon_position == 'right-title'  ) {
 
.fl-node-<?php echo $id; ?> .infobox .uabb-icon-wrap .uabb-icon i, 
.fl-node-<?php echo $id; ?> .infobox .uabb-icon-wrap .uabb-icon i:before {
    text-align: <?php echo ( $settings->img_icon_position == 'left-title' ) ? 'left' : 'right'; ?>;
}

	}
	if( $settings->img_icon_position == 'above-title' || $settings->img_icon_position == 'below-title'  ) {

.fl-node-<?php echo $id; ?> .infobox .uabb-icon-wrap .uabb-icon i, 
.fl-node-<?php echo $id; ?> .infobox .uabb-icon-wrap .uabb-icon i:before {
    text-align: <?php echo $settings->align; ?>;
}

	}
}*/
?>


<?php
if( $settings->image_type != 'none' ) {
	if( $settings->img_icon_position != 'above-title' && $settings->img_icon_position != 'below-title' ) {
		if( $settings->align_items == 'center' ) {
?>
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-imgicon-wrap {
	vertical-align: middle;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-content {
	vertical-align: middle;
}
<?php
		} else {
?>
.fl-node-<?php echo $id; ?> .infobox-icon-left-title .uabb-imgicon-wrap,
.fl-node-<?php echo $id; ?> .infobox-icon-right-title .uabb-imgicon-wrap,
.fl-node-<?php echo $id; ?> .infobox-photo-left-title .uabb-imgicon-wrap,
.fl-node-<?php echo $id; ?> .infobox-photo-right-title .uabb-imgicon-wrap {
	vertical-align: top;
}
<?php
		}
	}
}
?>

/* Image icon Margin 0 */
<?php if ( $settings->title == '' && $settings->text == '' ) { ?>
	.fl-node-<?php echo $id; ?> .uabb-imgicon-wrap {
		margin: 0;
	}
<?php } ?>

/* Border Properties */
<?php if($settings->uabb_border_type != 'none') : ?>
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
			border-style: <?php echo $settings->uabb_border_type; ?>;
			
			<?php if( $settings->uabb_border_color != "" ) : ?>
				border-color: <?php echo $settings->uabb_border_color; ?>;
			<?php endif; ?>
			
			border-top-width: <?php echo  $settings->uabb_border_top ? $settings->uabb_border_top : '0'; ?>px;
			border-bottom-width: <?php echo $settings->uabb_border_bottom ? $settings->uabb_border_bottom : '0'; ?>px;
			border-left-width: <?php echo $settings->uabb_border_left ? $settings->uabb_border_left : '0'; ?>px;
			border-right-width: <?php echo $settings->uabb_border_right ? $settings->uabb_border_right : '0'; ?>px;
			
			<?php if( $settings->uabb_border_top > 0 || $settings->uabb_border_bottom > 0 || $settings->uabb_border_left > 0 || $settings->uabb_border_right > 0 ) : ?>

					<?php 
					if( $converted === 'yes' ||  isset($settings->info_box_padding_dimension_top) && isset( $settings->info_box_padding_dimension_bottom ) && isset( $settings->info_box_padding_dimension_left )   && isset( $settings->info_box_padding_dimension_right ) ) { 
				        if(isset($settings->info_box_padding_dimension_top) ){
				            echo ( $settings->info_box_padding_dimension_top != '' ) ? 'padding-top:'.$settings->info_box_padding_dimension_top.'px;' : 'padding-top: 20px;'; 
				        }
				        if(isset($settings->info_box_padding_dimension_bottom) ){
				            echo ( $settings->info_box_padding_dimension_bottom != '' ) ? 'padding-bottom:'.$settings->info_box_padding_dimension_bottom.'px;' : 'padding-bottom: 20px;';
				        }
				        if(isset($settings->info_box_padding_dimension_left) ){
				            echo ( $settings->info_box_padding_dimension_left != '' ) ? 'padding-left:'.$settings->info_box_padding_dimension_left.'px;' : 'padding-left: 20px;';
				        }
				        if(isset($settings->info_box_padding_dimension_right) ){
				            echo ( $settings->info_box_padding_dimension_right != '' ) ? 'padding-right:'.$settings->info_box_padding_dimension_right.'px;' : 'padding-right: 20px;';
				        }
					} else if( isset( $settings->info_box_padding ) && $settings->info_box_padding != '' && isset( $settings->info_box_padding_dimension_top ) && $settings->info_box_padding_dimension_top == '' && isset( $settings->info_box_padding_dimension_bottom ) && $settings->info_box_padding_dimension_bottom == '' && isset( $settings->info_box_padding_dimension_left ) && $settings->info_box_padding_dimension_left == '' && isset( $settings->info_box_padding_dimension_right ) && $settings->info_box_padding_dimension_right == '' ) { ?>
							<?php echo $settings->info_box_padding; ?>;
				 <?php } ?>

			<?php endif; ?>
		}
	
<?php endif; ?>

/* Background Property */
<?php if ( !empty( $settings->bg_type ) ) { ?>
	.fl-node-<?php echo $id; ?> .uabb-infobox { 
	<?php if( $settings->bg_type == 'color' && !empty($settings->bg_color) ) : ?>
			background: <?php echo $settings->bg_color; ?>;

			<?php 
			if( $converted === 'yes' || $settings->info_box_padding_dimension_top != '' && isset($settings->info_box_padding_dimension_top) && isset( $settings->info_box_padding_dimension_bottom ) && $settings->info_box_padding_dimension_bottom != ''  && isset( $settings->info_box_padding_dimension_left ) && $settings->info_box_padding_dimension_left != ''  && isset( $settings->info_box_padding_dimension_right ) && $settings->info_box_padding_dimension_right != '' ) { 
		        if(isset($settings->info_box_padding_dimension_top) ){
		            echo ( $settings->info_box_padding_dimension_top != '' ) ? 'padding-top:'.$settings->info_box_padding_dimension_top.'px;' : 'padding-top: 20px;'; 
		        }
		        if(isset($settings->info_box_padding_dimension_bottom) ){
		            echo ( $settings->info_box_padding_dimension_bottom != '' ) ? 'padding-bottom:'.$settings->info_box_padding_dimension_bottom.'px;' : 'padding-bottom: 20px;';
		        }
		        if(isset($settings->info_box_padding_dimension_left) ){
		            echo ( $settings->info_box_padding_dimension_left != '' ) ? 'padding-left:'.$settings->info_box_padding_dimension_left.'px;' : 'padding-left: 20px;';
		        }
		        if(isset($settings->info_box_padding_dimension_right) ){
		            echo ( $settings->info_box_padding_dimension_right != '' ) ? 'padding-right:'.$settings->info_box_padding_dimension_right.'px;' : 'padding-right: 20px;';
		        }
			} else if( isset( $settings->info_box_padding ) && $settings->info_box_padding != '' && isset( $settings->info_box_padding_dimension_top ) && ( $settings->info_box_padding_dimension_top == '' || $settings->info_box_padding_dimension_top == '0' ) && isset( $settings->info_box_padding_dimension_bottom ) && ( $settings->info_box_padding_dimension_bottom == '' || $settings->info_box_padding_dimension_bottom == '0' ) && isset( $settings->info_box_padding_dimension_left ) && ( $settings->info_box_padding_dimension_left == '' || $settings->info_box_padding_dimension_left == '0' ) && isset( $settings->info_box_padding_dimension_right ) && ( $settings->info_box_padding_dimension_right == '' || $settings->info_box_padding_dimension_right == '0' ) ) { ?>
					<?php echo $settings->info_box_padding; ?>;
			<?php } ?>

	<?php elseif( $settings->bg_type == 'gradient' ) : ?>
		<?php if( $settings->bg_gradient['color_one'] != '' && $settings->bg_gradient['color_two'] != '' ) : ?>
			
			<?php 
			if( $converted === 'yes' || $settings->info_box_padding_dimension_top != '' && isset($settings->info_box_padding_dimension_top) && isset( $settings->info_box_padding_dimension_bottom ) && $settings->info_box_padding_dimension_bottom != ''  && isset( $settings->info_box_padding_dimension_left ) && $settings->info_box_padding_dimension_left != ''  && isset( $settings->info_box_padding_dimension_right ) && $settings->info_box_padding_dimension_right != '' ) { 
		        if(isset($settings->info_box_padding_dimension_top) ){
		            echo ( $settings->info_box_padding_dimension_top != '' ) ? 'padding-top:'.$settings->info_box_padding_dimension_top.'px;' : 'padding-top: 20;'; 
		        }
		        if(isset($settings->info_box_padding_dimension_bottom) ){
		            echo ( $settings->info_box_padding_dimension_bottom != '' ) ? 'padding-bottom:'.$settings->info_box_padding_dimension_bottom.'px;' : 'padding-bottom: 20;';
		        }
		        if(isset($settings->info_box_padding_dimension_left) ){
		            echo ( $settings->info_box_padding_dimension_left != '' ) ? 'padding-left:'.$settings->info_box_padding_dimension_left.'px;' : 'padding-left: 20;';
		        }
		        if(isset($settings->info_box_padding_dimension_right) ){
		            echo ( $settings->info_box_padding_dimension_right != '' ) ? 'padding-right:'.$settings->info_box_padding_dimension_right.'px;' : 'padding-right: 20;';
		        }
			} else if( isset( $settings->info_box_padding ) && $settings->info_box_padding != '' && isset( $settings->info_box_padding_dimension_top ) && $settings->info_box_padding_dimension_top == '' && isset( $settings->info_box_padding_dimension_bottom ) && $settings->info_box_padding_dimension_bottom == '' && isset( $settings->info_box_padding_dimension_left ) && $settings->info_box_padding_dimension_left == '' && isset( $settings->info_box_padding_dimension_right ) && $settings->info_box_padding_dimension_right == '' ) { ?>
					<?php echo $settings->info_box_padding; ?>;
			<?php } ?>

		<?php endif; ?>
		<?php UABB_Helper::uabb_gradient_css( $settings->bg_gradient ); ?>
	<?php endif; ?>
	}
<?php } ?>


/* Align */
.fl-node-<?php echo $id; ?> .infobox-<?php echo $settings->align; ?> {
	text-align: <?php echo $settings->align; ?>;
}

/* Minimum Height and Vertical Alignment */	
<?php if( $settings->min_height_switch == 'custom' && $settings->min_height != "" ) : ?>
.fl-node-<?php echo $id; ?> .uabb-infobox {
	
	min-height: <?php echo $settings->min_height; ?>px;
	display: flex;
	align-items: <?php echo $settings->vertical_align; ?>;
}

.fl-node-<?php echo $id; ?> .infobox-<?php echo $settings->align; ?> {
	justify-content: <?php echo ( $settings->align == 'center' ) ? 'center' : ( ( $settings->align == 'left' ) ? 'flex-start' : 'flex-end' )  ?>;
}

<?php endif; ?>

/* Heading Margin Properties */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-title {
	margin-top: <?php echo $settings->heading_margin_top; ?>px;
	margin-bottom: <?php echo ( $settings->heading_margin_bottom !== '' ) ? $settings->heading_margin_bottom : '10' ; ?>px;
}

/* Prefix Margin Properties */
<?php if ( $settings->prefix_margin_top !== '' ) { ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-title-prefix {
	margin-top: <?php echo $settings->prefix_margin_top; ?>px;
}
<?php } ?>

/* Heading Color */
<?php if( !empty($settings->title_color) ) : ?> 
.fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title, 
.fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title span a,
.fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title * {
	color: <?php echo $settings->title_color; ?>
}
<?php endif; ?>

.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-text {
	margin-top: <?php echo ( trim( $settings->content_margin_top ) !== '' ) ? $settings->content_margin_top : '0'; ?>px;
	margin-bottom: <?php echo ( trim( $settings->content_margin_bottom ) !== '' ) ? $settings->content_margin_bottom : '0'; ?>px;
}

/* Description Color */
 
.fl-node-<?php echo $id; ?> .uabb-infobox-text {
	color: <?php echo uabb_theme_text_color( $settings->subhead_color ); ?>;
}

/* Icon Margin */
<?php if ( $settings->image_type == 'icon' ) { ?>
	<?php $pos = $settings->img_icon_position;
	if ( $pos == 'above-title' || $pos == 'below-title' || $pos == 'left' || $pos == 'right' ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-imgicon-wrap {
		margin-top: <?php echo ( $settings->img_icon_margin_top != '' ) ? $settings->img_icon_margin_top : '5'; ?>px;
		margin-bottom: <?php echo ( $settings->img_icon_margin_bottom != '' ) ? $settings->img_icon_margin_bottom : '0'; ?>px;
	}
	<?php } ?>
<?php } ?>
/* Image Margin */
<?php if ( $settings->image_type == 'photo' ) { ?>
	<?php $pos = $settings->img_icon_position;
	if ( $pos == 'above-title' || $pos == 'below-title' || $pos == 'left' || $pos == 'right' ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-imgicon-wrap {
		margin-top: <?php echo ( $settings->img_icon_margin_top != '' ) ? $settings->img_icon_margin_top : '5'; ?>px;
		margin-bottom: <?php echo ( $settings->img_icon_margin_bottom != '' ) ? $settings->img_icon_margin_bottom : '0'; ?>px;
	}
	<?php } ?>
<?php } ?>


<?php if( $settings->cta_type == 'button' ) { ?>
/* Button Margin */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-button {
	margin-top: <?php echo ( trim( $settings->btn_margin_top ) !== '' ) ? $settings->btn_margin_top : '10'; ?>px;
	margin-bottom: <?php echo ( trim( $settings->btn_margin_bottom ) !== '' ) ? $settings->btn_margin_bottom : '0'; ?>px;
}
<?php } ?>

<?php if( $settings->cta_type == 'link' ) { ?>
/* Link Text Margin */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-cta-link {
	margin-top: <?php echo ( trim($settings->link_margin_top) != '' ) ? $settings->link_margin_top : '0'; ?>px;
	margin-bottom: <?php echo ( trim($settings->link_margin_bottom) != '' ) ? $settings->link_margin_bottom : '0'; ?>px;

	<?php if ( $settings->link_margin_top != '' || $settings->link_margin_bottom != '' ) { ?>
	display:block;
	<?php } ?>
}
<?php } ?>

/* Link Color */
<?php if( !empty($settings->link_color) ) : ?> 
.fl-builder-content .fl-node-<?php echo $id; ?> a,
.fl-builder-content .fl-node-<?php echo $id; ?> a *,
.fl-builder-content .fl-node-<?php echo $id; ?> a:visited {
	color: <?php echo uabb_theme_text_color( $settings->link_color ); ?>;
}
<?php endif; ?>

/* Typography Options for Title */
.fl-builder-content .fl-node-<?php echo $id; ?> <?php /*echo $settings->title_tag_selection;*/ ?>.uabb-infobox-title,
.fl-builder-content .fl-node-<?php echo $id; ?> <?php /*echo $settings->title_tag_selection;*/ ?>.uabb-infobox-title a {
	
	<?php if( $settings->title_font_family['family'] != "Default") : ?>
		<?php UABB_Helper::uabb_font_css( $settings->title_font_family ); ?>
	<?php endif; ?>

    <?php if( $converted === 'yes' || isset( $settings->title_font_size_unit ) && $settings->title_font_size_unit != '' ) { ?>
     	font-size: <?php echo $settings->title_font_size_unit; ?>px;
		<?php if( $settings->title_line_height_unit == '' &&  $settings->title_font_size_unit != '' ){ ?>
			line-height: <?php echo $settings->title_font_size_unit + 2 ; ?>px;
	    <?php } ?>		
    <?php } else if( isset( $settings->title_font_size_unit ) && $settings->title_font_size_unit == '' && isset( $settings->title_font_size['desktop'] ) && $settings->title_font_size['desktop'] != '' ) { ?>
	    font-size: <?php echo $settings->title_font_size['desktop']; ?>px;
	    line-height: <?php echo $settings->title_font_size['desktop'] + 2 ; ?>px;
	<?php } ?> 

	<?php if( isset( $settings->title_font_size['desktop'] ) && $settings->title_font_size['desktop'] == '' && isset( $settings->title_line_height['desktop'] ) && $settings->title_line_height['desktop'] != '' && $settings->title_line_height_unit == '' ) { ?>
	    line-height: <?php echo $settings->title_line_height['desktop']; ?>px;
	<?php } ?>
    
    <?php if( $converted === 'yes' || isset( $settings->title_line_height_unit ) && $settings->title_line_height_unit != '' ) { ?>
    	line-height: <?php echo $settings->title_line_height_unit; ?>em;	
    <?php }  else if( isset( $settings->title_line_height_unit ) && $settings->title_line_height_unit == '' && isset( $settings->title_line_height['desktop'] ) && $settings->title_line_height['desktop'] != '' ) { ?>
	    line-height: <?php echo $settings->title_line_height['desktop']; ?>px;
	<?php } ?>  

	<?php if( $settings->title_transform != '' ) ?>
	   text-transform: <?php echo $settings->title_transform; ?>;

    <?php if( $settings->title_letter_spacing != '' ) ?>
	   letter-spacing: <?php echo $settings->title_letter_spacing; ?>px;	 

}

/* Typography Options for Description */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-text {
	
	<?php if( $settings->subhead_font_family['family'] != "Default") : ?>
		<?php UABB_Helper::uabb_font_css( $settings->subhead_font_family ); ?>
	<?php endif; ?>

    <?php if( $converted === 'yes' || isset( $settings->subhead_font_size_unit ) && $settings->subhead_font_size_unit != '' ) { ?>
     	font-size: <?php echo $settings->subhead_font_size_unit; ?>px;
		<?php if( $settings->subhead_line_height_unit == '' &&  $settings->subhead_font_size_unit != '' ){ ?>
			line-height: <?php echo $settings->subhead_font_size_unit + 2 ; ?>px;
	    <?php } ?>		
    <?php } else if( isset( $settings->subhead_font_size_unit ) && $settings->subhead_font_size_unit == '' && isset( $settings->subhead_font_size['desktop'] ) && $settings->subhead_font_size['desktop'] != '' ) { ?>
	    font-size: <?php echo $settings->subhead_font_size['desktop']; ?>px;
	    line-height: <?php echo $settings->subhead_font_size['desktop'] + 2 ; ?>px;
	<?php } ?> 

	<?php if( isset( $settings->subhead_font_size['desktop'] ) && $settings->subhead_font_size['desktop'] == '' && isset( $settings->subhead_line_height['desktop'] ) && $settings->subhead_line_height['desktop'] != '' && $settings->subhead_line_height_unit == '' ) { ?>
	    line-height: <?php echo $settings->subhead_line_height['desktop']; ?>px;
	<?php } ?>

    <?php if( $converted === 'yes' || isset( $settings->subhead_line_height_unit ) && $settings->subhead_line_height_unit != '' ) { ?>
    	line-height: <?php echo $settings->subhead_line_height_unit; ?>em;	
    <?php }  else if( isset( $settings->subhead_line_height_unit ) && $settings->subhead_line_height_unit == '' && isset( $settings->subhead_line_height['desktop'] ) && $settings->subhead_line_height['desktop'] != '' ) { ?>
	    line-height: <?php echo $settings->subhead_line_height['desktop']; ?>px;
	<?php } ?> 

	<?php if( $settings->subhead_transform != '' )?>
	   text-transform: <?php echo $settings->subhead_transform; ?>;

    <?php if( $settings->subhead_letter_spacing != '' )?>
	   letter-spacing: <?php echo $settings->subhead_letter_spacing; ?>px;	
}

/* Typography Options for Prefix */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-title-prefix {
	
	<?php if( $settings->prefix_font_family['family'] != "Default") : ?>
		<?php UABB_Helper::uabb_font_css( $settings->prefix_font_family ); ?>
	<?php endif; ?>

    <?php if( $converted === 'yes' || isset( $settings->prefix_font_size_unit ) && $settings->prefix_font_size_unit != '' ) { ?>
     	font-size: <?php echo $settings->prefix_font_size_unit; ?>px;
		<?php if( $settings->prefix_line_height_unit == '' &&  $settings->prefix_font_size_unit != '' ){ ?>
			line-height: <?php echo $settings->prefix_font_size_unit + 2 ; ?>px;
	    <?php } ?>		
    <?php } else if( isset( $settings->prefix_font_size_unit ) && $settings->prefix_font_size_unit == '' && isset( $settings->prefix_font_size['desktop'] ) && $settings->prefix_font_size['desktop'] != '' ) { ?>
	    font-size: <?php echo $settings->prefix_font_size['desktop']; ?>px;
	    line-height: <?php echo $settings->prefix_font_size['desktop'] + 2 ; ?>px;
	<?php } ?>

	<?php if( isset( $settings->prefix_font_size['desktop'] ) && $settings->prefix_font_size['desktop'] == '' && isset( $settings->prefix_line_height['desktop'] ) && $settings->prefix_line_height['desktop'] != '' && $settings->prefix_line_height_unit == '' ) { ?>
	    line-height: <?php echo $settings->prefix_line_height['desktop']; ?>px;
	<?php } ?>

    <?php if( $converted === 'yes' || isset( $settings->prefix_line_height_unit ) && $settings->prefix_line_height_unit != '' ) { ?>
    	line-height: <?php echo $settings->prefix_line_height_unit; ?>em;	
    <?php }  else if( isset( $settings->prefix_line_height_unit ) && $settings->prefix_line_height_unit == '' && isset( $settings->prefix_line_height['desktop'] ) && $settings->prefix_line_height['desktop'] != '' ) { ?>
	    line-height: <?php echo $settings->prefix_line_height['desktop']; ?>px;
	<?php } ?> 

	<?php if( $settings->prefix_color != '' ) : ?>
	color: <?php echo $settings->prefix_color; ?>;
	<?php endif; ?>

	<?php if( $settings->prefix_transform != '' ) ?>
	   text-transform: <?php echo $settings->prefix_transform; ?>;

    <?php if( $settings->prefix_letter_spacing != '' ) ?>
	   letter-spacing: <?php echo $settings->prefix_letter_spacing; ?>px;
}

/* Typography Options for Link Text */
.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-cta-link {
	<?php if( $settings->link_font_family['family'] != "Default") : ?>
		<?php UABB_Helper::uabb_font_css( $settings->link_font_family ); ?>
	<?php endif; ?>

    <?php if( $converted === 'yes' || isset( $settings->link_font_size_unit ) && $settings->link_font_size_unit != '' ) { ?>
     	font-size: <?php echo $settings->link_font_size_unit; ?>px;
		<?php if( $settings->link_line_height_unit == '' &&  $settings->link_font_size_unit != '' ){ ?>
			line-height: <?php echo $settings->link_font_size_unit + 2 ; ?>px;
	    <?php } ?>		
    <?php } else if( isset( $settings->link_font_size_unit ) && $settings->link_font_size_unit == '' && isset( $settings->link_font_size['desktop'] ) && $settings->link_font_size['desktop'] != '' ) { ?>
	    font-size: <?php echo $settings->link_font_size['desktop']; ?>px;
	    line-height: <?php echo $settings->link_font_size['desktop'] + 2 ; ?>px;
	<?php } ?>

	<?php if( isset( $settings->link_font_size['desktop'] ) && $settings->link_font_size['desktop'] == '' && isset( $settings->link_line_height['desktop'] ) && $settings->link_line_height['desktop'] != '' && $settings->link_line_height_unit == '' ) { ?>
	    line-height: <?php echo $settings->link_line_height['desktop']; ?>px;
	<?php } ?>

    <?php if( $converted === 'yes' || isset( $settings->link_line_height_unit ) && $settings->link_line_height_unit != '' ) { ?>
    	line-height: <?php echo $settings->link_line_height_unit; ?>em;	
    <?php }  else if( isset( $settings->link_line_height_unit ) && $settings->link_line_height_unit == '' && isset( $settings->link_line_height['desktop'] ) && $settings->link_line_height['desktop'] != '' ) { ?>
	    line-height: <?php echo $settings->link_line_height['desktop']; ?>px;
	<?php } ?>

	<?php if( $settings->link_transform != '' ) ?>
	   text-transform: <?php echo $settings->link_transform; ?>;

    <?php if( $settings->link_letter_spacing != '' ) ?>
	   letter-spacing: <?php echo $settings->link_letter_spacing; ?>px;

}

/* Module Link */
<?php  if( $settings->cta_type == 'module' && !empty($settings->link) ) { ?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
		position: relative; 
	}
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap i,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap i:before,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap i,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap i:before {
		color : <?php echo $settings->icon_hover_color; ?>;
	}
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap i,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap i {
		background-color: <?php echo $settings->icon_bg_hover_color; ?>;

		<?php if($settings->icon_three_d && !empty($settings->icon_bg_hover_color)) : // 3D Styles 

		$bg_hover_color = ( !empty($settings->icon_bg_hover_color) ) ? uabb_parse_color_to_hex( $settings->icon_bg_hover_color ) : '' ;
		$bg_hover_grad_start = '#'.FLBuilderColor::adjust_brightness($bg_hover_color, 40, 'lighten');
		$border_hover_color = '#'.FLBuilderColor::adjust_brightness($bg_hover_color, 20, 'darken'); ?>

		background: -moz-linear-gradient(top,  <?php echo $bg_hover_grad_start; ?> 0%, <?php echo $settings->icon_bg_hover_color; ?> 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $bg_hover_grad_start; ?>), color-stop(100%,<?php echo $settings->icon_bg_hover_color; ?>)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  <?php echo $bg_hover_grad_start; ?> 0%,<?php echo $settings->icon_bg_hover_color; ?> 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  <?php echo $bg_hover_grad_start; ?> 0%,<?php echo $settings->icon_bg_hover_color; ?> 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  <?php echo $bg_hover_grad_start; ?> 0%,<?php echo $settings->icon_bg_hover_color; ?> 100%); /* IE10+ */
		background: linear-gradient(to bottom,  <?php echo $bg_hover_grad_start; ?> 0%,<?php echo $settings->icon_bg_hover_color; ?> 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $bg_hover_grad_start; ?>', endColorstr='<?php echo $settings->icon_bg_hover_color; ?>',GradientType=0 ); /* IE6-9 */
		<?php if($settings->icon_style == 'circle' || $settings->icon_style == 'square') : ?>
		    border: 1px solid <?php echo $border_hover_color; ?>;
		<?php endif; ?>    
		<?php endif; ?>

		<?php if($settings->icon_style == 'custom' && $settings->icon_border_style != 'none') : ?>
            <?php if( !empty( $settings->icon_border_color ) ) : ?>
                border-color: <?php echo $settings->icon_border_hover_color; ?>;
            <?php endif; ?>
        <?php endif; ?>
	}

	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap img,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap img:before,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap img,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap img:before {
		background-color: <?php echo $settings->img_bg_hover_color; ?>;
	}
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-infobox-content .uabb-imgicon-wrap .uabb-image-content,
	.fl-node-<?php echo $id; ?> .uabb-infobox-module-link:hover ~ .uabb-imgicon-wrap .uabb-image-content {
		<?php if( !empty( $settings->img_border_hover_color ) ): ?>
            border-color: <?php echo $settings->img_border_hover_color; ?>;
        <?php endif; ?>
	}
<?php } ?>


/* Calculation Width */
<?php 	$class 		= '';
		$pos 		= '';
		$cal_width 	= '';
if( $settings->image_type == 'icon' ) { 
	/*$class = 'uabb-number-'.$settings->image_type.'-'.$settings->icon_position;*/
	$class = 'infobox-icon-' . $settings->img_icon_position;
	$pos = $settings->img_icon_position;
	if ( $pos == 'left' || $pos == 'right' || $pos == 'left-title' || $pos == 'right-title') {
		$cal_width = $settings->icon_size;
		if ( $pos == 'left' || $pos == 'right' || $pos == 'left-title' || $pos == 'right-title' ) {
            $cal_width = $settings->icon_size;
        }
		if ( $settings->icon_style != 'simple' ) {
			$cal_width = $settings->icon_size * 2;
			if ( $settings->icon_style == 'custom' ) {
				$cal_width = $settings->icon_size + intval($settings->icon_bg_size);
				if ( $settings->icon_border_style != 'none' ) {
					$cal_width = $cal_width + ( intval($settings->icon_border_width) * 2 );
				}
			}
		}
		$cal_width = $cal_width + 20;
	}

}elseif ( $settings->image_type == 'photo' ) {
	/*$class = 'uabb-number-'.$settings->image_type.'-'.$settings->image_position;*/
	$class = 'infobox-photo-' . $settings->img_icon_position;
	$pos = $settings->img_icon_position;
	if ( $pos == 'left' || $pos == 'right' || $pos == 'left-title' || $pos == 'right-title' ) {
		$cal_width = $settings->img_size;
		if ( $settings->image_style == 'custom' ) {
			$cal_width = $cal_width + intval($settings->img_bg_size) * 2;
			if ( $settings->img_border_style != 'none' ) {
				$cal_width = $cal_width + ( intval($settings->img_border_width) * 2 );
			}
		}
		$cal_width = $cal_width + 20;
	}
}
?>

/* Left Right Title Image */
<?php if ( $pos == 'left' || $pos == 'right' ) { ?>
	.fl-node-<?php echo $id; ?> .uabb-infobox {
		text-align: <?php echo $pos; ?>;
	}
	.fl-builder-content .fl-node-<?php echo $id; ?> .<?php echo $class; ?> .uabb-infobox-content{
		width: calc(100% - <?php echo $cal_width; ?>px);
	}
<?php }elseif( $pos == 'left-title' || $pos == 'right-title' ) { ?>
	.fl-node-<?php echo $id; ?> .uabb-infobox {
		text-align: <?php echo ( $pos == 'left-title' ) ? 'left' : 'right'; ?>;
	}
	.fl-builder-content .fl-node-<?php echo $id; ?> .<?php echo $class; ?> .uabb-infobox-title-wrap {
		width: calc(100% - <?php echo $cal_width; ?>px);
		display: inline-block;
	}
<?php } ?>

/* Responsive CSS */
<?php if($global_settings->responsive_enabled) { ?>

<?php
if( $settings->uabb_border_type != 'none' || $settings->bg_type != 'none' ) {
	if( $settings->medium_border == 'yes' ) : ?>
	<?php echo '@media (min-width: '. $global_settings->responsive_breakpoint .'px) and (max-width: '. $global_settings->medium_breakpoint .'px) { '; ?>

		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
			border: none;
			<?php echo ( $settings->bg_type != 'none' ) ? '' : 'padding: 0;'; ?>
		}
	}	
	<?php
	endif;
}
?>
	

<?php echo '@media (max-width: '. $global_settings->medium_breakpoint .'px) { '; ?>
    
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
			<?php 
		        if(isset($settings->info_box_padding_dimension_top_medium) ){
		            echo ( $settings->info_box_padding_dimension_top_medium != '' ) ? 'padding-top:'.$settings->info_box_padding_dimension_top_medium.'px;' : ''; 
		        }
		        if(isset($settings->info_box_padding_dimension_bottom_medium) ){
		            echo ( $settings->info_box_padding_dimension_bottom_medium != '' ) ? 'padding-bottom:'.$settings->info_box_padding_dimension_bottom_medium.'px;' : '';
		        }
		        if(isset($settings->info_box_padding_dimension_left_medium) ){
		            echo ( $settings->info_box_padding_dimension_left_medium != '' ) ? 'padding-left:'.$settings->info_box_padding_dimension_left_medium.'px;' : ';';
		        }
		        if(isset($settings->info_box_padding_dimension_right_medium) ){
		            echo ( $settings->info_box_padding_dimension_right_medium != '' ) ? 'padding-right:'.$settings->info_box_padding_dimension_right_medium.'px;' : '';
		        } 
		    ?>
		}
	
	.fl-builder-content .fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title,
	.fl-builder-content .fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title a {

	    <?php if( $converted === 'yes' || isset( $settings->title_font_size_unit_medium ) && $settings->title_font_size_unit_medium != '' ){ ?>
	    	font-size: <?php echo $settings->title_font_size_unit_medium; ?>px;
			<?php if( $settings->title_line_height_unit_medium == '' && $settings->title_font_size_unit_medium != '' ) { ?>
				line-height: <?php $settings->title_font_size_unit_medium + 2?>px;
			<?php } ?>	
	    <?php } else if(  isset( $settings->title_font_size_unit_medium ) && $settings->title_font_size_unit_medium == '' && isset( $settings->title_font_size['medium'] ) && $settings->title_font_size['medium'] != '' ) {?>
	    	font-size: <?php echo $settings->title_font_size['medium']; ?>px;
		    line-height: <?php $settings->title_font_size['medium'] + 2?>px;
	    <?php } ?>  

		<?php if( isset( $settings->title_font_size['medium'] ) && $settings->title_font_size['medium'] == '' && isset( $settings->title_line_height['medium'] ) && $settings->title_line_height['medium'] != '' && $settings->title_line_height_unit == '' && $settings->title_line_height_unit_medium == '' ) { ?>
		    line-height: <?php echo $settings->title_line_height['medium']; ?>px;
		<?php } ?>
    
        <?php if( $converted === 'yes' || isset( $settings->title_line_height_unit_medium ) && $settings->title_line_height_unit_medium != '' ){ ?>
        	line-height: <?php echo $settings->title_line_height_unit_medium; ?>em;	
        <?php } else if( isset( $settings->title_line_height_unit_medium )&& $settings->title_line_height_unit_medium == '' && isset( $settings->title_line_height['medium'] ) && $settings->title_line_height['medium'] != '' ) {?>
        	line-height: <?php echo $settings->title_line_height['medium']; ?>px;
        <?php } ?>  
	}

	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-text {	

	    <?php if( $converted === 'yes' || isset( $settings->subhead_font_size_unit_medium ) && $settings->subhead_font_size_unit_medium != '' ){ ?>
	    	font-size: <?php echo $settings->subhead_font_size_unit_medium; ?>px;
			<?php if( $settings->subhead_line_height_unit_medium == '' && $settings->subhead_font_size_unit_medium != '' ) { ?>
				line-height: <?php $settings->subhead_font_size_unit_medium + 2?>px;
			<?php } ?>	
	    <?php } else if(  isset( $settings->subhead_font_size_unit_medium ) && $settings->subhead_font_size_unit_medium == '' && isset( $settings->subhead_font_size['medium'] ) && $settings->subhead_font_size['medium'] != '' ) {?>
	    	font-size: <?php echo $settings->subhead_font_size['medium']; ?>px;
		    line-height: <?php $settings->subhead_font_size['medium'] + 2?>px;
	    <?php } ?>   

		<?php if( isset( $settings->subhead_font_size['medium'] ) && $settings->subhead_font_size['medium'] == '' && isset( $settings->subhead_line_height['medium'] ) && $settings->subhead_line_height['medium'] != '' && $settings->subhead_line_height_unit == '' && $settings->subhead_line_height_unit_medium == '' ) { ?>
		    line-height: <?php echo $settings->subhead_line_height['medium']; ?>px;
		<?php } ?>

        <?php if( $converted === 'yes' || isset( $settings->subhead_line_height_unit_medium ) && $settings->subhead_line_height_unit_medium != '' ){ ?>
        	line-height: <?php echo $settings->subhead_line_height_unit_medium; ?>em;	
        <?php } else if( isset( $settings->subhead_line_height_unit_medium )&& $settings->subhead_line_height_unit_medium == '' && isset( $settings->subhead_line_height['medium'] ) && $settings->subhead_line_height['medium'] != '' ) {?>
        	line-height: <?php echo $settings->subhead_line_height['medium']; ?>px;
        <?php } ?>    
	}

	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-title-prefix {

	    <?php if( $converted === 'yes' || isset( $settings->prefix_font_size_unit_medium ) && $settings->prefix_font_size_unit_medium != '' ){ ?>
	    	font-size: <?php echo $settings->prefix_font_size_unit_medium; ?>px;
			<?php if( $settings->prefix_line_height_unit_medium == '' && $settings->prefix_font_size_unit_medium != '' ) { ?>
				line-height: <?php $settings->prefix_font_size_unit_medium + 2?>px;
			<?php } ?>	
	    <?php } else if(  isset( $settings->prefix_font_size_unit_medium ) && $settings->prefix_font_size_unit_medium == '' && isset( $settings->prefix_font_size['medium'] ) && $settings->prefix_font_size['medium'] != '' ) {?>
	    	font-size: <?php echo $settings->prefix_font_size['medium']; ?>px;
		    line-height: <?php $settings->prefix_font_size['medium'] + 2?>px;
	    <?php } ?>

		<?php if( isset( $settings->prefix_font_size['medium'] ) && $settings->prefix_font_size['medium'] == '' && isset( $settings->prefix_line_height['medium'] ) && $settings->prefix_line_height['medium'] != '' && $settings->prefix_line_height_unit == '' && $settings->prefix_line_height_unit_medium == '' ) { ?>
		    line-height: <?php echo $settings->prefix_line_height['medium']; ?>px;
		<?php } ?>
    
        <?php if( $converted === 'yes' || isset( $settings->prefix_line_height_unit_medium ) && $settings->prefix_line_height_unit_medium != '' ){ ?>
        	line-height: <?php echo $settings->prefix_line_height_unit_medium; ?>em;	
        <?php } else if( isset( $settings->prefix_line_height_unit_medium )&& $settings->prefix_line_height_unit_medium == '' && isset( $settings->prefix_line_height['medium'] ) && $settings->prefix_line_height['medium'] != '' ) {?>
        	line-height: <?php echo $settings->prefix_line_height['medium']; ?>px;
        <?php } ?>

	}

	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-cta-link {

	    <?php if( $converted === 'yes' || isset( $settings->link_font_size_unit_medium ) && $settings->link_font_size_unit_medium != '' ){ ?>
	    	font-size: <?php echo $settings->link_font_size_unit_medium; ?>px;
			<?php if( $settings->link_line_height_unit_medium == '' && $settings->link_font_size_unit_medium != '' ) { ?>
				line-height: <?php $settings->link_font_size_unit_medium + 2?>px;
			<?php } ?>	
	    <?php } else if(  isset( $settings->link_font_size_unit_medium ) && $settings->link_font_size_unit_medium == '' && isset( $settings->link_font_size['medium'] ) && $settings->link_font_size['medium'] != '' ) {?>
	    	font-size: <?php echo $settings->link_font_size['medium']; ?>px;
		    line-height: <?php $settings->link_font_size['medium'] + 2?>px;
	    <?php } ?>  

		<?php if( isset( $settings->link_font_size['medium'] ) && $settings->link_font_size['medium'] == '' && isset( $settings->link_line_height['medium'] ) && $settings->link_line_height['medium'] != '' && $settings->link_line_height_unit == '' && $settings->link_line_height_unit_medium == '' ) { ?>
		    line-height: <?php echo $settings->link_line_height['medium']; ?>px;
		<?php } ?>

        <?php if( $converted === 'yes' || isset( $settings->link_line_height_unit_medium ) && $settings->link_line_height_unit_medium != '' ){ ?>
        	line-height: <?php echo $settings->link_line_height_unit_medium; ?>em;	
        <?php } else if( isset( $settings->link_line_height_unit_medium )&& $settings->link_line_height_unit_medium == '' && isset( $settings->link_line_height['medium'] ) && $settings->link_line_height['medium'] != '' ) {?>
        	line-height: <?php echo $settings->link_line_height['medium']; ?>px;
        <?php } ?>    

	}

	<?php if( $settings->min_height_switch == 'custom' && $settings->min_height_medium != "" ) : ?>
		.fl-node-<?php echo $id; ?> .uabb-infobox {
			min-height: <?php echo $settings->min_height_medium; ?>px;
		}
	<?php endif; ?>
}

<?php echo '@media (max-width: '. $global_settings->responsive_breakpoint .'px) { '; ?>
    
    .fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
		<?php 
	        if(isset($settings->info_box_padding_dimension_top_responsive) ){
	            echo ( $settings->info_box_padding_dimension_top_responsive != '' ) ? 'padding-top:'.$settings->info_box_padding_dimension_top_responsive.'px;' : ''; 
	        }
	        if(isset($settings->info_box_padding_dimension_bottom_responsive) ){
	            echo ( $settings->info_box_padding_dimension_bottom_responsive != '' ) ? 'padding-bottom:'.$settings->info_box_padding_dimension_bottom_responsive.'px;' : '';
	        }
	        if(isset($settings->info_box_padding_dimension_left_responsive) ){
	            echo ( $settings->info_box_padding_dimension_left_responsive != '' ) ? 'padding-left:'.$settings->info_box_padding_dimension_left_responsive.'px;' : ';';
	        }
	        if(isset($settings->info_box_padding_dimension_right_responsive) ){
	            echo ( $settings->info_box_padding_dimension_right_responsive != '' ) ? 'padding-right:'.$settings->info_box_padding_dimension_right_responsive.'px;' : '';
	        } 
	    ?>
	}
	
	<?php
	if( $settings->uabb_border_type != 'none' || $settings->bg_type != 'none' ) {
 		if( $settings->responsive_border == 'yes' ) : ?>
 		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox {
 			border: none;
 			<?php echo ( $settings->bg_type != 'none' ) ? '' : 'padding: 0;'; ?>
 		}	
 		<?php
 		endif; 
 	}


	if( $settings->img_icon_position == 'left' || $settings->img_icon_position == 'right' ) {
		if( $settings->mobile_view == 'stack' ) { ?>
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-left-right-wrap .uabb-imgicon-wrap {
			padding: 0;
			margin-bottom: 20px;
		}

		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox .uabb-infobox-left-right-wrap .uabb-infobox-content,
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox .uabb-infobox-left-right-wrap .uabb-imgicon-wrap {
			display: block;
			width: 100%;
			text-align: center;
		}

		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-reverse-order .uabb-infobox-left-right-wrap,
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-reverse-order .uabb-infobox-left-right-wrap {
			display: inline-flex;
			flex-direction: column-reverse;
		}

		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox.infobox-left .uabb-imgicon-wrap,
		.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox.infobox-right .uabb-imgicon-wrap {
			margin-left: 0;
			margin-right: 0;
		}
	<?php
		}
	}
	?>


	.fl-node-<?php echo $id; ?> .infobox-responsive-<?php echo $settings->mobile_align; ?> {
        text-align: <?php echo $settings->mobile_align; ?>;
    }
    

	.fl-builder-content .fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title,
	.fl-builder-content .fl-node-<?php echo $id; ?> <?php echo $settings->title_tag_selection; ?>.uabb-infobox-title a {
		
        <?php if( $converted === 'yes' || isset( $settings->title_font_size_unit_responsive ) && $settings->title_font_size_unit_responsive != '' ) { ?>
        	font-size: <?php echo $settings->title_font_size_unit_responsive; ?>px;
			<?php if( $settings->title_line_height_unit_responsive == '' && $settings->title_font_size_unit_responsive != '' ) {?>
				line-height: <?php $settings->title_font_size_unit_responsive + 2 ?>px;
			<?php } ?>	
        <?php } else if( isset( $settings->title_font_size_unit_responsive ) && $settings->title_font_size_unit_responsive == '' && isset( $settings->title_font_size['small'] ) && $settings->title_font_size['small'] != '' ) {?> 
        	font-size: <?php echo $settings->title_font_size['small']; ?>px;
		    line-height: <?php $settings->title_font_size['small'] + 2?>px;
        <?php } ?>

		<?php if( isset( $settings->title_font_size['small'] ) && $settings->title_font_size['small'] == '' && isset( $settings->title_line_height['small'] ) && $settings->title_line_height['small'] != '' && $settings->title_line_height_unit == '' && $settings->title_line_height_unit_medium == '' && $settings->title_line_height_unit_responsive == '' ) { ?>
		    line-height: <?php echo $settings->title_line_height['small']; ?>px;
		<?php } ?>

        <?php if( $converted === 'yes' || isset( $settings->title_line_height_unit_responsive ) && $settings->title_line_height_unit_responsive != '' ) { ?>
        	line-height: <?php echo $settings->title_line_height_unit_responsive; ?>em;
        <?php } else if( isset( $settings->title_line_height_unit_responsive )&& $settings->title_line_height_unit_responsive == '' && isset( $settings->title_line_height['small'] ) && $settings->title_line_height['small'] != '') { ?> 
        	line-height: <?php echo $settings->title_line_height['small']; ?>px;
        <?php } ?>  

	}

	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-text {

        <?php if( $converted === 'yes' || isset( $settings->subhead_font_size_unit_responsive ) && $settings->subhead_font_size_unit_responsive != '' ) { ?>
        	font-size: <?php echo $settings->subhead_font_size_unit_responsive; ?>px;
			<?php if( $settings->subhead_line_height_unit_responsive == '' && $settings->subhead_font_size_unit_responsive != '' ) {?>
				line-height: <?php $settings->subhead_font_size_unit_responsive + 2 ?>px;
			<?php } ?>	
        <?php } else if( isset( $settings->subhead_font_size_unit_responsive ) && $settings->subhead_font_size_unit_responsive == '' && isset( $settings->subhead_font_size['small'] ) && $settings->subhead_font_size['small'] != '' ) {?> 
        	font-size: <?php echo $settings->subhead_font_size['small']; ?>px;
		    line-height: <?php $settings->subhead_font_size['small'] + 2?>px;
        <?php } ?>		

		<?php if( isset( $settings->subhead_font_size['small'] ) && $settings->subhead_font_size['small'] == '' && isset( $settings->subhead_line_height['small'] ) && $settings->subhead_line_height['small'] != '' && $settings->subhead_line_height_unit == '' && $settings->subhead_line_height_unit_medium == '' && $settings->subhead_line_height_unit_responsive == '' ) { ?>
		    line-height: <?php echo $settings->subhead_line_height['small']; ?>px;
		<?php } ?>
        
        <?php if( $converted === 'yes' || isset( $settings->subhead_line_height_unit_responsive ) && $settings->subhead_line_height_unit_responsive != '' ) { ?>
        	line-height: <?php echo $settings->subhead_line_height_unit_responsive; ?>em;
        <?php } else if( isset( $settings->subhead_line_height_unit_responsive )&& $settings->subhead_line_height_unit_responsive == '' && isset( $settings->subhead_line_height['small'] ) && $settings->subhead_line_height['small'] != '') { ?> 
        	line-height: <?php echo $settings->subhead_line_height['small']; ?>px;
        <?php } ?>

	}

	<?php if( $settings->min_height_switch == 'custom' && $settings->min_height_responsive != "" ) : ?>
		.fl-node-<?php echo $id; ?> .uabb-infobox {
			min-height: <?php echo $settings->min_height_responsive; ?>px;
		}
	<?php endif; ?>

	.fl-builder-content .fl-node-<?php echo $id; ?> .uabb-infobox-title-prefix {

		<?php if( $converted === 'yes' || isset( $settings->prefix_font_size_unit_responsive ) && $settings->prefix_font_size_unit_responsive != '' ){ ?>
			font-size: <?php echo $settings->prefix_font_size_unit_responsive; ?>px;	
		<?php } else if( $settings->prefix_font_size_unit_responsive  && $settings->prefix_font_size_unit_responsive == '' && isset( $settings->prefix_font_size['small'] ) && $settings->prefix_font_size['small'] != '' ) { ?>
			font-size: <?php echo $settings->prefix_font_size['small']; ?>px;
		<?php } ?> 

		<?php if( isset( $settings->prefix_font_size['small'] ) && $settings->prefix_font_size['small'] == '' && isset( $settings->prefix_line_height['small'] ) && $settings->prefix_line_height['small'] != '' && $settings->prefix_line_height_unit == '' && $settings->prefix_line_height_unit_medium == '' && $settings->prefix_line_height_unit_responsive == '' ) { ?>
		    line-height: <?php echo $settings->prefix_line_height['small']; ?>px;
		<?php } ?>

        <?php if( $converted === 'yes' || isset( $settings->prefix_line_height_unit_responsive ) && $settings->prefix_line_height_unit_responsive != '' ) { ?>
        	line-height: <?php echo $settings->prefix_line_height_unit_responsive; ?>em;
        <?php } else if( isset( $settings->prefix_line_height_unit_responsive ) && $settings->prefix_line_height_unit_responsive == '' && isset( $settings->prefix_line_height['small'] ) && $settings->prefix_line_height['small'] != '' ) {?>
        	line-height: <?php echo $settings->prefix_line_height['small']; ?>px;
    	<?php } ?>

	}

	.fl-builder-content .fl-node-<?php echo $id; ?>	.uabb-infobox-cta-link { 

		<?php if( $converted === 'yes' || isset( $settings->link_font_size_unit_responsive ) && $settings->link_font_size_unit_responsive != '' ){ ?>
			font-size: <?php echo $settings->link_font_size_unit_responsive; ?>px;	
		<?php } else if( $settings->link_font_size_unit_responsive  && $settings->link_font_size_unit_responsive == '' && isset( $settings->link_font_size['small'] ) && $settings->link_font_size['small'] != '' ) { ?>
			font-size: <?php echo $settings->link_font_size['small']; ?>px;
		<?php } ?>
		
		<?php if( isset( $settings->link_font_size['small'] ) && $settings->link_font_size['small'] == '' && isset( $settings->link_line_height['small'] ) && $settings->link_line_height['small'] != '' && $settings->link_line_height_unit == '' && $settings->link_line_height_unit_medium == '' && $settings->link_line_height_unit_responsive == '' ) { ?>
		    line-height: <?php echo $settings->link_line_height['small']; ?>px;
		<?php } ?>

        <?php if( $converted === 'yes' || isset( $settings->link_line_height_unit_responsive ) && $settings->link_line_height_unit_responsive != '' ) { ?>
        	line-height: <?php echo $settings->link_line_height_unit_responsive; ?>em;
        <?php } else if( isset( $settings->link_line_height_unit_responsive ) && $settings->link_line_height_unit_responsive == '' && isset( $settings->link_line_height['small'] ) && $settings->link_line_height['small'] != '' ) {?>
        	line-height: <?php echo $settings->link_line_height['small']; ?>px;
    	<?php } ?> 
		
	}

	<?php if ( $settings->image_type == 'photo' && !empty( $settings->responsive_img_size ) ) {
		$class = 'infobox-photo-' . $settings->img_icon_position;
		$pos = $settings->img_icon_position;
		if ( $pos == 'left' || $pos == 'right' || $pos == 'left-title' || $pos == 'right-title' ) {
			$cal_width = $settings->responsive_img_size;
			if ( $settings->image_style == 'custom' ) {
				$cal_width = $cal_width + intval($settings->img_bg_size) * 2;
				if ( $settings->img_border_style != 'none' ) {
					$cal_width = $cal_width + ( intval($settings->img_border_width) * 2 );
				}
			}
			$cal_width = $cal_width + 25;
		}
	}
	?>

	/* Left Right Title Image */
	<?php if ( $pos == 'left' || $pos == 'right' ) { ?>
		.fl-node-<?php echo $id; ?> .uabb-infobox {
			text-align: <?php echo $pos; ?>;
		}
		.fl-builder-content .fl-node-<?php echo $id; ?> .<?php echo $class; ?> .uabb-infobox-content{
			width: calc(100% - <?php echo $cal_width; ?>px);
		}
	<?php }elseif( $pos == 'left-title' || $pos == 'right-title' ) { ?>
		.fl-node-<?php echo $id; ?> .uabb-infobox {
			text-align: <?php echo ( $pos == 'left-title' ) ? 'left' : 'right'; ?>;
		}
		.fl-builder-content .fl-node-<?php echo $id; ?> .<?php echo $class; ?> .uabb-infobox-title-wrap {
			width: calc(100% - <?php echo $cal_width; ?>px);
			display: inline-block;
		}
	<?php } ?>
}
<?php } ?>