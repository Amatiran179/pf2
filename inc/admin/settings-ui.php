<?php
/**
 * Theme settings admin UI.
 *
 * Provides Settings API registrations and the settings page renderer.
 *
 * @package PF2\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pf2_admin_get_settings_schema' ) ) {
	/**
	 * Describe available settings tabs and fields.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function pf2_admin_get_settings_schema() {
		return array(
			'general'      => array(
				'title'       => __( 'General', 'pf2' ),
				'description' => __( 'Global defaults for brand accent, contact, and footer copy.', 'pf2' ),
				'fields'      => array(
					'accent_color' => array(
						'label'       => __( 'Site Accent Color', 'pf2' ),
						'control'     => 'text',
						'input_type'  => 'text',
						'class'       => 'regular-text code',
						'sanitize'    => 'color',
						'description' => __( 'Hex color value used for theme accents (e.g. #0ea5e9).', 'pf2' ),
						'attributes'  => array(
							'pattern'     => '^#([A-Fa-f0-9]{3}){1,2}$',
							'placeholder' => '#0ea5e9',
						),
					),
					'phone_wa' => array(
						'label'       => __( 'Phone (WhatsApp)', 'pf2' ),
						'control'     => 'text',
						'input_type'  => 'text',
						'sanitize'    => 'string',
						'description' => __( 'Primary WhatsApp number for CTAs. Include country code.', 'pf2' ),
					),
					'footer_note' => array(
						'label'       => __( 'Footer Note', 'pf2' ),
						'control'     => 'textarea',
						'sanitize'    => 'textarea',
						'description' => __( 'Optional footer text for copyright or compliance notices.', 'pf2' ),
						'attributes'  => array(
							'rows' => 3,
						),
					),
				),
			),
			'cta'          => array(
				'title'       => __( 'CTA', 'pf2' ),
				'description' => __( 'Default call-to-action messaging and toggles.', 'pf2' ),
				'fields'      => array(
					'cta_text' => array(
						'label'      => __( 'Default CTA Text', 'pf2' ),
						'control'    => 'text',
						'input_type' => 'text',
						'sanitize'   => 'string',
					),
					'cta_enabled' => array(
						'label'       => __( 'CTA Enabled', 'pf2' ),
						'control'     => 'checkbox',
						'sanitize'    => 'bool',
						'description' => __( 'Disable to hide all theme-managed CTAs.', 'pf2' ),
					),
					'cta_floating_enabled' => array(
						'label'       => __( 'Floating Button Enabled', 'pf2' ),
						'control'     => 'checkbox',
						'sanitize'    => 'bool',
						'description' => __( 'Toggle the floating WhatsApp button on eligible templates.', 'pf2' ),
					),
				),
			),
			'hero'         => array(
				'title'       => __( 'Hero', 'pf2' ),
				'description' => __( 'Customize hero defaults used across landing pages.', 'pf2' ),
				'fields'      => array(
					'hero_title' => array(
						'label'      => __( 'Hero Title', 'pf2' ),
						'control'    => 'text',
						'input_type' => 'text',
						'sanitize'   => 'string',
					),
					'hero_subtitle' => array(
						'label'      => __( 'Hero Subtitle', 'pf2' ),
						'control'    => 'text',
						'input_type' => 'text',
						'sanitize'   => 'string',
					),
				),
			),
			'branding'     => array(
				'title'       => __( 'Branding', 'pf2' ),
				'description' => __( 'Manage brand imagery surfaced within theme templates.', 'pf2' ),
				'fields'      => array(
					'logo_url' => array(
						'label'       => __( 'Logo URL', 'pf2' ),
						'control'     => 'text',
						'input_type'  => 'url',
						'sanitize'    => 'url',
						'description' => __( 'Direct URL to the primary brand logo.', 'pf2' ),
					),
					'favicon_url' => array(
						'label'       => __( 'Favicon URL', 'pf2' ),
						'control'     => 'text',
						'input_type'  => 'url',
						'sanitize'    => 'url',
						'description' => __( 'Direct URL to the favicon image.', 'pf2' ),
					),
				),
			),
			'integrations' => array(
				'title'       => __( 'Integrations', 'pf2' ),
				'description' => __( 'Connect external services and structured data output.', 'pf2' ),
				'fields'      => array(
					'ai_api_key' => array(
						'label'       => __( 'AI API Key', 'pf2' ),
						'control'     => 'text',
						'input_type'  => 'text',
						'sanitize'    => 'string',
						'description' => __( 'Stored securely for future AI integrations (Batch 11).', 'pf2' ),
					),
					'schema_enabled' => array(
						'label'       => __( 'Enable Schema Output', 'pf2' ),
						'control'     => 'checkbox',
						'sanitize'    => 'bool',
						'description' => __( 'Override automatic detection and force schema output.', 'pf2' ),
					),
				),
			),
		);
	}
}

if ( ! function_exists( 'pf2_admin_register_settings' ) ) {
	/**
	 * Register settings, sections, and fields.
	 *
	 * @return void
	 */
	function pf2_admin_register_settings() {
		register_setting(
			'pf2_options_group',
			'pf2_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'pf2_options_sanitize',
				'default'           => pf2_options_defaults(),
			)
		);

		$schema = pf2_admin_get_settings_schema();

		foreach ( $schema as $tab => $config ) {
			$page    = 'pf2_' . $tab;
			$section = $page . '_section';

			add_settings_section(
				$section,
				'',
				'__return_false',
				$page
			);

			if ( empty( $config['fields'] ) || ! is_array( $config['fields'] ) ) {
				continue;
			}

			foreach ( $config['fields'] as $key => $field ) {
				$field_args               = $field;
				$field_args['key']        = $key;
				$field_args['tab']        = $tab;
				$field_args['label_for']  = 'pf2_options_' . $key;
				$field_args['control']    = isset( $field['control'] ) ? $field['control'] : 'text';
				$field_args['input_type'] = isset( $field['input_type'] ) ? $field['input_type'] : 'text';

				add_settings_field(
					'pf2_' . $key,
					isset( $field['label'] ) ? $field['label'] : '',
					'pf2_admin_render_field',
					$page,
					$section,
					$field_args
				);
			}
		}
	}
}
add_action( 'admin_init', 'pf2_admin_register_settings' );

