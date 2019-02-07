<?php
/**
 * Team widget.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Testimonials extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_testimonials';
		$this->widget_cssclass    = 'stag_widget_testimonials full-wrap accent-background';
		$this->widget_description = __( 'Display client testimonials.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Testimonials', 'meth-assistant' );
		$this->settings           = array(
			'id' => array(
				'type'  => 'text',
				'std'   => 'testimonials',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			)
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

		$id    = $instance['id'];
		$posts = new WP_Query( array( 'post_type' => 'testimonials', 'posts_per_page' => -1 ) );

		echo $before_widget;
		?>

		<section id="<?php echo esc_attr( $id ); ?>" class="inner-section inside">

			<div class="testimonials-slideshow">
				<?php if ( $posts->have_posts() ) : ?>

					<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
					<div class="testimonial">
						<h5 class="testimonial-author"><?php the_title(); ?></h5>
						<div class="testimonial-content">
							<?php the_content(); ?>
						</div>
					</div>
					<?php endwhile; ?>

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

add_action( 'widgets_init', array( 'Stag_Widget_Testimonials', 'register' ) );
