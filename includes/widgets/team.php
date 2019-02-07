<?php
/**
 * Team widget.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Team extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_team';
		$this->widget_cssclass    = 'stag_widget_team';
		$this->widget_description = __( 'Display team members.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Team', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Our Team', 'meth-assistant' ),
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'team',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'count' => array(
				'type'  => 'number',
				'std'   => '3',
				'step'  => '1',
				'min'   => '3',
				'max'   => '36',
				'label' => __( 'Count:', 'meth-assistant' ),
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

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$count = $instance['count'];
		$id    = $instance['id'];
		$posts = new WP_Query( array( 'post_type' => 'team', 'posts_per_page' => $count ) );

		echo $before_widget;
		?>
		<section id="<?php echo esc_attr( $id ); ?>" class="inner-section">

			<?php if( $title ) echo $before_title . $title . $after_title; ?>

			<div class="team-container">

				<?php if ( $posts->have_posts() ) : ?>
					<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
						<?php get_template_part( 'content', 'team-grid' ); ?>
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

add_action( 'widgets_init', array( 'Stag_Widget_Team', 'register' ) );
