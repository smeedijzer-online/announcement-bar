<?php

class Announcement_Frontend
{
    /**
     * @var Holds the plugin version.
     * @since 1.0
     */
    public $version = '1.0.0';

    /**
     * @var Holds the plugin default settings.
     * @since 1.0
     */
    public $default_settings = array();


    // Define a property to hold the plugin options
    private $settings;


    /**
     * Constructor
     */
    public function __construct()
    {
        // Load the plugin options from the database
        $this->settings = get_option('announcement_plugin_options');

        // Define default settings.
        $default_settings = array(
            // main settings.
            'enabled'                 => false,
            'allow_collapse'          => true, //false,
            'is_sticky'               => false,
            'show_icon'               => false,
            'type'                    => 'notice',
            'close_icon'              => 'plain',
            'message'                 => '',
            // button settings.
            'button_text'             => '',
            'button_link'             => '#',
            'button_nofollow'         => false,
            'button_sponsored'        => false,
            'button_target_blank'     => false,
            // styling options.
            'background_color'        => '',
            'text_color'              => '',
            'text_align'              => '', //'center',
            'font_size'               => '',
            'button_background_color' => '',
            'button_text_color'       => '',
            'button_padding'          => '',

            // Add timestamp field to output array
            'timestamp' => 'off',
        );

        // Update class default_settings var.
        $this->default_settings = $default_settings;

        add_action('wp_body_open', [$this, 'display_notification']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);


        // Add body class if notification bar is enabled.
        add_filter('body_class', array( $this, 'add_body_class' ));
    }

    /**
     * Add body class if notification bar is enabled.
     *
     * @since  1.2
     * @access public
     * @return array $class
     */
    public function add_body_class($class)
    {

        if ($this->is_enabled()) {
            $class[] = 'has-announcement-bar';
        }

        return $class;
    }
    /**
     * Get plugin settings.
     *
     * @since  1.0
     * @access public
     * @return $settings | array
     */
    public function get_settings()
    {
//        if ( ! empty( $this->settings ) && ! is_customize_preview() ) {
//            return $this->settings;
//        }

//        /**
//         * Filters the notification bar settings.
//         *
//         * @param array $settings
//         * @param object $this Current class object.
//         */
//        $this->settings = apply_filters( 'announcement_bar_settings', get_theme_mod( 'easy_nb' ), $this );
        $this->settings = wp_parse_args($this->settings, $this->default_settings);

        return $this->settings;
    }

    /**
     * Get plugin setting.
     *
     * @since  1.0
     * @access public
     * @return $settings | array
     */
    public function get_setting($name, $fallback = false)
    {
        $this->get_settings();
        if (isset($this->settings[ $name ])) {
            return $this->settings[ $name ];
        }
        if ($fallback) {
            return $this->defaults[ $name ];
        }
    }

    /**
     * Check if the notification bar is enabled.
     *
     * @since  1.0
     * @access public
     * @return $enabled | bool
     */
    public function is_enabled()
    {
        $enabled = wp_validate_boolean($this->get_setting('enabled'));

        return (bool) $enabled;
    }

    /**
     * Display the close icon.
     *
     * @since  1.4
     * @access public
     */
    public function close_icon()
    {
        $icon_style = $this->get_setting('close_icon', true);

        switch ($icon_style) {
            case 'outline':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none" opacity=".87"/><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.59-13L12 10.59 8.41 7 7 8.41 10.59 12 7 15.59 8.41 17 12 13.41 15.59 17 17 15.59 13.41 12 17 8.41z"/></svg>';
                break;
            case 'plain':
            default:
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>';
                break;
        }

        $icon_size = 24;

        $icon = str_replace('<svg', '<svg width="' . absint($icon_size) .'px" height="' . absint($icon_size) .'px"', $icon);

        return $icon;
    }

