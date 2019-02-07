<?php
/**
 * Homepage / Landing Page Intro Widget
 *
 * @since Meth 1.0
 */
class Stag_Widget_Intro extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_intro';
		$this->widget_cssclass    = 'stag_widget_intro no-heading-style full-wrap';
		$this->widget_description = __( 'Displays content from a specific page.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Intro', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'intro',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'description' => array(
				'type'  => 'textarea',
				'std'   => '',
				'rows'  => 5,
				'label' => __( 'Description:', 'meth-assistant' ),
			),
			'button_label' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Button Label:', 'meth-assistant' ),
			),
			'button_url' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Button URL:', 'meth-assistant' ),
			),
			'bg_color' => array(
				'type'  => 'colorpicker',
				'std'   => stag_theme_mod( 'colors', 'accent' ),
				'label' => __( 'Background Color:', 'meth-assistant' ),
			),
			'bg_opacity' => array(
				'type'  => 'number',
				'std'   => '20',
				'step'  => '5',
				'min'   => '0',
				'max'   => '100',
				'label' => __( 'Background Opacity:', 'meth-assistant' ),
			),
			'box_bg_opacity' => array(
				'type'  => 'number',
				'std'   => '90',
				'step'  => '5',
				'min'   => '0',
				'max'   => '100',
				'label' => __( 'Box Background Opacity:', 'meth-assistant' ),
			),
			'bg_image' => array(
				'type'  => 'image',
				'std'   => 'https://source.unsplash.com/random/1600x800',
				'label' => __( 'Background Image:', 'meth-assistant' ),
			),
			'text_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#ffffff',
				'label' => __( 'Text Color:', 'meth-assistant' ),
			),
			'link_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#f8f8f8',
				'label' => __( 'Link Color:', 'meth-assistant' ),
			),
			'disabled_parallax' => array(
				'type'  => 'checkbox',
				'std'   => false,
				'label' => __( 'Disable Parallax Effect?', 'meth-assistant' ),
			),
			'disabled_parallax_desc' => array(
				'type' => 'description',
				'std' => __( 'This will disable the parallax effect on the widget section.', 'meth-assistant' )
			),
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		$title             = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$id                = $instance['id'];
		$description       = $instance['description'];
		$button_label      = $instance['button_label'];
		$button_url        = $instance['button_url'];
		$bg_color          = $instance['bg_color'];
		$bg_opacity        = $instance['bg_opacity'];
		$box_bg_opacity    = isset( $instance['box_bg_opacity'] ) ? $instance['box_bg_opacity'] : 35;
		$bg_image          = $instance['bg_image'];
		$text_color        = $instance['text_color'];
		$link_color        = $instance['link_color'];
		$disabled_parallax = isset( $instance['disabled_parallax'] ) ? $instance['disabled_parallax'] : false;

		echo $before_widget;

		?>
		<section id="<?php echo esc_attr( $id ); ?>" class="inner-section inside">
			<span class="hentry" data-bg-color="<?php echo esc_attr( $bg_color ); ?>" data-bg-image="<?php echo esc_url( $bg_image ); ?>" data-bg-opacity="<?php echo esc_attr( $bg_opacity ); ?>" data-box-bg-opacity="<?php echo esc_attr( $box_bg_opacity ); ?>" data-text-color="<?php echo esc_attr( $text_color ); ?>" data-link-color="<?php echo esc_attr( $link_color ); ?>" data-disabled-parallax="<?php echo esc_attr( $disabled_parallax ); ?>"></span>

			<div class="inner-container">
				<div class="box-bg"></div>
				<?php if ( $title ) echo $before_title . $title . $after_title; ?>
				<span class="sep"></span>
				<?php if ( $description ) : ?>
					<div class="intro-description">
						<?php echo wpautop( $description ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $button_url ) : ?>
					<a href="<?php echo esc_url( $button_url ); ?>" class="stag-button stag-button--large accent-background"><?php echo esc_attr( $button_label ); ?></a>
				<?php endif; ?>
			</div>
		</section>
		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}

	/**
	 * Registers the widget with the WordPress Widget API.
	 *
	 * @return mixed
	 */
	public static function register() {
		register_widget( __CLASS__ );
	}
}

add_action( 'widgets_init', array( 'Stag_Widget_Intro', 'register' ) );
