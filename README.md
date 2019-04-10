# Wordpress Helpers

A collection of abstract PHP classes to work easier with WordPress themes with Composer.

## Installation

```
composer require oan/wordpress
```

## AbstractTheme

The class is of Singleton implementation, which means you should not instantiate it with `new MyTheme`. Instead you should use the static method `MyTheme::instance()` to get the **only** available instance.

By default the AbstractTheme will remove som functionality without you setting it; Some default widgets and some meta elements in the header. Look at the file for specifics.

### Example

```php
use OAN\Wordpress\AbstractTheme;

class MyTheme extends AbstractTheme {
	public static function awesome_static_function( $content = '' ) {
		// Do awesome stuff
		return $content;
	}

	public function awesome_function( $content = '' ) {
		// Do awesome stuff
		return $content;
	}
}

MyTheme::instance()

// Add filters and actions use self for static methods
->add_filter( 'the_content', 'self::awesome_static_function' )

// And this for normal methods (chaining works great btw)
->add_filter( 'the_content', 'this::awesome_function' )

// Or other classes
->add_action( 'save_post', 'MyAwesomeClass::my_awesome_method' )
->add_action( 'save_post', [ $awesomeClassInstance, 'my_awesome_method' ] )

// Add nav menus
->add_menu( 'main', __( 'Main menu', 'my-theme' ) )

// Add sidebars
->add_sidebar( 'default-sidebar', __( 'Default sidebar', 'my-theme' ) )

// Add widgets by adding the global class name
->add_widget( 'MyThemeWidget' );
```
