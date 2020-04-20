<?php

add_action('add_meta_boxes', 'stag_metabox_portfolio');

function stag_metabox_portfolio(){
	$meta_box = array(
		'id'          => 'stag-metabox-portfolio',
		'title'       =>  __( 'Portfolio Settings', 'meth' ),
		'description' => __( 'Here you can customize your project details.', 'meth' ),
		'page'        => 'portfolio',
		'context'     => 'normal',
		'priority'    => 'high',
		'fields'      => array(
			array(
				'name' => __( 'Project Images', 'meth' ),
				'desc' => __( 'Choose project images, ideal size 1170px x unlimited.', 'meth' ),
				'id'   => '_stag_portfolio_images',
				'type' => 'images',
				'std'  => __( 'Upload Images', 'meth' )
			),
		)
	);

    stag_add_meta_box( $meta_box );

	$meta_box = array(
		'id'          => 'stag-metabox-page',
		'title'       =>  __( 'Background Settings', 'meth' ),
		'description' => __( 'Here you can customize the cover settings for this page.', 'meth' ),
		'page'        => 'portfolio',
		'context'     => 'normal',
		'priority'    => 'high',
		'fields'      => array(
			array(
				'name' => __( 'Text Color', 'meth' ),
				'desc' => __( 'Choose Text color.', 'meth' ),
				'id'   => '_stag_text_color',
				'type' => 'color',
				'std'  => '#fff'
			),
			array(
				'name' => __( 'Upload background Image', 'meth' ),
				'desc' => __( 'Choose background image for this page.', 'meth' ),
				'id'   => '_stag_background_image',
				'type' => 'file',
				'std'  => ''
			),
			array(
				'name' => __( 'Background Color', 'meth' ),
				'desc' => __( 'Choose background color.', 'meth' ),
				'id'   => '_stag_background_color',
				'type' => 'color',
				'std'  => stag_theme_mod( 'colors', 'accent' )
			),
			array(
				'name' => __( 'Background Opacity', 'meth' ),
				'desc' => __( 'Choose background image opacity.', 'meth' ),
				'id'   => '_stag_background_opacity',
				'type' => 'text',
				'std'  => '20'
			),
		)
	);

    stag_add_meta_box( $meta_box );
}
