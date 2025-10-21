<?php
/**
 * Theme options registry utilities.
 *
 * Provides helpers for reading and updating the consolidated pf2 options array.
 *
 * @package PF2\Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pf2_options_defaults' ) ) {
	/**
	 * Retrieve default option values.
	 *
	 * @return array<string, mixed>
	 */
	function pf2_options_defaults() {
		$defaults = array(
			'accent_color'         => '#0ea5e9',
			'phone_wa'             => '',
			'footer_note'          => '',
			'cta_text'             => 'Konsultasi via WhatsApp',
			'cta_enabled'          => 1,
			'cta_floating_enabled' => 1,
			'hero_title'           => '',
			'hero_subtitle'        => '',
			'logo_url'             => '',
			'favicon_url'          => '',
			'ai_api_key'           => '',
			'schema_enabled'       => 1,
		);

		/**
		 * Filter the default option set.
		 *
		 * @param array<string, mixed> $defaults Default option map.
		 */
		return apply_filters( 'pf2/options/defaults', $defaults );
	}
}

if ( ! function_exists( 'pf2_options_get_all' ) ) {
	/**
	 * Fetch all options merged with defaults.
	 *
	 * @return array<string, mixed>
	 */
	function pf2_options_get_all() {
		$stored = get_option( 'pf2_options', array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return array_merge( pf2_options_defaults(), $stored );
	}
}

if ( ! function_exists( 'pf2_options_get' ) ) {
	/**
	 * Retrieve an option value by key.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Optional default if the key is missing.
	 * @return mixed
	 */
	function pf2_options_get( $key, $default = '' ) {
		$all = pf2_options_get_all();

		if ( array_key_exists( $key, $all ) ) {
			return $all[ $key ];
		}

		return $default;
	}
}

if ( ! function_exists( 'pf2_options_update' ) ) {
	/**
	 * Persist updated option values.
	 *
	 * @param array<string, mixed> $data New option values.
	 * @return bool
	 */
	function pf2_options_update( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		$current = pf2_options_get_all();
		$merged  = array_merge( $current, $data );

		return update_option( 'pf2_options', $merged, false );
	}
}