    public function get_icon($icon)
    {

        switch ($icon) {
            case 'close-outline':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none" opacity=".87"/><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.59-13L12 10.59 8.41 7 7 8.41 10.59 12 7 15.59 8.41 17 12 13.41 15.59 17 17 15.59 13.41 12 17 8.41z"/></svg>';
                break;
            case 'close-plain':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>';
                break;
            case 'notice':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                break;
            case 'success':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                break;
            case 'warning':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                break;
            case 'danger':
                $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                break;
            default:
                $icon = null;
        }

        if (!$icon) {
            return '';
        }

        $icon_size = 24;

        $icon = str_replace('<svg', '<svg width="' . absint($icon_size) .'px" height="' . absint($icon_size) .'px"', $icon);

        return $icon;
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function enqueue_scripts()
    {
		/**
		 * Filters whether the plugin should load it's CSS files or not.
		 *
		 * @param bool $check
		 * @param object $this Current class object.
		 */
		$enqueue_css_check = apply_filters( 'easy_notification_bar_enqueue_css', true, $this );
		if ( $enqueue_css_check ) {
			wp_enqueue_style( 'announcement-bar', ANNOUNCEMENT_BAR_PLUGIN_URL . 'build/style.css' );
			wp_enqueue_script( 'announcement-bar', ANNOUNCEMENT_BAR_PLUGIN_URL . 'build/script.js' );
		}
    }


    /**
     * Display Notification Bar.
     *
     * @since  1.0
     * @access public
     * @return void
     */
    public function display_notification()
    {
        if ($this->is_enabled()) {
            $message = $this->get_setting('message');
            $button_link = $this->get_setting('button_link');
            $button_text = $this->get_setting('button_text');

            if (! $message && ( ! $button_link && ! $button_text )) {
                return;
            }
            ?>

            <div class="<?php echo esc_attr(join(' ', $this->get_wrap_class())); ?>" data-identifier="<?php echo $this->get_setting('timestamp') ?>">

                <div class="<?php echo esc_attr(join(' ', $this->get_container_class())); ?>">
					<?php
					$show_icon = $this->get_setting('show_icon', true);
					$type = $this->get_setting('type', true);
					$icon = $this->get_icon($type);

					// Display Message
					if ($show_icon && $icon) { ?>
						<span class="announcement-bar-icon">
                            <?php echo $icon; ?>
                        </span><!-- .announcement-bar-icon -->
						<?php
					} ?>

					<?php
                    // Display Message
                    if ($message) { ?>
                        <div class="announcement-bar-message">
                            <?php echo apply_filters('announcement_bar_message', $message, $this); ?>
                        </div><!-- .announcement-bar-message -->
                    <?php } ?>

                    <?php
                    // Display button
                    if ($button_text && $button_link) { ?>
                        <div class="announcement-bar-button">
                            <a href="<?php echo esc_url($button_link); ?>"<?php $this->button_rel() . $this->button_target_blank(); ?>><?php echo wp_kses_post($button_text); ?></a>
                        </div><!-- .announcement-bar-button -->
                    <?php } ?>

                </div>

                <?php if (true === $this->get_setting('allow_collapse')) { ?>
                    <a class="announcement-bar-close" href="#" aria-label="<?php esc_html_e('Close notification', 'announcement-bar'); ?>"><?php echo $this->close_icon(); ?></a>
                <?php } ?>

            </div><!-- .announcement-bar -->

            <?php
        }
    }


    /**
     * Get wrap class.
     *
     * @since  1.4.2
     * @access public
     * @return $classes | array
     */
    public function get_wrap_class()
    {
        $class = array( 'announcement-bar' );

        if ($type = $this->get_setting('type', true)) {
            $class[] = 'announcement-bar--' . sanitize_html_class($type);
        }

       // if ($align = $this->get_setting('text_align', true)) {
        //    $class[] = 'announcement-bar--align_' . sanitize_html_class($align);
       // }

        if (true === $this->get_setting('is_sticky')) {
            $class[] = 'announcement-bar--sticky';
        }

        if (true === $this->get_setting('allow_collapse')) {
            if (! is_customize_preview()) {
                $class[] = 'announcement-bar--hidden';
            }
            $class[] = 'announcement-bar--collapsible';
        }

        return $class;
    }

    /**
     * Get container class.
     *
     * @since  1.0
     * @access public
     * @return $class | array
     */
    public function get_container_class()
    {
        $class = array( 'announcement-bar-container' );

        //$class[] = 'enb-text' . sanitize_html_class($this->get_setting('text_align', true));

        return $class;
    }

    /**
     * Echos rel="nofollow" tag for button if enabled.
     *
     * @since  1.0
     * @access public
     */
    public function button_rel()
    {

        $rel = array();

        if (wp_validate_boolean($this->get_setting('button_nofollow'))) {
            if (wp_validate_boolean($this->get_setting('button_target_blank'))) {
                $rel[] = 'nofollow';
                $rel[] = 'noopener';
            } else {
                $rel[] = 'nofollow';
            }
        }

        if (wp_validate_boolean($this->get_setting('button_sponsored'))) {
            $rel[] = 'sponsored';
        }

        if (! empty($rel) && is_array($rel)) {
            $rel = apply_filters('easy_notification_bar_button_rel', implode(' ', $rel));
            echo ' rel="'. esc_attr($rel) . '"';
        }
    }

    /**
     * Echos target="blank" tag for button if enabled.
     *
     * @since  1.0
     * @access public
     */
    public function button_target_blank()
    {

        if (wp_validate_boolean($this->get_setting('button_target_blank'))) {
            if (wp_validate_boolean($this->get_setting('button_nofollow'))) {
                echo ' target="_blank"';
            } else {
                echo ' rel="noreferrer" target="_blank"';
            }
        }
    }
}
