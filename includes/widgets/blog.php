<?php
/**
 * Blog widget.
 *
 * @since Meth 1.0
 */
class Stag_Widget_Blog extends Stag_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_id          = 'stag_widget_blog';
		$this->widget_cssclass    = 'stag_widget_blog no-heading-style full-wrap';
		$this->widget_description = __( 'Displays most recent blog posts.', 'meth-assistant' );
		$this->widget_name        = __( 'Section: Blog', 'meth-assistant' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'From Our Blog', 'meth-assistant' ),
				'label' => __( 'Title:', 'meth-assistant' ),
			),
			'id' => array(
				'type'  => 'text',
				'std'   => 'blog',
				'label' => __( 'CSS ID:', 'meth-assistant' )
			),
			'id_desc' => array(
				'type' => 'description',
				'std' => __( 'This will used as link for one page menu.', 'meth-assistant' )
			),
			'category' => array(
				'type'  => 'categories',
				'std'   => -1,
				'label' => __( 'Category:', 'meth-assistant' )
			),
			'count' => array(
				'type'  => 'number',
				'std'   => '3',
				'min'   => '3',
				'max'   => '36',
				'step'  => '3',
				'label' => __( 'Count:', 'meth-assistant' ),
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
			'blog_popup' => array(
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

		$title             = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$count             = $instance['count'];
		$id                = $instance['id'];
		$bg_color          = $instance[ 'bg_color' ];
		$bg_opacity        = $instance[ 'bg_opacity' ];
		$bg_image          = $instance[ 'bg_image' ];
		$text_color        = $instance[ 'text_color' ];
		$link_color        = $instance[ 'link_color' ];
		$category          = $instance[ 'category' ];
		$disabled_parallax = isset( $instance['disabled_parallax'] ) ? $instance['disabled_parallax'] : false;
		$blog_popup        = isset( $instance['blog_popup'] ) ? $instance['blog_popup'] : false;

		$query_args = array( 'post_type' => 'post', 'posts_per_page' => $count, 'ignore_sticky_posts' => true );

		if( $category != '-1' ) {
			$query_args['cat'] = $category;
		}

		$posts      = new WP_Query( $query_args );

		echo $before_widget;
		?>

		<div id="<?php echo esc_attr( $id ); ?>" class="inner-section inside blog-grid">
			<?php if( $title ) echo $before_title . $title . $after_title; ?>

			<?php if ( $posts->have_posts() ) : ?>
				<span class="hentry" data-bg-color="<?php echo esc_attr( $bg_color ); ?>" data-bg-image="<?php echo esc_url( $bg_image ); ?>" data-bg-opacity="<?php echo esc_attr( $bg_opacity ); ?>" data-text-color="<?php echo esc_attr( $text_color ); ?>" data-link-color="<?php echo esc_attr( $link_color ); ?>" data-disabled-parallax="<?php echo esc_attr( $disabled_parallax ); ?>" data-blog-popup="<?php echo esc_attr( $blog_popup ); ?>"></span>
				<div class="blog-slideshow">

				<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('slide'); stag_markup_helper( array( 'context' => 'entry' ) ); ?>>
					<div class="article-inside">
						<time class="entry-date published" datetime="<?php echo get_the_time( 'c' ); ?>"<?php stag_markup_helper( array( 'context' => 'entry_time' ) ); ?>><?php the_time('d F Y'); ?></time>
						<header class="entry-header">
							<h1 class="entry-title"<?php stag_markup_helper( array( 'context' => 'title' ) ); ?>><a data-type="<?php echo get_post_type(); ?>" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
						</header>
					</div>
				</article>

				<?php endwhile; ?>

				</div>

			<?php endif; ?>
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

add_action( 'widgets_init', array( 'Stag_Widget_Blog', 'register' ) );
