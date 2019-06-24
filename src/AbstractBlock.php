<?php namespace OAN\Wordpress;

use Exception;
use OAN\Helpers\Singleton;

// PHP functions used
use array_merge, implode, is_array, ltrim, preg_match;

// Wordpress functions used
use apply_filters, get_template_directory_uri;

// ACF functions used
use acf_register_block_type;

abstract class AbstractBlock extends Singleton {

	/**
	 * Hooks trait construct alias
	 */
	use Traits\Hooks;

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
	protected $args = [];

	/**
	 * Name for acf_register_block_type
	 */
	protected $name = '';

	/**
	 * Title for acf_register_block_type
	 */
	protected $title = '';

	/**
	 * Description for acf_register_block_type
	 */
	protected $description = '';

	/**
	 * Category for acf_register_block_type
	 */
	protected $category = 'common';


	/**
	 * Align for acf_register_block_type
	 */
	protected $align = 'full';

	/**
	 * Mode for acf_register_block_type
	 */
	protected $mode = null;

	/**
	 * Scripts for acf_register_block_type
	 */
	protected $scripts = [];

	/**
	 * Styles for acf_register_block_type
	 */
	protected $styles = [];

/**
	 * Enqueue assets for acf_register_block_type
	 */
	protected $enqueue_assets = null;

	/**
	 * Enqueue style for acf_register_block_type
	 */
	protected $enqueue_script = null;


	/**
	 * Enqueue script for acf_register_block_type
	 */
	protected $enqueue_style = null;

	/**
	 * Supports for acf_register_block_type
	 */
	protected $supports = [ 'align' => false ];

	/**
	 * Base class name
	 * Will be used in get_attributes
	 */
	protected $base_class = '';

	/**
	 * Prefrefined actions
	 */
	protected $pre_actions = [
		[ 'acf/init', 'this::register' ],
	];
	protected $actions = [];

	protected $base_class_name = '';

	/**
	 * Predefined filters
	 *
	 * @var array
	 */
	protected $pre_filters = [];
	protected $filters = [];

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

		if ( empty( $this->args ) or ! is_array( $this->args ) ) {
			$this->args = [];
		}

		$args = array_merge( [
			'name'            => $this->name,
			'title'           => $this->title,
			'description'     => $this->description,
			'category'        => $this->category,
			'align'           => $this->align,
			'mode'            => $this->mode,
			'enqueue_assets'  => $this->enqueue_assets,
			'enqueue_script'  => $this->enqueue_script,
			'enqueue_style'   => $this->enqueue_style,
			'supports'        => $this->supports,
			'render_callback' => [ &$this, 'render' ],
		], $this->args, $args );

		if ( empty( $args['name'] ) ) {
			throw new Exception( 'Name was not found in ' . static::class );
		}

		if ( empty( $args['title'] ) ) {
			throw new Exception( 'Title was not found in ' . static::class );
		}

		$args = apply_filters( 'oan/blocks/' . $args['name'] . '/args', $args );

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

		$this->args = $args;

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

		acf_register_block_type( $this->args );

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
		$args = $this->args;

		$id = "{$block['name']}-{$block['id']}";

		if ( ! empty( $block['anchor'] ) ) {
			$id = $block['anchor'];
		}

		$classes = [];

		if ( ! empty( $args['base_class'] ) ) {
			$classes[] = $args['base_class'];
		}

		if ( ! empty( $block['className'] ) ) {
			$classes[] = $block['className'];
		}

		if ( ! empty( $block['align'] ) ) {
			if ( ! empty( $args['base_class'] ) ) {
				$classes[] = $args['base_class'] . "--align{$block['align']}";
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