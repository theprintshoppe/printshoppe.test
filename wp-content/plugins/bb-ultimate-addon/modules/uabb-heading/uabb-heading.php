<?php

/**
 * @class UABBHeadingModule
 */
class UABBHeadingModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __('Heading', 'uabb'),
			'description'   	=> __('Display a title/page heading.', 'uabb'),
			'category'          => BB_Ultimate_Addon_Helper::module_cat( BB_Ultimate_Addon_Helper::$content_modules ),
            'group'             => UABB_CAT,
			'dir'           	=> BB_ULTIMATE_ADDON_DIR . 'modules/uabb-heading/',
            'url'           	=> BB_ULTIMATE_ADDON_URL . 'modules/uabb-heading/',
            'partial_refresh'	=> true,
			'icon'				=> 'text.svg',
		));
	}

	public function render_separator( $pos ) {
		if( $this->settings->separator_style != 'none' && $this->settings->separator_position == $pos ) {

			$position = '0';
			if( $this->settings->alignment == 'center' ) {
				$position = '50';
			} elseif( $this->settings->alignment == 'right' ) {
				$position = '100';
			}
			$line_color = uabb_theme_base_color( $this->settings->separator_line_color );
			$separator_array = array(
				/* General Section */
				'separator' => $this->settings->separator_style,
				'style'		=> $this->settings->separator_line_style,
				'color'		=> $line_color,
				'height'	=> $this->settings->separator_line_height,
				'width'		=> ($this->settings->separator_line_width != '') ? $this->settings->separator_line_width : '30',
				'alignment'	=> $this->settings->alignment,
				'icon_photo_position'	=> $position,

				/* Icon Basics */
				'icon' => $this->settings->icon,
				'icon_size' => $this->settings->icon_size,
				'icon_align' => $this->settings->alignment,
				'icon_color' => $this->settings->separator_icon_color,
				
				/* Image Basics */
				'photo_source' => $this->settings->photo_source,
				'photo' => $this->settings->photo,
				'photo_url' => $this->settings->photo_url,
				'img_align' => $this->settings->alignment,
				'image_style' => 'simple',
				'photo_src' =>  isset($this->settings->photo_src) ? $this->settings->photo_src : '',

				/* Text */
				'text_inline' => $this->settings->text_inline,
				'text_tag_selection' => $this->settings->separator_text_tag_selection,
				'text_font_family' => $this->settings->separator_text_font_family,
				'text_font_size' => isset( $this->settings->separator_text_font_size ) ? $this->settings->separator_text_font_size : '',
				'text_line_height' => isset( $this->settings->separator_text_line_height ) ? $this->settings->separator_text_line_height : '',
				'text_font_size_unit_responsive' => $this->settings->separator_text_font_size_unit_responsive,
				'text_line_height_unit_responsive' => $this->settings->separator_text_line_height_unit_responsive,
				'text_font_size_unit_medium' => $this->settings->separator_text_font_size_unit_medium,
				'text_line_height_unit_medium' => $this->settings->separator_text_line_height_unit_medium,
				'text_font_size_unit' => $this->settings->separator_text_font_size_unit,
				'text_line_height_unit' => $this->settings->separator_text_line_height_unit,
				'text_color' => $this->settings->separator_text_color,
				'text_transform' => $this->settings->separator_transform,
				'text_letter_spacing' => $this->settings->separator_letter_spacing,

			); 
			/* Render HTML Function */
			FLBuilder::render_module_html( 'advanced-separator', $separator_array );

		}
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('UABBHeadingModule', array(
	'general'       => array(
		'title'         => __('General', 'uabb'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'heading'        => array(
						'type'            => 'text',
						'label'           => __('Heading', 'uabb'),
						'default'         => __('Design is a funny word', 'uabb'),
						'preview'         => array(
							'type'            => 'text',
							'selector'        => '.uabb-heading-text'
						),
						'connections'		=> array( 'string', 'html' )
					),
					'link'          => array(
						'type'          => 'link',
						'label'         => __('Link', 'uabb'),
						'preview'         => array(
							'type'            => 'none'
						),
						'connections'		=> array( 'url' )
					),
					'link_target'   => array(
						'type'          => 'select',
						'label'         => __('Link Target', 'uabb'),
						'default'       => '_self',
						'options'       => array(
							'_self'         => __('Same Window', 'uabb'),
							'_blank'        => __('New Window', 'uabb')
						),
						'preview'         => array(
							'type'            => 'none'
						)
					)
				)
			),
			'description'	=> array( 
				'title'  		=> __( 'Description', 'uabb' ),
				'fields' 		=> array(
					'description'	=> array(
						'type'   	=> 'editor',
						'label'  	=> '',
						'rows'   	=> 7,
						'default'	=> '',
						'connections'	=> array( 'string', 'html' ),
					)
				),
			),
			'structure'     => array(
				'title'         => __('Structure', 'uabb'),
				'fields'        => array(
					'alignment'     => array(
						'type'          => 'select',
						'label'         => __('Alignment', 'uabb'),
						'default'       => 'center',
						'options'       => array(
							'left'      =>  __('Left', 'uabb'),
							'center'    =>  __('Center', 'uabb'),
							'right'     =>  __('Right', 'uabb')
						),
						'help'         => __('This is the overall alignment and would apply to all Heading elements', 'uabb'),
						'preview'         => array(
                            'type'          => 'css',
                            'selector'      => '.uabb-heading-wrapper .uabb-heading, .uabb-heading-wrapper .uabb-subheading *',
                            'property'      => 'text-align',
                        )
					),
					'r_custom_alignment' => array(
						'type'          => 'select',
						'label'         => __('Responsive Alignment', 'uabb'),
						'default'       => 'center',
						'options'       => array(
							'left'      =>  __('Left', 'uabb'),
							'center'    =>  __('Center', 'uabb'),
							'right'     =>  __('Right', 'uabb')
						),
						'preview'         => array(
							'type'            => 'none'
						),
						'help'         => __('The alignment will apply on Mobile', 'uabb'),
					),
				)
			),
		)
	),
	'style'         => array(
		'title'         => __('Separator', 'uabb'),
		'sections'      => array(
			'separator'          => array(
				'title'         => __('Separator', 'uabb'),
				'fields'        => array(
					'separator_style'     => array(
						'type'          => 'select',
						'label'         => __('Separator Style', 'uabb'),
						'default'       => 'none',
						'options'       => array(
							'none'      	=>  __('None', 'uabb'),
							'line'      	=>  __('Line', 'uabb'),
							'line_icon'    	=>  __('Line With Icon', 'uabb'),
							'line_image'    =>  __('Line With Image', 'uabb'),
							'line_text'     =>  __('Line With Text', 'uabb'),
						),
						'toggle'	=> array(
							'line'	=> array(
								'sections'	=> array( 'separator_line' ),
								'fields'	=> array( 'separator_position' )
							),
							'line_icon'	=> array(
								'sections'	=> array( 'separator_line', 'separator_icon_basic' ),
								'fields'	=> array( 'separator_position' )
							),
							'line_image'	=> array(
								'sections'	=> array( 'separator_line', 'separator_img_basic' ),
								'fields'	=> array( 'separator_position' )
							),
							'line_text'	=> array(
								'sections'	=> array( 'separator_line', 'separator_text', 'separator_text_typography' ),
								'fields'	=> array( 'separator_position' )
							),
						)
					),
					'separator_position'     => array(
						'type'          => 'select',
						'label'         => __('Separator Position', 'uabb'),
						'default'       => 'center',
						'options'       => array(
							'center'      	=>  __('Between Heading & Description', 'uabb'),
							'top'    	=>  __('Top', 'uabb'),
							'bottom'    =>  __('Bottom', 'uabb'),
						),
					),
				)
			),
			'separator_icon_basic' 	=> 	array( // Section
		        'title'         => __('Icon Basics','uabb'), // Section Title
		        'fields'        => array( // Section Fields
		            'icon'          => array(
		                'type'          => 'icon',
		                'label'         => __('Icon', 'uabb'),
		                'show_remove'   => true
		            ),
		            'icon_size'     => array(
		                'type'          => 'text',
		                'label'         => __('Size', 'uabb'),
		                'placeholder'   => '30',
		                'maxlength'     => '5',
		                'size'          => '6',
		                'description'   => 'px',
		                'preview'   => array(
                            'type'      => 'css',
                            'selector'  => '.uabb-icon-wrap .uabb-icon i, .uabb-icon-wrap .uabb-icon i:before',
                            'property'  => 'font-size',
                            'unit'		=> 'px'
                        ),
		            ),
					'separator_icon_color' => array( 
						'type'       => 'color',
						'label'         => __('Icon Color', 'uabb'),
						'default'    => '',
						'show_reset' => true,
						'preview'   => array(
                            'type'      => 'css',
                            'selector'  => '.uabb-icon-wrap .uabb-icon i, .uabb-icon-wrap .uabb-icon i:before',
                            'property'  => 'color',
                        ),
					),
		        )
		    ),
			'separator_img_basic' 	=> array( // Section
		        'title'         => __('Image Basics','uabb'), // Section Title
		        'fields'        => array( // Section Fields
		            'photo_source'  => array(
		                'type'          => 'select',
		                'label'         => __('Photo Source', 'uabb'),
		                'default'       => 'library',
		                'options'       => array(
		                    'library'       => __('Media Library', 'uabb'),
		                    'url'           => __('URL', 'uabb')
		                ),
		                'toggle'        => array(
		                    'library'       => array(
		                        'fields'        => array('photo')
		                    ),
		                    'url'           => array(
		                        'fields'        => array('photo_url' )
		                    )
		                )
		            ),
		            'photo'         => array(
		                'type'          => 'photo',
		                'label'         => __('Photo', 'uabb'),
		                'show_remove'   => true,
		                'connections'	=> array( 'photo' )
		            ),
		            'photo_url'     => array(
		                'type'          => 'text',
		                'label'         => __('Photo URL', 'uabb'),
		                'placeholder'   => 'http://www.example.com/my-photo.jpg',
		            ),
		            'img_size'     => array(
		                'type'          => 'text',
		                'label'         => __('Size', 'uabb'),
		                'maxlength'     => '5',
		                'size'          => '6',
		                'description'   => 'px',
						'placeholder' => '50'
		            ),
					'responsive_img_size'     => array(
						'type'          => 'text',
						'label'         => __('Responsive Size', 'uabb'),
						'maxlength'     => '5',
						'size'          => '6',
						'description'   => 'px',
						'help'			=> __( 'Image size below medium devices. Leave it blank if you want to keep same size', 'uabb' ),
						'preview'		=> array(
							'type'	=> 'none'
						)
					),
		        )
		    ),
			'separator_text'			=> array(
				'title'			=> __('Text', 'uabb'),
				'fields'		=> array(
					'text_inline'        => array(
						'type'          => 'text',
						'label'         => __('Text', 'uabb'),
						'default'       => 'Ultimate',
						'preview'       => array(
							'type'          => 'text',
							'selector'      => '.uabb-divider-text',
						)
					),
					'responsive_compatibility' => array(
						'type' => 'select',
						'label' => __('Responsive Compatibility', 'uabb'),
						'help' => __('There might be responsive issues for long texts. If you are facing such issues then select appropriate devices width to make your module responsive.', 'uabb'),
						'default' => '',
						'options' => array(
							'' => __('None','uabb'),
							'uabb-responsive-mobile' => __('Small Devices','uabb'),
							'uabb-responsive-medsmall' => __('Medium & Small Devices','uabb'),
						),
					),
				)
			),
			'separator_line'	=> array(
				'title'		=> __('Line Style', 'uabb'), //tab title
				'fields'	=> array(
					'separator_line_style'		=> array(
						'type'          => 'select',
						'label'         => __('Style', 'uabb'),
						'default'       => 'solid',
						'options'       => array(
							'solid'         => __( 'Solid', 'uabb' ),
							'dashed'        => __( 'Dashed', 'uabb' ),
							'dotted'        => __( 'Dotted', 'uabb' ),
							'double'        => __( 'Double', 'uabb' )
						),
						'help'          => __('The type of border to use. Double borders must have a height of at least 3px to render properly.', 'uabb'),
						'preview'         => array(
                            'type'          => 'css',
                            'selector'      => '.uabb-separator, .uabb-separator-line > span',
                            'property'      => 'border-top-style',
                        )
					),
					'separator_line_color' => array( 
						'type'       => 'color',
						'label'      => __('Color', 'uabb'),
						'default'    => '',
						'show_reset' => true,
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.uabb-separator, .uabb-separator-line > span',
							'property'      => 'border-top-color'
						)
					),
					'separator_line_height'     => array(
						'type'          => 'text',
						'label'         => __('Thickness', 'uabb'),
						'placeholder'   => '1',
						'maxlength'     => '2',
						'size'          => '3',
						'description'   => 'px',
						'help'			=> __( 'Thickness of Border', 'uabb' ),
						'preview'       => array(
							'type'          => 'css',
							'selector'      => '.uabb-separator, .uabb-separator-line > span',
							'property'      => 'border-top-width',
							'unit'			=> 'px',
						)
					),
					'separator_line_width'      => array(
						'type'          => 'text',
						'label'         => __('Width', 'uabb'),
						'placeholder'   => '30',
						'maxlength'     => '3',
						'size'          => '5',
						'description'   => '%',
					),
				)
			),
		)
	),
	'typography'         => array(
		'title'         => __('Typography', 'uabb'),
		'sections'      => array(
			'heading_typo'     => array(
				'title'         => __('Heading', 'uabb'),
				'fields'        => array(
					'tag'           => array(
						'type'          => 'select',
						'label'         => __( 'HTML Tag', 'uabb' ),
						'default'       => 'h3',
						'options'       => array(
							'h1'            =>  'h1',
							'h2'            =>  'h2',
							'h3'            =>  'h3',
							'h4'            =>  'h4',
							'h5'            =>  'h5',
							'h6'            =>  'h6'
						)
					),
					'font'          => array(
						'type'          => 'font',
						'default'		=> array(
							'family'		=> 'Default',
							'weight'		=> 300
						),
						'label'         => __('Font', 'uabb'),
						'preview'         => array(
							'type'            => 'font',
							'selector'        => '.uabb-heading .uabb-heading-text'
						)
					),
					'font_size_unit' => array(
						'type' => 'unit',
						'label' => __('Font Size', 'uabb'),
						'description' => 'px',
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.uabb-heading .uabb-heading-text',
							'property'	=> 'font-size',
							'unit' 		=> 'px'
						),
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
					),
					'line_height_unit' => array(
						'type' => 'unit',
						'label' => __('Line height', 'uabb'),
						'description' => 'em',
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.uabb-heading .uabb-heading-text',
							'property'	=> 'line-height',
							'unit' 		=> 'em'
						),
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
					),
					'color'    => array( 
						'type'       => 'color',
						'label'         => __('Text Color', 'uabb'),
						'default'    => '',
						'show_reset' => true,
						'preview'		=> array(
							'type' => 'css',
							'property' => 'color',
							'selector' => '.fl-module-content.fl-node-content .uabb-heading  .uabb-heading-text'
						)
					),
                    'transform'     => array(
                        'type'          => 'select',
                        'label'         => __( 'Transform', 'uabb' ),
                        'default'       => '',
                        'options'       => array(
                            ''           		=>  'Default',
                            'uppercase'         =>  'UPPERCASE',
                            'lowercase'         =>  'lowercase',
                            'capitalize'        =>  'Capitalize'                 
                        ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.fl-module-content.fl-node-content .uabb-heading  .uabb-heading-text',
                            'property'      => 'text-transform'
                        ),
                    ),
                    'letter_spacing'       => array(
                        'type'          => 'text',
                        'label'         => __('Letter Spacing', 'uabb'),
                        'placeholder'   => '0',
                        'size'          => '5',
                        'description'   => 'px',
                        'preview'         => array(
                            'type'          => 'css',
                            'selector'      => '.fl-module-content.fl-node-content .uabb-heading  .uabb-heading-text',
                            'property'      => 'letter-spacing',
                            'unit'          => 'px'
                        )
                    ),
					'heading_margin_top'       => array(
						'type'          => 'text',
						'label'         => __('Margin Top', 'uabb'),
						'placeholder'	=> '0',
						'size'			=> '5',
						'description'	=> 'px',
						'preview'		=> array(
							'type' => 'css',
							'property' => 'margin-top',
							'selector' => '.uabb-heading',
							'unit'		=> 'px',
						)
					),
					'heading_margin_bottom'       => array(
						'type'          => 'text',
						'label'         => __('Margin Bottom', 'uabb'),
						'placeholder'	=> '15',
						'size'			=> '5',
						'description'	=> 'px',
						'preview'		=> array(
							'type' => 'css',
							'property'	=> 'margin-bottom',
							'selector'	=> '.uabb-heading',
							'unit'		=> 'px',
						)
					),
				)
			),
			'description_typo'    =>  array(
		        'title'		=> __('Description', 'uabb'),
		        'fields'    => array(
		            'desc_font_family'       => array(
		                'type'          => 'font',
		                'label'         => __('Font Family', 'uabb'),
		                'default'       => array(
		                    'family'        => 'Default',
		                    'weight'        => 'Default'
		                ),
						'preview'         => array(
							'type'            => 'font',
							'selector'        => '.uabb-subheading, .uabb-subheading *'
						)
		            ),
					'desc_font_size_unit' => array(
						'type' => 'unit',
						'label' => __('Font Size', 'uabb'),
						'description' => 'px',
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.uabb-subheading, .uabb-subheading *',
							'property'	=> 'font-size',
							'unit' 		=> 'px'
						),
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
					),
					'desc_line_height_unit' => array(
						'type' => 'unit',
						'label' => __('Line height', 'uabb'),
						'description' => 'em',
						'preview' => array(
							'type' 		=> 'css',
							'selector'	=> '.uabb-subheading, .uabb-subheading *',
							'property'	=> 'line-height',
							'unit' 		=> 'em'
						),
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
					),
		            'desc_color'        => array( 
						'type'       => 'color',
						'label'      => __('Color', 'uabb'),
						'default'    => '',
						'show_reset' => true,
						'preview'		=> array(
							'type' => 'css',
							'property' => 'color',
							'selector' => '.fl-module-content.fl-node-content .uabb-subheading, .fl-module-content.fl-node-content .uabb-subheading *'
						)
					),
                    'desc_transform'     => array(
                        'type'          => 'select',
                        'label'         => __( 'Transform', 'uabb' ),
                        'default'       => '',
                        'options'       => array(
                            ''           		=>  'Default',
                            'uppercase'         =>  'UPPERCASE',
                            'lowercase'         =>  'lowercase',
                            'capitalize'        =>  'Capitalize'                 
                        ),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.fl-module-content.fl-node-content .uabb-subheading, .fl-module-content.fl-node-content .uabb-subheading *',
                            'property'      => 'text-transform'
                        ),
                    ),
                    'desc_letter_spacing'       => array(
                        'type'          => 'text',
                        'label'         => __('Letter Spacing', 'uabb'),
                        'placeholder'   => '0',
                        'size'          => '5',
                        'description'   => 'px',
                        'preview'         => array(
                            'type'          => 'css',
                            'selector'      => '.fl-module-content.fl-node-content .uabb-subheading, .fl-module-content.fl-node-content .uabb-subheading *',
                            'property'      => 'letter-spacing',
                            'unit'          => 'px'
                        )
                    ),
					'desc_margin_top'       => array(
						'type'          => 'text',
						'label'         => __('Margin Top', 'uabb'),
						'placeholder'	=> '15',
						'size'			=> '5',
						'description'	=> 'px',
						'preview'		=> array(
							'type' => 'css',
							'property' => 'margin-top',
							'selector' => '.uabb-subheading',
							'unit'	=> 'px',
						)
					),
					'desc_margin_bottom'       => array(
						'type'          => 'text',
						'label'         => __('Margin Bottom', 'uabb'),
						'placeholder'	=> '0',
						'size'			=> '5',
						'description'	=> 'px',
						'preview'		=> array(
							'type' => 'css',
							'property' => 'margin-bottom',
							'selector' => '.uabb-subheading',
							'unit'		=> 'px',
						)
					),
		        )
		    ),
			'separator_text_typography' => array(
		        'title'     => __('Separator Text Typography', 'uabb'),
		        'fields'    => array(
		            'separator_text_tag_selection'   => array(
		                'type'          => 'select',
		                'label'         => __('Text Tag', 'uabb'),
		                'default'       => 'h3',
		                'options'       => array(
		                    'h1'      => __('H1', 'uabb'),
		                    'h2'      => __('H2', 'uabb'),
		                    'h3'      => __('H3', 'uabb'),
		                    'h4'      => __('H4', 'uabb'),
		                    'h5'      => __('H5', 'uabb'),
		                    'h6'      => __('H6', 'uabb'),
		                    'div'     => __('Div', 'uabb'),
		                    'p'       => __('p', 'uabb'),
		                    'span'    => __('span', 'uabb'),
		                )
		            ),
		            'separator_text_font_family'       => array(
		                'type'          => 'font',
		                'label'         => __('Font Family', 'uabb'),
		                'default'       => array(
		                    'family'        => 'Default',
		                    'weight'        => 'Default'
		                ),
                        'preview'         => array(
                            'type'            => 'font',
                            'selector'        => '.uabb-divider-text'
                        )
		            ),
		            'separator_text_font_size_unit'     => array(
		                'type'          => 'unit',
		                'label'         => __( 'Font Size', 'uabb' ),
						'description' => 'px',
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
	                  	'preview'		=> array(
							'type' => 'css',
							'property'	=> 'font-size',
							'selector'  => '.uabb-divider-text',
							'unit'		=> 'px',
						)
		            ),
		            'separator_text_line_height_unit'    => array(
		                'type'          => 'unit',
		                'label'         => __( 'Line Height', 'uabb' ),
						'description' => 'em',
						'responsive' => array(
							'placeholder' => array(
								'default' => '',
								'medium' => '',
								'responsive' => '',
							),
						),
		                'preview'		=> array(
							'type' => 'css',
							'property'	=> 'line-height',
							'selector'  => '.uabb-divider-text',
							'unit'		=> 'em',
						)
		            ),
		            'separator_text_color' => array( 
						'type'       => 'color',
						'label'      => __('Text Color', 'uabb'),
						'default'    => '',
						'show_reset' => true,
						'preview'		=> array(
							'type' => 'css',
							'property'	=> 'color',
							'selector'  => '.uabb-divider-text',
						)
					),
                    'separator_transform'     => array(
                        'type'          => 'select',
                        'label'         => __( 'Transform', 'uabb' ),
                        'default'       => '',
                        'options'       => array(
                            ''           		=>  'Default',
                            'uppercase'         =>  'UPPERCASE',
                            'lowercase'         =>  'lowercase',
                            'capitalize'        =>  'Capitalize'                 
                        ), 
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.uabb-divider-text',
                            'property'      => 'text-transform'
                        ),
                    ),
                    'separator_letter_spacing'       => array(
                        'type'          => 'text',
                        'label'         => __('Letter Spacing', 'uabb'),
                        'placeholder'   => '0',
                        'size'          => '5',
                        'description'   => 'px',
                        'preview'         => array(
                            'type'          => 'css',
                            'selector'      => '.uabb-divider-text',
                            'property'      => 'letter-spacing',
                            'unit'          => 'px'
                        )
                    )
		        )
		    ),
		)
	)
));
