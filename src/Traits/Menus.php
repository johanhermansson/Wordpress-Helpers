<?php namespace OAN\Wordpress\Traits;

trait Menus {

	/**
	 * Menus
	 *
	 * @var array
	 */
	protected static $menus = [];

	/**
	 * Add single menu
	 *
	 * @param string $location
	 * @param string $description
	 * @return instance
	 */
	final public function add_menu( $location = '', $description = '' ) {
		static::$menus[ $location ] = $description;

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
		$menus = self::$menus ?: [];

		if ( static::$menus and static::$menus !== self::$menus ) {
			$menus = array_merge( $menus, static::$menus ?: [] );
		}

		return $menus;
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
}
