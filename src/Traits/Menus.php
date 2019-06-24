<?php namespace OAN\Wordpress\Traits;

// Wordpress functions used
use register_nav_menu;

trait Menus {

	/**
	 * Menus
	 *
	 * @var array
	 */
	protected $menus = [];

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
		return (array) $this->menus;
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
