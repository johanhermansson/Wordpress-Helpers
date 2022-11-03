<?php
namespace OAN\Wordpress\Traits;

trait Sidebars {

	/**
	 * Sidebars
	 *
	 * @var array
	 */
	protected $sidebars = [];

	/**
	 * Add single sidebar
	 *
	 * @param string $id
	 * @param string|array $name Label or array with args
	 * @return instance
	 */
	final public function add_sidebar( $id = '', $name = '' ) {
		$this->sidebars[$id] = $name;

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
			$this->add_sidebar( $id, $name );
		}

		return $this;
	}

	/**
	 * Get sidebars
	 *
	 * @return array
	 */
	final public function get_sidebars() {
		return (array) $this->sidebars;
	}

	/**
	 * Register sidebars
	 *
	 * @return void
	 */
	final public function register_sidebars() {
		foreach ( $this->get_sidebars() as $id => $name ) {
			$args = [
				'id' => $id,
			];

			if ( is_array( $name ) ) {
				$args = array_merge( $name, $args );
			} else if ( is_string( $name ) ) {
				$args['name'] = $name;
			}

			register_sidebar( $args );
		}
	}
}
