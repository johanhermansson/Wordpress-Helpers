<?php namespace OAN\Wordpress;

use OAN\Helpers\Singleton;

abstract class AbstractBlock extends Singleton {

	/**
	 * Hooks trait construct alias
	 */
	use Traits\Hooks {
		Traits\Hooks::__construct as hooks_construct;
	}

	/**
	 * Initialized or not
	 */
	protected $initialized = false;

	/**
	 * Registered or not
	 */
	protected $registered = false;

	/**
	 * Arguments sent to acf_register_block_type
	 */
	protected static $args = [];

	/**
	 * Name for acf_register_block_type
	 */
	protected static $name = '';

	/**
	 * Title for acf_register_block_type
	 */
	protected static $title = '';

	/**
	 * Description for acf_register_block_type
	 */
	protected static $description = '';

	/**
	 * Category for acf_register_block_type
	 */
	protected static $category = 'common';


	/**
	 * Align for acf_register_block_type
	 */
	protected static $align = 'full';

	/**
	 * Mode for acf_register_block_type
	 */
	protected static $mode = null;

	/**
	 * Scripts for acf_register_block_type
	 */
	protected static $scripts = [];

	/**
	 * Styles for acf_register_block_type
	 */
	protected static $styles = [];

/**
	 * Enqueue assets for acf_register_block_type
	 */
	protected static $enqueue_assets = null;

	/**
	 * Enqueue style for acf_register_block_type
	 */
	protected static $enqueue_script = null;


	/**
	 * Enqueue script for acf_register_block_type
	 */
	protected static $enqueue_style = null;

	/**
	 * Supports for acf_register_block_type
	 */
	protected static $supports = [ 'align' => false ];

	/**
	 * Base class name
	 * Will be used in get_attributes
	 */
	protected static $base_class = '';

	/**
	 * Prefrefined actions
	 */
	protected static $actions = [
		[ 'acf/init', 'this::register' ],
	];

	protected $base_class_name = '';

	/**
	 * Predefined filters
	 *
	 * @var array
	 */
	protected static $filters = [];

	/**
	 * Initialize block
	 * Prepares arguments for acf_register_block_type
	 * @param array $args
	 *
	 * @return instance
	 */
	final public function initialize( $args = [] ) {
		if ( $this->initialized ) {
			return static::instance();
		}

		if ( empty( $args ) or ! is_array( $args ) ) {
			$args = [];
		}

		if ( ! empty( static::$args ) and is_array( $args ) ) {
			$args = static::$args;
		}

		$args = array_merge( [
			'name'            => static::$name,
			'title'           => static::$title,
			'description'     => static::$description,
			'category'        => static::$category,
			'align'           => static::$align,
			'mode'            => static::$mode,
			'enqueue_assets'  => static::$enqueue_assets,
			'enqueue_script'  => static::$enqueue_script,
			'enqueue_style'   => static::$enqueue_style,
			'supports'        => static::$supports,
			'render_callback' => [ &$this, 'render' ],
		], $args );

		if ( empty( $args['name'] ) ) {
			throw new \Exception( 'Name was not found in ' . static::class );
		}

		if ( empty( $args['title'] ) ) {
			throw new \Exception( 'Title was not found in ' . static::class );
		}

		$args = apply_filters( 'oan/blocks/' . $args['name'] . '/args', $args );

		if ( ! empty( $args['base_class'] ) and empty( static::$base_class ) ) {
			$this->base_class_name = $args['base_class'];
		}

		if ( ! empty( $args['base_class'] ) ) {
			unset( $args['base_class'] );
		}

		if ( empty( $args['enqueue_script'] ) ) {
			unset( $args['enqueue_script'] );
		}

		if ( ! empty( $args['enqueue_script'] ) and ! preg_match( '/^https?:/', $args['enqueue_script'] ) ) {
			$args['enqueue_script'] = get_template_directory_uri() . '/' . ltrim( $args['enqueue_script'], '/' );
		}

		if ( empty( $args['enqueue_style'] ) ) {
			unset( $args['enqueue_style'] );
		}

		if ( ! empty( $args['enqueue_style'] ) and ! preg_match( '/^https?:/', $args['enqueue_style'] ) ) {
			$args['enqueue_style'] = get_template_directory_uri() . '/' . ltrim( $args['enqueue_style'], '/' );
		}

		if ( empty( $args['enqueue_assets'] ) ) {
			unset( $args['enqueue_assets'] );
		}

		$this->arguments = $args;

		$this->hooks_construct();

		$this->initialized = true;

		return static::instance();
	}

	/**
	 * Register block
	 * Calls acf_register_block_type
	 *
	 * @return instance
	 */
	final public function register() {
		if ( $this->registered ) {
			return static::instance();
		}

		acf_register_block_type( $this->arguments );

		$this->registered = true;

		return static::instance();
	}

	/**
	 * Get block attributes
	 * Follows BEM class name coding style
	 *
	 * @param array $block
	 * @return array Array of attributes
	 */
	public function get_attributes( $block = [] ) {
		$id = "{$block['name']}-{$block['id']}";

		if ( ! empty( $block['anchor'] ) ) {
			$id = $block['anchor'];
		}

		$classes = [];

		if ( ! empty( $this->base_class_name ) ) {
			$classes[] = $this->base_class_name;
		}

		if ( ! empty( $block['className'] ) ) {
			$classes[] = $block['className'];
		}

		if ( ! empty( $block['align'] ) ) {
			if ( ! empty( $this->base_class_name ) ) {
				$classes[] = $this->base_class_name . "--align{$block['align']}";
			}

			$classes[] = "align{$block['align']}";
		}

		return [
			'class' => implode( ' ', $classes ),
			'id'    => $id,
		];
	}

	/**
	 * Fallback render function
	 *
	 * @return void
	 */
	public function render() {}

	/**
	 * Fallback enqueue assets function
	 *
	 * @return void
	 */
	public function enqueue_assets() {}
}