if ( ! function_exists( 'pf2_admin_render_field' ) ) {
	/**
	 * Render a settings field control.
	 *
	 * @param array<string, mixed> $args Field arguments.
	 * @return void
	 */
	function pf2_admin_render_field( $args ) {
		$defaults = pf2_options_defaults();
		$key      = isset( $args['key'] ) ? $args['key'] : '';

		if ( '' === $key ) {
			return;
		}

		$value      = pf2_options_get( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
		$control    = isset( $args['control'] ) ? $args['control'] : 'text';
		$input_type = isset( $args['input_type'] ) ? $args['input_type'] : 'text';
		$id         = 'pf2_options_' . $key;
		$class      = isset( $args['class'] ) ? $args['class'] : 'regular-text';
		$attributes = array();

		if ( isset( $args['attributes'] ) && is_array( $args['attributes'] ) ) {
			$attributes = $args['attributes'];
		}

		switch ( $control ) {
                       case 'checkbox':
                               printf(
                                       '<input type="hidden" name="pf2_options[%1$s]" value="0" />' .
                                       '<input type="checkbox" id="%2$s" name="pf2_options[%1$s]" value="1" %3$s />',
                                       esc_attr( $key ),
                                       esc_attr( $id ),
                                       checked( (int) $value, 1, false )
                               );
                               break;
			case 'textarea':
				$rows = isset( $attributes['rows'] ) ? (int) $attributes['rows'] : 4;
				printf(
					'<textarea id="%1$s" name="pf2_options[%2$s]" rows="%3$d" class="large-text">%4$s</textarea>',
					esc_attr( $id ),
					esc_attr( $key ),
					max( 2, $rows ),
					esc_textarea( $value )
				);
				break;
			default:
				$attr_html = '';
				foreach ( $attributes as $attr_key => $attr_value ) {
					$attr_html .= sprintf( ' %1$s="%2$s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
				}
				printf(
					'<input type="%1$s" id="%2$s" name="pf2_options[%3$s]" value="%4$s" class="%5$s"%6$s />',
					esc_attr( $input_type ),
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( $value ),
					esc_attr( $class ),
					$attr_html
				);
				break;
		}

		if ( ! empty( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}
}

if ( ! function_exists( 'pf2_options_sanitize' ) ) {
	/**
	 * Sanitize pf2 options prior to persistence.
	 *
	 * @param mixed $input Raw input data.
	 * @return array<string, mixed>
	 */
	function pf2_options_sanitize( $input ) {
		$defaults  = pf2_options_defaults();
		$schema    = pf2_admin_get_settings_schema();
		$sanitized = pf2_options_get_all();

		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$active_tab = 'general';

		if ( isset( $_POST['pf2_options_active_tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$active_tab = sanitize_key( wp_unslash( $_POST['pf2_options_active_tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		if ( ! isset( $schema[ $active_tab ] ) ) {
			$active_tab = 'general';
		}

		if ( empty( $schema[ $active_tab ]['fields'] ) || ! is_array( $schema[ $active_tab ]['fields'] ) ) {
			return $sanitized;
		}

		foreach ( $schema[ $active_tab ]['fields'] as $key => $field ) {
			$mode      = isset( $field['sanitize'] ) ? $field['sanitize'] : 'string';
			$default   = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
			$has_value = array_key_exists( $key, $input );

			switch ( $mode ) {
                               case 'bool':
                                       if ( ! $has_value ) {
                                               break;
                                       }

                                       $raw = wp_unslash( $input[ $key ] );

                                       if ( is_array( $raw ) ) {
                                               $raw = end( $raw );
                                       }

                                       $sanitized[ $key ] = filter_var( $raw, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0;
                                       break;
			        case 'url':
			                if ( ! $has_value ) {
			                        break;
			                }

			                $value = trim( (string) wp_unslash( $input[ $key ] ) );
			                if ( '' !== $value ) {
			                        $sanitized[ $key ] = esc_url_raw( $value );
			                } else {
			                        $sanitized[ $key ] = '';
			                }
			                break;
			        case 'color':
			                if ( ! $has_value ) {
			                        break;
			                }

			                $value = wp_unslash( $input[ $key ] );
			                $value = is_string( $value ) ? trim( $value ) : '';
			                if ( is_string( $value ) && preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $value ) ) {
			                        $sanitized[ $key ] = strtolower( $value );
			                } else {
			                        $sanitized[ $key ] = $default;
			                }
			                break;
			        case 'textarea':
			                if ( ! $has_value ) {
			                        break;
			                }

			                $sanitized[ $key ] = sanitize_textarea_field( wp_unslash( $input[ $key ] ) );
			                break;
			        default:
			                if ( ! $has_value ) {
			                        break;
			                }

			                $sanitized[ $key ] = sanitize_text_field( wp_unslash( $input[ $key ] ) );
			                break;
			}
		}

		return $sanitized;
	}
}

if ( ! function_exists( 'pf2_admin_render_settings_page' ) ) {
	/**
	 * Output the PutraFiber settings page.
	 *
	 * @return void
	 */
	function pf2_admin_render_settings_page() {
		$tabs       = pf2_admin_get_settings_schema();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! isset( $tabs[ $active_tab ] ) ) {
			$active_tab = 'general';
		}

		$base_url = menu_page_url( 'pf2', false );

		echo '<div class="wrap pf2-settings">';
		echo '<h1 class="pf2-settings__title">' . esc_html__( 'PutraFiber Settings', 'pf2' ) . '</h1>';
		settings_errors();
		echo '<h2 class="nav-tab-wrapper pf2-settings__tabs">';

		foreach ( $tabs as $slug => $config ) {
			$tab_url = $base_url;
			if ( 'general' === $slug ) {
				$tab_url = remove_query_arg( 'tab', $base_url );
			} else {
				$tab_url = add_query_arg( 'tab', $slug, $base_url );
			}

			$classes = 'nav-tab';
			if ( $slug === $active_tab ) {
				$classes .= ' nav-tab-active';
			}

			printf(
				'<a href="%1$s" class="%2$s">%3$s</a>',
				esc_url( $tab_url ),
				esc_attr( $classes ),
				esc_html( $config['title'] )
			);
		}

		echo '</h2>';

		if ( ! empty( $tabs[ $active_tab ]['description'] ) ) {
			printf( '<p class="pf2-settings__intro">%s</p>', esc_html( $tabs[ $active_tab ]['description'] ) );
		}

		echo '<form method="post" action="options.php" class="pf2-settings__form">';
		settings_fields( 'pf2_options_group' );
		printf(
			'<input type="hidden" name="pf2_options_active_tab" value="%s" />',
			esc_attr( $active_tab )
		);
		echo '<div class="pf2-settings__card">';
		echo '<table class="form-table pf2-settings__table" role="presentation">';
		do_settings_sections( 'pf2_' . $active_tab );
		echo '</table>';
		submit_button( __( 'Save Changes', 'pf2' ) );
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}
}
