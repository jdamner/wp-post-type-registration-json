<?php
/**
 * Plugin Name: Post Type Registration JSON
 * Description: Register Post Types from a JSON file
 * Version: 1.0.0
 * Author: James Amner <jdamner@me.com>
 * Author URI: https://amner.me
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostTypeRegistrationJson {

	/**
	 * Init Hooks
	 */
	public function init(): void {
		add_action( 'init', [ $this, 'register_post_types' ] );
	}

	/**
	 * Register Post Types
	 * 
	 * @return void
	 */
	public function register_post_types(): void {

		$path = get_template_directory() . '/post-types.json';
		if ( ! file_exists( $path ) ) {
			return;
		}
		$file = file_get_contents( $path ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		$data = json_decode( $file ?: 'invalid file', true );
		if ( ! is_array( $data ) ) {
			return;
		}

		foreach ( $data['post_types'] ?? [] as $name => $args ) {
			$args = wp_parse_args(
				$args,
				[
					'show_in_rest' => true,
					'public' => true,
					'template' => $this->blocks_to_template( 
						$this->get_blocks_from_file( $name ) ?? []
					) ?? [],
				]
			);

			register_post_type( $name, $args ); // phpcs:ignore WordPress.NamingConventions.ValidPostTypeSlug.NotStringLiteral
		}
	}

	/**
	 * Locate and Parse Template for Post Type
	 * 
	 * @param string $name Post Type Name.
	 * 
	 * @return array
	 */
	public function get_blocks_from_file( string $name ): ?array {

		if ( ! is_admin() ) {
			// Template is only used in the editor, so we don't need
			// to waste time parsing it for the front end.
			return null;
		}

		$path = get_template_directory() . '/post-type-templates/' . $name . '.html';
		if ( ! file_exists( $path ) ) {
			return null;
		}
		$file = file_get_contents( $path ); // phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown

		if ( ! $file ) {
			return null;
		}

		return parse_blocks( $file );
	}

	/**
	 * Transform Blocks to Template
	 * 
	 * @param array $blocks Blocks.
	 * 
	 * @return array|null
	 */
	public function blocks_to_template( array $blocks ): ?array {
		
		$template = [];

		foreach ( $blocks as $block ) {
			$template[] = $this->block_to_template( $block );
		}

		return array_values( array_filter( $template ) );
	}

	/**
	 * Convert a block to a template array
	 * 
	 * @param array $block Block.
	 * 
	 * @return array|null
	 */
	public function block_to_template( array $block ): ?array {
		$name = $block['blockName'] ?? '';
		if ( ! $name ) {
			return null;
		}

		return [ $name, $block['attrs'] ?? [], $this->blocks_to_template( $block['innerBlocks'] ?? [] ) ?? [] ];
	}
}

// Initialise the plugin.
add_action(
	'plugins_loaded', 
	function () { 
		( new PostTypeRegistrationJson() )->init();
	}
);
