<?php
/**
 * Contact form widget.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Contact extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_contact';
		$this->widget_cssclass    = 'stag_widget_contact';
		$this->widget_description = __( 'Displays contact form.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Contact', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Get in Touch', 'meth-assistant' ),
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'contact',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'description' => array(
				'type'  => 'textarea',
				'std'   => null,
				'rows'	=> '6',
				'label' => __( 'Description:', 'meth-assistant' ),
			),
			'desc' => array(
				'type' => 'description',
				'std'  => __( 'HTML Tags allowed. You may also use shortcodes here.', 'meth-assistant' ),
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

		$title       = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$id          = $instance['id'];
		$description = $instance['description'];

		echo $before_widget;
		?>

		<div id="<?php echo esc_attr( $id ); ?>" class="inner-section">
			<?php if( $title ) echo $before_title . $title . $after_title; ?>

			<div class="grid">
				<div class="unit one-of-two entry-content">
					<?php if( '' != $description ) echo wpautop( do_shortcode( $description ) ); ?>
				</div>
				<div class="unit one-of-two entry-content">
					<form action="<?php the_permalink(); ?>" method="post" class="contact-form">
						<h3><?php _e( 'Send us a direct message', 'meth-assistant' ); ?></h3>

						<div class="contact-inside">
							<p class="form-row">
								<label for="contact_name"><?php _e( 'Your Name', 'meth-assistant' ) ?></label>
								<input type="text" name="contact_name" id="contact_name" >
							</p>
							<p class="form-row">
								<label for="contact_email"><?php _e( 'Your Email', 'meth-assistant' ) ?></label>
								<input type="email" name="contact_email" id="contact_email" >
							</p>
							<p class="form-row">
								<label for="contact_description"><?php _e( 'Enter your message here', 'meth-assistant' ) ?></label>
								<textarea rows="4" name="contact_description" id="contact_description" ></textarea>
							</p>
							<p class="form-row">
								<input type="submit" value="<?php _e( 'Send Message', 'meth-assistant' ); ?>" name="contact_submit" id="contact_submit" class="form-submit">
								<?php wp_nonce_field( 'contact-nonce' ); ?>
							</p>
						</div>
					</form>
				</div>
			</div>

		</div>

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

add_action( 'widgets_init', array( 'Stag_Widget_Contact', 'register' ) );
