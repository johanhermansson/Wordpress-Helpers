<?php namespace OAN\Wordpress\Traits;

// PHP functions used
use file_get_contents, json_decode, strpos;

// Wordpress functions used
use apply_filters, get_template_directory;

trait Manifest {

	/**
	 * Saved manifest
	 *
	 * @var null|array
	 */
	protected $manifest = null;

	/**
	 * Get dist folder
	 *
	 * @param boolean $get_path Returns path if true
	 * @return string Path or URI
	 */
	final public function get_dist_folder( $get_path = false ) {
		if ( false !== $get_path ) {
			return apply_filters( 'oan/dist/path', get_template_directory() . '/dist' );
		}

		return apply_filters( 'oan/dist/uri', get_template_directory_uri() . '/dist' );
	}

	/**
	 * Get manifest
	 *
	 * @return array
	 */
	final public function get_manifest() {
		if ( null !== $this->manifest ) {
			return $this->manifest;
		}

		$path     = apply_filters( 'oan/manifest/path', $this->get_dist_folder( true ) . '/manifest.json' );
		$contents = apply_filters( 'oan/manifest/contents', @file_get_contents( $path ) );
		$manifest = apply_filters( 'oan/manifest/transform', (array) json_decode( $contents ) );

		$this->manifest = $manifest;

		return $this->manifest;
	}

	/**
	 * Get manifest asset
	 *
	 * @param string $asset Manifest asset key
	 * @param boolean $use_strpos Search key with strpos
	 * @param boolean $get_path Get path if true
	 * @return string Path or URI to asset file
	 */
	final public function get_manifest_asset( $asset = '', $use_strpos = false, $get_path = false ) {
		$name = apply_filters( 'oan/manifest/asset_name', $asset );
		$name = apply_filters( "oan/manifest/asset_name/key={$asset}", $asset );

		$manifest = $this->get_manifest();

		if ( null !== $manifest ) {
			if ( false !== $use_strpos ) {
				foreach ( $manifest as $key => $value ) {
					if ( false !== strpos( $key, $name ) ) {
						$asset = $value;
						break;
					}
				}
			} else if ( isset( $manifest[ $asset ] ) ) {
				$asset = $manifest[ $asset ];
			}
		}

		$asset = apply_filters( 'oan/manifest/asset', $asset );
		$asset = apply_filters( "oan/manifest/asset/key={$name}", $asset );

		$asset = $this->get_dist_folder( $get_path ) . '/' . $asset;

		if ( false !== $get_path ) {
			$type = 'path';
		} else {
			$type = 'uri';
		}

		$asset = apply_filters( "oan/manifest/asset_{$type}", $asset );
		$asset = apply_filters( "oan/manifest/asset_{$type}/key={$name}", $asset );

		return $asset;
	}

}
