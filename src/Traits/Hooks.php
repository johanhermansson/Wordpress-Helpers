<?php namespace OAN\Wordpress\Traits;

use OAN\Helpers\Callback;

trait Hooks {

	public function __construct() {
		$this->initialize_hooks( 'filter' );
		$this->initialize_hooks( 'action' );
	}

	/**
	 * Initialize hooks
	 *
	 * @param string $type action or filter
	 * @return void
	 */
	private function initialize_hooks( $type = 'action' ) {
		$instance = &$this;
		$property = "{$type}s";
		$getter   = "get_{$property}";
		$setter   = "add_{$type}";

		$hooks = $this->{$getter}();

		if ( empty( $hooks ) or ! is_array( $hooks ) ) {
			$hooks = [];
		}

		foreach ( $hooks as $item ) {
			if ( is_array( $item ) and count( array_filter( array_keys( $item ), 'is_string') ) > 0 ) {
				$item['callback'] = Callback::get( $item['callback'], $instance );

				if ( ! $item['callback'] ) {
					continue;
				}
			} else if ( is_array( $item ) and isset( $item[0] ) and isset( $item[1] ) ) {
				$hook     = $item[0];
				$callback = Callback::get( $item[1], $instance );

				if ( ! $callback ) {
					continue;
				}

				$item = [
					'callback' => $callback,
					'hook'     => $hook,
				];
			} else if ( is_string( $item ) ) {
				if ( is_callable( [ &$this, $item ], true ) ) {
					$item = [
						'callback' => [ &$this, $item ],
						'hook'     => $item,
					];
				} else if ( is_callable( [ get_class( $this ), $item ], true ) ) {
					$item = [
						'callback' => [ get_class( $this ), $item ],
						'hook'     => $item,
					];
				} else if ( is_callable( $item, true ) ) {
					$item = [
						'callback' => $item,
						'hook'     => $item,
					];
				} else {
					continue;
				}
			} else {
				continue;
			}

			$item = array_merge( [
				'arguments' => 2,
				'callback'  => '',
				'hook'      => '',
				'priority'   => 10,
			], $item );

			$setter( $item['hook'], $item['callback'], $item['priority'], $item['arguments'] );
		}
	}

	/**
	 * Add single hook
	 *
	 * @param string $callback
	 * @param string $hook
	 * @param string|callable $callback
	 * @param integer $priority
	 * @param integer $arguments
	 * @return instance
	 */
	final public function add_hook( $type = '', $hook = '', $callback, $priority = 10, $arguments = 2 ) {
		$property = "{$type}s";

		if ( ! isset( static::$$property ) ) {
			return $this;
		}

		if ( ! is_array( static::$$property ) ) {
			static::$$property = [];
		}

		if ( ! empty( $hook ) and empty( $callback ) ) {
			// When callback and hook has the same name
			static::$$property[] = $hook;
		} else if ( ! empty( $hook ) and ! empty( $callback ) ) {
			static::$$property[] = [
				'arguments' => $arguments,
				'callback'  => $callback,
				'hook'      => $hook,
				'priority'  => $priority,
			];
		}

		return $this;
	}

	/**
	 * Add multiple hooks
	 *
	 * @param string $type
	 * @param array $hooks
	 * @return instance
	 */
	final public function add_hooks( $type = '', $hooks = [] ) {
		foreach ( $hooks as $hook ) {
			if ( is_string( $hook ) ) {
				$hook = [
					'hook' => $hook,
				];
			}

			$hook = array_merge( [
				'arguments' => 2,
				'callback'  => '',
				'hook'      => '',
				'priority'  => 10,
			], $hook );

			$this->add_hook( $type, $hook['hook'], $hook['callback'], $hook['priority'], $hook['arguments'] );
		}

		return $this;
	}

	/**
	 * Add single action
	 *
	 * @param string $hook
	 * @param string $callback
	 * @param integer $priority
	 * @param integer $arguments
	 * @return instance
	 */
	final public function add_action( $hook = '', $callback = '', $priority = 10, $arguments = 2 ) {
		return $this->add_hook( 'action', $hook, $callback, $priority, $arguments );
	}

	/**
	 * Add multiple actions
	 *
	 * @param array $actions
	 * @return instance
	 */
	final public function add_actions( $actions = [] ) {
		return $this->add_hooks( 'action', $actions );
	}

	/**
	 * Get all actions
	 *
	 * @return array
	 */
	final public function get_actions() {
		$actions = empty( self::$actions ) ? [] : self::$actions;

		if ( isset( static::$actions ) and static::$actions !== self::$actions ) {
			$actions = array_merge( $actions, static::$actions );
		}

		return $actions;
	}

	/**
	 * Add single filter
	 *
	 * @param string $hook
	 * @param string|callable $callback
	 * @param integer $priority
	 * @param integer $arguments
	 * @return instance
	 */
	final public function add_filter( $hook = '', $callback, $priority = 10, $arguments = 2 ) {
		return $this->add_hook( 'filter', $hook, $callback, $priority, $arguments );
	}

	/**
	 * Add multiple filters
	 *
	 * @param array $filters
	 * @return instance
	 */
	final public function add_filters( $filters = [] ) {
		return $this->add_hooks( 'filter', $filters );
	}

	/**
	 * Get all filters
	 *
	 * @return array
	 */
	final public function get_filters() {
		$filters = empty( self::$filters ) ? [] : self::$filters;

		if ( isset( static::$filters ) and static::$filters !== self::$filters ) {
			$filters = array_merge( $filters, static::$filters );
		}

		return $filters;
	}

}
