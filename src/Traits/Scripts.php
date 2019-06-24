<?php namespace OAN\Wordpress\Traits;

// PHP functions used
use array_replace, call_user_func_array, end, explode, func_get_args, is_array, is_string, ltrim, method_exists, strpos;

// Wordpress functions used
use get_stylesheet_directory_uri, sanitize_title, wp_get_theme;

trait Scripts {

	/**
	 * Scripts
	 *
	 * @var array
	 */
	protected $scripts = [];

	/**
	 * Add single script
	 *
	 * @param array $script
	 * @return instance
	 */
	final public function add_script( $handle = '', $src = '', $deps = [], $ver = '', $in_footer = true ) {
		$this->scripts[] = func_get_args();

		return $this;
	}

	/**
	 * Add multiple scripts
	 *
	 * @param array $scripts
	 * @return instance
	 */
	final public function add_scripts( $styles = [] ) {
		foreach ( $scripts as $script ) {
			call_user_func_array( [ $this, 'add_script' ], $script );
		}

		return $this;
	}

	/**
	 * Get scripts
	 *
	 * @return array
	 */
	final public function get_scripts() {
		return (array) $this->scripts;
	}

	final public function register_scripts() {
		$defaults = [
			'',                   // Handle
			'',                   // Source
			[],                   // Dependencies
			$this->get_version(), // Version
			true,                 // In footer
		];

		foreach ( $this->get_scripts() as $i => $script ) {
			if ( is_string( $script ) ) {
				if ( false === strpos( $style, 'manifest::' ) and false === strpos( $script, get_stylesheet_directory_uri() ) ) {
					$script = get_stylesheet_directory_uri() . '/' . ltrim( $script, '/' );
				}

				$script = [ sanitize_title( ( wp_get_theme() )->Name ) . '-script-' . ( $i + 1 ), $script ];
			}

			if ( ! $script or ! is_array( $script ) or empty( $script[1] ) ) {
				continue;
			}

			$script = array_replace( $defaults, $script );

			if ( false !== strpos( $script[1], 'manifest::' ) and method_exists( $this, 'get_manifest_asset' ) ) {
				$key = explode( 'manifest::', $script[1] );
				$script[1] = $this->get_manifest_asset( end( $key ) );
			}

			call_user_func_array( 'wp_enqueue_script', $script );
		}
	}
}
