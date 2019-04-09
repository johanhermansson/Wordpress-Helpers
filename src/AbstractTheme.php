<?php namespace OAN\Wordpress;

use OAN\Helpers\Singleton;

abstract class AbstractTheme extends Singleton {

	use Traits\Hooks {
		Traits\Hooks::__construct as hooks_construct;
	}

	/**
	 * Initialized boolean
	 *
	 * @var boolean
	 */
	protected $initialized = false;

	/**
	 * Actions
	 *
	 * @var array
	 */
	protected $actions = [
		'after_setup_theme',
		[ 'wp', 'this::remove_header_tags' ],
		[ 'widgets_init', 'this::register_sidebars' ],
		[ 'widgets_init', 'this::register_widgets' ],
		[ 'after_setup_theme', 'this::register_menus' ],
	];

	/**
	 * Filters
	 *
	 * @var array
	 */
	protected $filters = [];

	/**
	 * Menus
	 *
	 * @var array
	 */
	protected $menus = [];

	/**
	 * Sidebars
	 *
	 * @var array
	 */
	protected $sidebars = [];

	/**
	 * Widgets
	 *
	 * @var array
	 */
	protected $widgets = [
		'WP_Nav_Menu_Widget'        => false,
		'WP_Widget_Calendar'        => false,
		'WP_Widget_Links'           => false,
		'WP_Widget_Pages'           => false,
		'WP_Widget_Recent_Comments' => false,
		'WP_Widget_Recent_Posts'    => false,
		'WP_Widget_RSS'             => false,
		'WP_Widget_Search'          => false,
		'WP_Widget_Tag_Cloud'       => false,
	];

	/**
	 * Intialize theme
	 *
	 * @return instance
	 */
	final public function initialize() {
		if ( $this->initialized ) {
			return $this;
		}

		$this->hooks_construct();

		$this->initialized = true;

		return $this;
	}

	/**
	 * Removes unnecessary header tags
	 *
	 * @return void
	 */
	public function remove_header_tags() {
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}

	/**
	 * Add support for post thumbnails
	 *
	 * @return void
	 */
	final public function after_setup_theme() {
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Add single sidebar
	 *
	 * @param string $id
	 * @param string $name
	 * @return instance
	 */
	final public function add_sidebar( $id = '', $name = '' ) {
		$this->sidebars[ $id ] = $name;

		return $this;
	}

	/**
	 * Add multiple sidebars
	 *
	 * @param array $sidebars
	 * @return instance
	 */
	final public function add_sidebars( $sidebars = [] ) {
		foreach ( $sidebars as $id => $name ) {
			$this->add_widget( $id, $name );
		}

		return $this;
	}

	/**
	 * Get sidebars
	 *
	 * @return void
	 */
	final public function get_sidebars() {
		return $this->sidebars;
	}

	/**
	 * Register sidebars
	 *
	 * @return void
	 */
	final public function register_sidebars() {
		foreach ( $this->get_sidebars() as $id => $name ) {
			register_sidebar( array(
				'name' => $name,
				'id'   => $id,
			) );
		}
	}

	/**
	 * Add single widget
	 *
	 * @param string $class Absolute class name
	 * @return instance
	 */
	final public function add_widget( $class = '' ) {
		$this->widgets[ $class ] = true;

		return $this;
	}

	/**
	 * Add multiple widgets
	 *
	 * @param array $classes
	 * @return instance
	 */
	final public function add_widgets( $classes = [] ) {
		foreach ( $classes as $class ) {
			$this->add_widget( $class );
		}

		return $this;
	}

	/**
	 * Get multiple widgets
	 *
	 * @return void
	 */
	final public function get_widgets() {
		return $this->widgets;
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	final public function register_widgets() {
		foreach ( $this->get_widgets() as $widget => $active ) {
			if ( ! $active ) {
				unregister_widget( $widget );

				continue;
			}

			register_widget( $widget );
		}
	}

	/**
	 * Add single menu
	 *
	 * @param string $location
	 * @param string $description
	 * @return instance
	 */
	final public function add_menu( $location = '', $description = '' ) {
		$this->menus[ $location ] = $description;

		return $this;
	}

	/**
	 * Add multiple menus
	 *
	 * @param array $menus
	 * @return instance
	 */
	final public function add_menus( $menus = [] ) {
		foreach ( $menus as $location => $description ) {
			$this->add_menu( $location, $description );
		}

		return $this;
	}

	/**
	 * Get menus
	 *
	 * @return array
	 */
	final public function get_menus() {
		return $this->menus;
	}

	/**
	 * Register menus
	 *
	 * @return void
	 */
	final public function register_menus() {
		foreach ( $this->get_menus() as $location => $description ) {
			register_nav_menu( $location, $description );
		}
	}

	/**
	 * Get styles
	 *
	 * @return array
	 */
	final public function get_styles() {
		return $this->styles;
	}

	/**
	 * Get scripts
	 *
	 * @return array
	 */
	final public function get_scripts() {
		return $this->scripts;
	}

	/**
	 * Get theme version from style.css
	 *
	 * @return string
	 */
	final public static function get_version() {
		$theme = wp_get_theme();
		return $theme->Version;
	}

}