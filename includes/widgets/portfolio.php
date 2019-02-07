<?php
/**
 * Portfolio widget.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Portfolio extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_portfolio';
		$this->widget_cssclass    = 'stag_widget_portfolio full-wrap';
		$this->widget_description = __( 'Showcase your work.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Portfolio', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'portfolio',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'category' => array(
				'type'     => 'categories',
				'taxonomy' => 'skill',
				'std'      => -1,
				'label'    => __( 'Skill:', 'meth-assistant' )
			),
			'cat_desc' => array(
				'type' => 'description',
				'std' => sprintf( __( 'Display posts by specific skills or category.<br>(Applicable only when <a href="%s" target="_blank">Portfolio Filter</a> is disabled under Theme Options).', 'meth-assistant' ), admin_url( 'admin.php?page=stagframework#portfolio-settings' ) )
			),
			'count' => array(
				'type'  => 'number',
				'std'   => '3',
				'step'  => '3',
				'min'   => '3',
				'max'   => '36',
				'label' => __( 'Count:', 'meth-assistant' ),
			),
			'portfolio_popup' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => __( 'Open in pop-up window', 'meth-assistant' ),
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

		$title    = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$count    = $instance['count'];
		$id       = $instance['id'];
		$category = $instance['category'];
		$portfolio_popup = isset( $instance['portfolio_popup'] ) ? $instance['portfolio_popup'] : false;

		$query_args = array(
			'post_type' => 'portfolio',
			'posts_per_page' => $count,
		);

		if ( false === (bool) stag_theme_mod( 'theme_options', 'portfolio-filter' ) ) {
			if ( isset( $category ) && -1 !== (int) $category ) {
				$tax_query = array(
					'tax_query' => array(
						array(
							'taxonomy' => 'skill',
							'field'    => 'id',
							'terms'    => $category,
						),
					),
				);
				$query_args = array_merge( $query_args, $tax_query );
			}
		}

		$posts = new WP_Query( $query_args );

		echo $before_widget;
		?>

		<div id="<?php echo esc_attr( $id ); ?>" class="inner-section inside">

			<?php if( $title ) echo $before_title . $title . $after_title; ?>
			<?php if ( true === (bool) stag_theme_mod( 'theme_options', 'portfolio-filter' ) ) : ?>
			<ul id="filters" class="portfolio-filter inside">
				<li class="filter"><a data-filter-value='*' href='#' class='active'><?php _e('All', 'meth-assistant'); ?></li>
				<?php
				$terms = get_terms('skill');
				$count = count($terms);
				$i     = 0;

				if($count > 0){
				    foreach($terms as $term){
				        echo "<li class='filter'><a href='". get_term_link( $term->slug, 'skill' ) ."' data-filter-value='.{$term->slug}'>{$term->name}</a></li>";
				    }
				}
				?>
			</ul>
			<?php endif; ?>

			<div class="portfolio-grid" data-portfolio-popup="<?php echo esc_attr( $portfolio_popup ); ?>">
				<?php if ( $posts->have_posts() ) : ?>
					<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
						<?php if( !has_post_thumbnail() ) continue; ?>
						<?php get_template_part( 'content', 'portfolio-grid' ); ?>
					<?php endwhile; ?>
				<?php endif; ?>
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

add_action( 'widgets_init', array( 'Stag_Widget_Portfolio', 'register' ) );
