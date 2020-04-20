<?php
/**
 * Widget Base Class.
 *
 * @package StagFramework
 * @since 2.0.1.3
 */

class Stag_Widget extends WP_Widget {

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;
	public $control_ops;
	public $selective_refresh = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description,
			'customize_selective_refresh' => true,
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops, $this->control_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0
	 *
	 * @param string $hook_suffix
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( 'widgets.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'underscore' );
	}

	/**
	 * Get cached widget function.
	 *
	 * @return boolean
	 */
	function get_cached_widget( $args ) {
		if ( apply_filters( 'stag_disable_widget_cache', false ) ) {
			return false;
		}

		global $post;

		if ( isset( $post->ID ) ) {
			$args[ 'widget_id' ] = $args[ 'widget_id' ] . '-' . $post->ID;
		}

		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args[ 'widget_id' ] ] ) ) {
			echo $cache[ $args[ 'widget_id' ] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @return void
	 */
	public function cache_widget( $args, $content ) {
		if ( ! isset( $args[ 'widget_id' ] ) ) {
			$args[ 'widget_id' ] = rand(0, 100);
		}

		$cache[ $args[ 'widget_id' ] ] = $content;

		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache.
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * Update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			switch ( $setting[ 'type' ] ) {
				case 'textarea' :
					if ( current_user_can( 'unfiltered_html' ) )
						$instance[ $key ] = $new_instance[ $key ];
					else
						$instance[ $key ] = wp_kses_data( $new_instance[ $key ] );
				break;
				case 'multicheck' :
					$instance[ $key ] = maybe_serialize( $new_instance[ $key ] );

				case 'text' :
				case 'checkbox' :
				case 'select' :
				case 'number' :
				case 'colorpicker' :
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
				default :
					$instance[ $key ] = apply_filters( 'stag_widget_update_type_' . $setting[ 'type' ], $new_instance[ $key ], $key, $setting );
				break;
			}
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting[ 'std' ];

			switch ( $setting[ 'type' ] ) {
				case 'description' :
					?>
					<p class="description"><?php echo $value; ?></p>
					<?php
				break;

				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>">
							<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="1" <?php checked( 1, esc_attr( $value ) ); ?>/>
							<?php echo $setting[ 'label' ]; ?>
						</label>
					</p>
					<?php
				break;

				case 'multicheck' :
					$value = maybe_unserialize( $value );

					if ( ! is_array( $value ) )
						$value = array();
					?>
					<p><?php echo esc_attr( $setting[ 'label' ] ); ?></p>
					<p>
						<?php foreach ( $setting[ 'options' ] as $id => $label ) : ?>
						<label for="<?php echo sanitize_title( $label ); ?>-<?php echo esc_attr( $id ); ?>">
							<input type="checkbox" id="<?php echo sanitize_title( $label ); ?>-<?php echo esc_attr( $id ); ?>" name="<?php echo $this->get_field_name( $key ); ?>[]" value="<?php echo esc_attr( $id ); ?>" <?php if ( in_array( $id, $value ) ) : ?>checked="checked"<?php endif; ?>/>
							<?php echo esc_attr( $label ); ?><br />
						</label>
						<?php endforeach; ?>
					</p>
					<?php
				break;

				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting[ 'options' ] as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_attr( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;

				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting[ 'step' ] ); ?>" min="<?php echo esc_attr( $setting[ 'min' ] ); ?>" max="<?php echo esc_attr( $setting[ 'max' ] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;

				case 'textarea' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
						name="<?php echo $this->get_field_name( $key ); ?>" rows="<?php echo isset( $setting[ 'rows' ] )
						? $setting[ 'rows' ] : 3; ?>"><?php echo esc_html( $value ); ?></textarea>
					</p>
					<?php
				break;

				case 'page':
					$exclude_ids = implode( ',', array( get_option('page_for_posts'), get_option('page_on_front') ));
					$pages       = get_pages( 'sort_order=ASC&sort_column=post_title&post_status=publish&exclude='. $exclude_ids );
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach( $pages as $page ) : ?>
								<option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $page->ID, $value ); ?>><?php echo esc_attr( $page->post_title ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;

				case 'categories':
					$args = array( 'hide_empty' => 0 );

					if( isset( $setting['taxonomy'] ) ) $args['taxonomy'] = $setting['taxonomy'];

					$categories = get_categories( $args );
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<option value="-1"><?php _e( 'All', 'meth-assistant' ); ?></option>
							<?php foreach( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php selected( $cat->term_id, $value ); ?>><?php echo esc_attr( $cat->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;

				case 'colorpicker' :
						wp_enqueue_script( 'wp-color-picker' );
						wp_enqueue_style( 'wp-color-picker' );
						wp_enqueue_style( 'underscore' );
					?>
						<p style="margin-bottom: 0;">
							<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
						</p>
						<input type="text" class="widefat color-picker" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" data-default-color="<?php echo $value; ?>" value="<?php echo $value; ?>" />
						<script>
							( function( $ ){
								function initColorPicker( widget ) {
									widget.find( '.color-picker' ).wpColorPicker( {
										change: _.throttle( function() { // For Customizer
											$(this).trigger( 'change' );
										}, 3000 )
									});
								}

								function onFormUpdate( event, widget ) {
									initColorPicker( widget );
								}

								$( document ).on( 'widget-added widget-updated', onFormUpdate );

								$( document ).ready( function() {
									$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
										initColorPicker( $( this ) );
									} );
								} );
							}( jQuery ) );
						</script>
						<p></p>
					<?php
				break;

				case 'image' :
					wp_enqueue_media();
					wp_enqueue_script( 'app-image-widget-admin', get_template_directory_uri() . '/stag-customizer/js/app-image-widget-admin.js', array( 'jquery' ), '', true );
				?>
					<p style="margin-bottom: 0;">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
					</p>
					<p>
						<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>"value="<?php echo $value; ?>" placeholder="http://" />
					</p>
					<p style="margin-top: 3px;">
						<a href="#" class="button-secondary <?php echo esc_attr( $this->get_field_id( $key ) ); ?>-add"><?php _e( 'Choose Image', 'meth-assistant' ); ?></a>
					</p>
					<script>
						( function( $ ){
							$(document).on( 'ready widget-added widget-updated', function($) {
								var <?php echo esc_js( $key ); ?> = new cImageWidget.MediaManager({
									target: '<?php echo esc_attr( $this->get_field_id( $key ) ); ?>'
								});
							});
						}( jQuery ) );
					</script>
				<?php
				break;
				default :
					do_action( 'stag_widget_type_' . $setting[ 'type' ], $this, $key, $setting, $instance );
				break;
			}
		}
	}
}
