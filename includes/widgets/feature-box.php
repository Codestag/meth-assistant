<?php
/**
 * Create a feature box for the "Features" Section.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Feature_Box extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_feature_box';
		$this->widget_cssclass    = 'stag_widget_feature_box';
		$this->widget_description = __( 'Create a feature box for the "Features" Section.', 'meth-assistant' );
		$this->widget_name        = __( 'Feature Box', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Feature Title', 'meth-assistant' ),
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'feature_image' => array(
				'type'  => 'image',
				'std'   => null,
				'label' => __( 'Feature Image:', 'meth-assistant' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'std'   => '',
				'rows'  => 6,
				'label' => __( 'Description:', 'meth-assistant' ),
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
		ob_start();

		extract( $args );

		$title         = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$feature_image = $instance[ 'feature_image' ];
		$description   = $instance[ 'description' ];

		echo $before_widget;
		?>

		<div class="feature-wrap<?php if( $feature_image ) echo ' has-image'; ?>">
			<?php if( $feature_image ): ?>
				<figure class="feature-image">
					<img src="<?php echo $feature_image; ?>" alt="">
				</figure>
			<?php endif; ?>

			<div class="feature-content">
				<?php if( $title ) echo $before_title . $title . $after_title; ?>
				<?php if( $description ): ?>
					<div class="feature-description"><?php echo wpautop($description); ?></div>
				<?php endif; ?>
			</div>
		</div>

		<?php
		echo $after_widget;

		$content = ob_get_clean();

		echo $content;
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

add_action( 'widgets_init', array( 'Stag_Widget_Feature_Box', 'register' ) );
