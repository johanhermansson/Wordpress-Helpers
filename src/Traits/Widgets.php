<?php namespace OAN\Wordpress\Traits;

trait Widgets {

	/**
	 * Default disabled widgets
	 *
	 * @var array
	 */
	protected static $widgets = [
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
	 * Add single widget
	 *
	 * @param string $class Absolute class name
	 * @return instance
	 */
	final public function add_widget( $class = '' ) {
		static::$widgets[ $class ] = true;

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
		$widgets = self::$widgets ?: [];

		if ( static::$widgets and static::$widgets !== self::$widgets ) {
			$widgets = array_merge( $widgets, static::$widgets ?: [] );
		}

		return $widgets;
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
