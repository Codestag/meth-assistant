<?php
/**
 * Output the Features section (based on the "Features" widget area)
 *
 * @since Meth 1.0
 */
class Stag_Widget_Features extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_features';
		$this->widget_cssclass    = 'stag_widget_features';
		$this->widget_description = __( 'Output the Features section (based on the "Features" widget area).', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Features', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Features', 'meth-assistant' ),
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'features',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'columns' => array(
				'type'    => 'select',
				'std'     => '2',
				'label'   => __( 'Columns:', 'meth-assistant' ),
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4'
				)
			),
			'desc' => array(
				'type' => 'description',
				'std'  => __( 'Select number of columns to display.', 'meth-assistant' )
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

		$title   = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$columns = $instance[ 'columns' ];
		$id      = $instance[ 'id' ];

		echo $before_widget;
		?>

		<section id="<?php echo esc_attr( $id ); ?>" class="inner-section feature-widget-<?php echo (int)$columns; ?>">
			<?php if( $title ) echo $before_title . $title . $after_title; ?>

			<?php dynamic_sidebar( 'sidebar-features' ); ?>
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

add_action( 'widgets_init', array( 'Stag_Widget_Features', 'register' ) );
