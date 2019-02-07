<?php
/**
 * Display static content from an specific page.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Static_Content extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_static_content';
		$this->widget_cssclass    = 'stag_widget_static_content no-heading-style full-wrap';
		$this->widget_description = __( 'Displays content from a specific page.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Static Content', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'static-content',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'page' => array(
				'type'  => 'page',
				'std'   => '',
				'label' => __( 'Select Page:', 'meth-assistant' ),
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
		$id                = $instance[ 'id' ];
		$page              = $instance[ 'page' ];
		$bg_color          = $instance[ 'bg_color' ];
		$bg_opacity        = $instance[ 'bg_opacity' ];
		$bg_image          = $instance[ 'bg_image' ];
		$text_color        = $instance[ 'text_color' ];
		$link_color        = $instance[ 'link_color' ];
		$disabled_parallax = isset( $instance['disabled_parallax'] ) ? $instance['disabled_parallax'] : false;

		$post              = new WP_Query( array( 'page_id' => $page ) );

		echo $before_widget;

		// Allow site-wide customization of the 'Read more' link text
		$read_more = apply_filters( 'stag_read_more_text', __( 'Read more', 'meth-assistant' ) );
		?>
		<section id="<?php echo esc_attr( $id ); ?>" class="inner-section">

			<?php if ( $post->have_posts() ) : ?>
				<?php while ( $post->have_posts() ) : $post->the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); stag_markup_helper( array( 'context' => 'entry' ) ); ?> data-bg-color="<?php echo esc_attr( $bg_color ); ?>" data-bg-image="<?php echo esc_url( $bg_image ); ?>" data-bg-opacity="<?php echo esc_attr( $bg_opacity ); ?>" data-text-color="<?php echo esc_attr( $text_color ); ?>" data-link-color="<?php echo esc_attr( $link_color ); ?>" data-disabled-parallax="<?php echo esc_attr( $disabled_parallax ); ?>">
						<?php if( $title ) echo $before_title . $title . $after_title; ?>

						<div class="entry-content"<?php stag_markup_helper( array( 'context' => 'entry_content' ) ); ?>>
							<?php the_content( $read_more ); ?>
						</div>
					</article>
				<?php endwhile; ?>
			<?php endif; ?>

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

add_action( 'widgets_init', array( 'Stag_Widget_Static_Content', 'register' ) );
