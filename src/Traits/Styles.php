<?php namespace OAN\Wordpress\Traits;

trait Styles {

	/**
	 * Styles
	 *
	 * @var array
	 */
	protected static $styles = [];

	/**
	 * Add single style
	 *
	 * @param array $style
	 * @return instance
	 */
	final public function add_style( $handle = '', $src = '', $deps = [], $ver = '', $media = 'all' ) {
		static::$styles[] = func_get_args();

		return $this;
	}

	/**
	 * Add multiple styles
	 *
	 * @param array $styles
	 * @return instance
	 */
	final public function add_styles( $styles = [] ) {
		foreach ( $styles as $style ) {
			call_user_func_array( [ $this, 'add_style' ], $style );
		}

		return $this;
	}

	/**
	 * Get styles
	 *
	 * @return array
	 */
	final public function get_styles() {
		$styles = empty( self::$styles ) ? [] : self::$styles;

		if ( ! empty( static::$styles ) and static::$styles !== self::$styles ) {
			$styles = array_merge( $styles, static::$styles ?: [] );
		}

		return $styles;
	}

	final public function register_styles() {
		$defaults = [
			'',                  // Handle
			'',                  // Source
			[],                  // Dependencies
			self::get_version(), // Version
			'all',               // Media
		];

		foreach ( $this->get_styles() as $i => $style ) {
			if ( is_string( $style ) ) {
				if ( false === strpos( $style, get_stylesheet_directory_uri() ) ) {
					$style = get_stylesheet_directory_uri() . '/' . ltrim( $style, '/' );
				}

				$style = [ sanitize_title( ( wp_get_theme() )->Name ) . '-style-' . ( $i + 1 ), $style ];
			}

			if ( ! $style or ! is_array( $style ) ) {
				$style = [];
			}

			$style = array_replace( $defaults, $style );

			call_user_func_array( 'wp_enqueue_style', $style );
		}
	}
}
