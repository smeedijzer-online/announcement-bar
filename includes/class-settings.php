<?php

class Announcement_Settings {

	// Define a property to hold the plugin options
	private array $options;

	// Define a constructor to initialize the plugin options
	public function __construct() {
		// Load the plugin options from the database
		$this->options = get_option( 'announcement_plugin_options' ) ?: [];

		// Register the settings page with WordPress
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		// Register the settings with WordPress
				add_action( 'admin_init', array( $this, 'register_settings' ) );

				// Add save success and save error notifications
				add_action( 'admin_notices', array( $this, 'save_notification' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			}

			public function enqueue_admin_scripts() {
				wp_enqueue_style( 'announcement-admin-style', ANNOUNCEMENT_BAR_PLUGIN_URL . 'build/admin-style.css' );
				wp_enqueue_script( 'announcement-admin-script', ANNOUNCEMENT_BAR_PLUGIN_URL . 'build/admin-script.js', array(), false, true );
			}

			// Define a method to add the plugin settings page to the WordPress admin menu
			public function add_plugin_page() {
				add_menu_page( __( 'Notification Settings', 'announcement-bar' ),
							   __( 'Notification', 'announcement-bar' ),
							   'edit_pages',
							   'announcement',
							   array( $this, 'create_admin_page' ),
							   'dashicons-megaphone',
							   999 );
			}

			// Define a method to create the plugin settings page
			public function create_admin_page() {
				?>
				<div class="admin-announcement-bar-wrap">
					<h2><?php
						_e( 'Notification Settings', 'announcement-bar' ); ?></h2>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'announcement_plugin_options' ); ?>
						<?php
						do_settings_sections( 'announcement' ); ?>
						<?php
						submit_button( __( 'Save changes', 'announcement-bar' ) ); ?>
					</form>
				</div>
				<?php
			}

			// Define a method to register the plugin settings with WordPress
			public function register_settings() {
				register_setting( 'announcement_plugin_options', 'announcement_plugin_options', array( $this, 'sanitize' ) );

				add_settings_section( 'announcement_section',
									  '' , //__( 'Notificatie Instellingen', 'site-announcement' ),
									  array( $this, 'print_notification_label' ),
									  'announcement' );

				$fields = array(
					array(
						'id'    => 'enabled',
						'label' => __( 'Notification On/Off', 'announcement-bar' ),
						'type'  => 'checkbox',
					),
					array(
						'id'    => 'message',
						'label' => __( 'Text box', 'announcement-bar' ),
						'type'  => 'textarea',
					),
//					array(
//						'id'    => 'from_date',
//						'label' => __( 'From date/time', 'site-announcement' ),
//						'type'  => 'datetime-local',
//					),
//					array(
//						'id'    => 'to_date',
//						'label' => __( 'To date/time', 'site-announcement' ),
//						'type'  => 'datetime-local',
//					),
					array(
						'id'      => 'type',
						'label'   => __( 'Style type', 'announcement-bar' ),
						'type'    => 'radio',
						'options' => array(
							'notice'  => __( 'Notice', 'announcement-bar' ),
							'success' => __( 'Success', 'announcement-bar' ),
							'warning' => __( 'Warning', 'announcement-bar' ),
							'danger'  => __( 'Danger', 'announcement-bar' ),
						),
						'default' => 'notice',
					),
					array(
						'id'    => 'show_icon',
						'label' => __( 'Show icon', 'announcement-bar' ),
						'type'  => 'checkbox',
					),
				);

				foreach ( $fields as $field ) {
					add_settings_field( $field['id'],
										$field['label'],
										array( $this, 'print_settings_field' ),
										'announcement',
										'announcement_section',
										$field );
				}
			}


			public function sanitize( $input ) {
				$output = array();

				if ( isset( $input['enabled'] ) ) {
					$output['enabled'] = sanitize_text_field( $input['enabled'] );
				}

				if ( isset( $input['message'] ) ) {
					$output['message'] = sanitize_textarea_field( $input['message'] );
				}

				if ( isset( $input['from_date'] ) ) {
					$output['from_date'] = sanitize_text_field( $input['from_date'] );
				}

				if ( isset( $input['to_date'] ) ) {
					$output['to_date'] = sanitize_text_field( $input['to_date'] );
				}

				if ( isset( $input['type'] ) ) {
					$output['type'] = sanitize_text_field( $input['type'] );
				}

				if ( isset( $input['show_icon'] ) ) {
					$output['show_icon'] = sanitize_text_field( $input['show_icon'] );
				}

				if ( isset( $output['from_date'] ) ) {
					$output['from_date'] = date( 'Y-m-d H:i:s', strtotime( $output['from_date'] ) );
				}

				if ( isset( $output['to_date'] ) ) {
					$output['to_date'] = date( 'Y-m-d H:i:s', strtotime( $output['to_date'] ) );
				}

				// Add uni time field to output array
				$output['timestamp'] = time();

				return $output;
			}

			public function print_settings_field( $args ) {
				$value = $this->options[ $args['id'] ] ?? '';

				switch ( $args['type'] ) {
					case 'checkbox':
						printf( '<input type="checkbox" id="%s" name="announcement_plugin_options[%s]" value="1" %s>',
								$args['id'],
								$args['id'],
								$value === 1 ? 'checked' : '' );
						break;
					case 'textarea':
						printf( '<textarea id="%s" name="announcement_plugin_options[%s]" rows="5" cols="50">%s</textarea>',
								$args['id'],
								$args['id'],
								esc_textarea( $value ) );
						break;
					case 'datetime-local':
						printf( '<input type="datetime-local" id="%s" name="announcement_plugin_options[%s]" value="%s">',
								$args['id'],
								$args['id'],
								$value );
						break;
					case 'radio':
						foreach ( $args['options'] as $option_value => $option_label ) {
							printf( '<label><input type="radio" id="%s_%s" name="announcement_plugin_options[%s]" value="%s" %s>%s</label><br>',
									$args['id'],
									$option_value,
									$args['id'],
									$option_value,
									$value === $option_value ? 'checked' : '',
									$option_label );
						}
						break;
				}
			}

			public function print_notification_label() {
				echo __( 'Enter notification below:', 'announcement-bar' );
			}

			public function save_notification() {
				if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
					add_settings_error( 'announcement_messages', 'announcement_message', __( 'Settings saved.', 'announcement-bar' ), 'updated' );
				} elseif ( isset( $_GET['settings-updated'] ) && ! $_GET['settings-updated'] ) {
					add_settings_error( 'announcement_messages', 'announcement_message', __( 'An error occurred while saving the settings.', 'announcement-bar' ), 'error' );
				}

				settings_errors( 'announcement_messages' );
			}
}
