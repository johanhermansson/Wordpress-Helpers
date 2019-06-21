<?php namespace OAN\Wordpress;

use HaydenPierce\ClassFinder\ClassFinder;
use OAN\Helpers\Singleton;

abstract class AbstractTheme extends Singleton {

	use Traits\Hooks {
		Traits\Hooks::__construct as hooks_construct;
	}

	use Traits\Manifest;
	use Traits\Menus;
	use Traits\Sidebars;
	use Traits\Scripts;
	use Traits\Styles;
	use Traits\Widgets;

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
	protected static $actions = [
		'after_setup_theme',
		[ 'wp', 'this::remove_header_tags' ],
		[ 'widgets_init', 'this::register_sidebars' ],
		[ 'widgets_init', 'this::register_widgets' ],
		[ 'after_setup_theme', 'this::register_menus' ],
		[ 'wp_enqueue_scripts', 'this::register_styles' ],
		[ 'wp_enqueue_scripts', 'this::register_scripts' ],
	];

	/**
	 * Filters
	 *
	 * @var array
	 */
	protected static $filters = [];

	protected static $blocks = [];

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
		$this->initialize_blocks();

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
	public function after_setup_theme() {
		add_theme_support( 'post-thumbnails' );
	}

	/**
	 * Get theme version from style.css
	 *
	 * @return string
	 */
	public static function get_version() {
		$theme = wp_get_theme();
		return $theme->Version ?: get_bloginfo( 'version' );
	}

	/**
	 * Initialize blocks based on AbstractBlock
	 *
	 * @return void
	 */
	public function initialize_blocks() {
		$blocks = self::$blocks;

		if ( isset( static::$blocks ) ) {
			if ( is_string( static::$blocks ) ) {
				if (  ! class_exists( static::$blocks ) ) {
					$blocks = array_merge( $blocks, ClassFinder::getClassesInNamespace( static::$blocks ) );
				} else {
					$blocks = array_merge( $blocks, [ static::$blocks ] );
				}
			} else {
				$blocks = array_merge( $blocks, static::$blocks );
			}
		}

		foreach ( $blocks as $block ) {
			if ( method_exists( $block, 'instance' ) and method_exists( $block, 'initialize' ) ) {
				$block::instance()->initialize();
			}
		}
	}

}
