<?php namespace OAN\Wordpress\Traits;

// PHP functions used
use array_merge, is_integer;

// Wordpress functions
use register_widget, unregister_widget;

trait Widgets {

	/**
	 * Default disabled widgets
	 *
	 * @var array
	 */
	protected $pre_widgets = [
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
	protected $widgets = [];

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
	 * Get widgets
	 *
	 * @return void
	 */
	final public function get_widgets() {
		return array_merge( (array) $this->pre_widgets, (array) $this->widgets );
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	final public function register_widgets() {
		$widgets = $this->get_widgets();

		foreach ( $widgets as $key => $value ) {
			if ( is_integer( $key ) ) {
				$widgets[ $value ] = true;
				unset( $widgets[ $key ] );
			}
		}

		foreach ( $widgets as $widget => $active ) {
			if ( ! $active ) {
				unregister_widget( $widget );

				continue;
			}

			register_widget( $widget );
		}
	}
}
