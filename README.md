# Wordpress Helpers

A collection of abstract PHP classes to work easier with WordPress themes using Composer.

## Installation

```
composer require oan/wordpress-helpers
```

## AbstractTheme

The class is of Singleton implementation, which means you should not instantiate it with `new MyTheme`. Instead you should use the static method `MyTheme::instance()` to get the **only** available instance.

By default the AbstractTheme will remove som functionality without you setting it; Some default widgets and some meta elements in the header. Look at the file for specifics.

### Example

```php
use OAN\Wordpress\AbstractTheme;

class MyTheme extends AbstractTheme {
	protected static $styles = [
		// Handle will be generated from Theme name
		'style.css',
		// Or pass wp_enqueue_style parameters as an array
		[ 'my-theme-style', 'dist/css/another-style.css' ]
	];

	protected static $scripts = [
		// Handle will be generated from Theme name
		'script.js',
		// Or pass wp_enqueue_script arguments as an array
		[ 'my-theme-scripts', 'dist/js/scripts.js' ]
	];

	protected static $actions = [
		// Will look for method within instance
		// with the same name as action
		'init',
		// Pass add_action arguments as an array
		[ 'init', 'this::init', 10, 0 ],
	];

	protected static $filters = [
		// Will look for method within instance
		// with the same name as action
		'the_content',
		// Pass add_action arguments as an array
		[ 'the_content', 'this::the_content', 10, 1 ],
	];

	protected static $menus = [
		'main-menu' => __( 'Main menu', 'my-theme' ),
	];

	protected static $sidebars = [
		'second-sidebar' => __( 'Second sidebar', 'my-theme' ),
	];

	protected static $widgets = [
		// Activate
		'MyWidgetClass',
		// Deactivate, but won't work because of the line above
		'MyWidgetClass' => false,
		// Deactivate
		'Namespace\AnotherWidgetClass' => false,
	];

	public function init() {
		// Do awesome stuff
	}

	public function the_content( $content = '' ) {
		// Do awesome stuff
		return $content;
	}

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
// Also. There's support for priority and number of arguments.
->add_filter( 'the_content', 'this::awesome_function', 10, 2 )

// Or other classes
->add_action( 'save_post', 'MyAwesomeClass::my_awesome_method' )
->add_action( 'save_post', [ $awesomeClassInstance, 'my_awesome_method' ] )

// Add nav menus
->add_menu( 'footer-menu', __( 'Footer menu', 'my-theme' ) )

// Add sidebars
->add_sidebar( 'second-sidebar', __( 'Second sidebar', 'my-theme' ) )

// Add widgets by adding the global class name
->add_widget( 'MyThemeWidget' )

// To send everything to WordPress,
// you'll have to initialize the instance.
// It's only possible to do this once!
->initialize();
```

## Hooks Trait

If your class need access to `->add_action()` and `->add_filter()`, just use the Hooks trait.

```php
use OAN\Wordpress\Traits\Hooks;

class MyClass {
	use Hooks {
		// Alias the construct to get it running
		// with the below example, otherwise you
		// need to add actions and filters with
		// ->add_action and ->add_filter
		Hooks::__construct as hooks_construct;
	}

	protected $actions = [
		// Each item in the array will be sent
		// to the ->add_action() method
		[ 'after_setup_theme', 'this::my_awesome_function' ],
		// Priority and number of arguments
		[ 'after_setup_theme', 'this::my_awesome_function', 10, 2 ],
		// Single strings will look for a method
		// with the same name within the class
		'after_setup_theme',
	];

	protected $filters = [
		// Use in the same way as with actions
	];

	public function __construct() {
		$this->hooks_construct();
	}
}
```

## Future plans

-   AbstractPostType
-   AbstractTaxonomy
-   Handling of manifest.json
-   ... and probably a lot more
