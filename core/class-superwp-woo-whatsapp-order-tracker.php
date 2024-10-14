<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Superwp_Woo_Whatsapp_Order_Tracker' ) ) :

/**
 * Main Superwp_Woo_Whatsapp_Order_Tracker Class.
 *
 * @package     SUPERWPWOT
 * @subpackage  Classes/Superwp_Woo_Whatsapp_Order_Tracker
 * @since       1.0.03
 * @author      Thiarara SuperWP
 */
final class Superwp_Woo_Whatsapp_Order_Tracker {

    /**
     * The real instance
     *
     * @access  private
     * @since   1.0.03
     * @var     object|Superwp_Woo_Whatsapp_Order_Tracker
     */
    private static $instance;

    /**
     * SUPERWPWOT helpers object.
     *
     * @access  public
     * @since   1.0.03
     * @var     object|Superwp_Woo_Whatsapp_Order_Tracker_Helpers
     */
    public $helpers;

    /**
     * SUPERWPWOT settings object.
     *
     * @access  public
     * @since   1.0.03
     * @var     object|Superwp_Woo_Whatsapp_Order_Tracker_Settings
     */
    public $settings;

    /**
     * Throw error on object clone.
     *
     * Cloning instances of the class is forbidden.
     *
     * @access  public
     * @since   1.0.03
     * @return  void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'superwp-woo-whatsapp-order-tracker' ), '1.0.03' );
    }

    /**
     * Disable unserializing of the class.
     *
     * @access  public
     * @since   1.0.03
     * @return  void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'superwp-woo-whatsapp-order-tracker' ), '1.0.03' );
    }

    /**
     * Main Superwp_Woo_Whatsapp_Order_Tracker Instance.
     *
     * Insures that only one instance of Superwp_Woo_Whatsapp_Order_Tracker exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @access      public
     * @since       1.0.03
     * @static
     * @return      object|Superwp_Woo_Whatsapp_Order_Tracker    The one true Superwp_Woo_Whatsapp_Order_Tracker
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Superwp_Woo_Whatsapp_Order_Tracker ) ) {
            self::$instance                 = new Superwp_Woo_Whatsapp_Order_Tracker;
            self::$instance->base_hooks();
            self::$instance->includes();
            self::$instance->helpers        = new Superwp_Woo_Whatsapp_Order_Tracker_Helpers();
            self::$instance->settings       = new Superwp_Woo_Whatsapp_Order_Tracker_Settings();

            //Fire the plugin logic
            new Superwp_Woo_Whatsapp_Order_Tracker_Run();

            /**
             * Fire a custom action to allow dependencies
             * after the successful plugin setup
             */
            do_action( 'SUPERWPWOT/plugin_loaded' );
        }

        return self::$instance;
    }

    /**
     * Include required files.
     *
     * @access  private
     * @since   1.0.03
     * @return  void
     */
    private function includes() {
        require_once SUPERWPWOT_PLUGIN_DIR . 'core/includes/classes/class-superwp-woo-whatsapp-order-tracker-helpers.php';
        require_once SUPERWPWOT_PLUGIN_DIR . 'core/includes/classes/class-superwp-woo-whatsapp-order-tracker-settings.php';
        require_once SUPERWPWOT_PLUGIN_DIR . 'core/includes/classes/class-superwp-woo-whatsapp-order-tracker-run.php';
    }

    /**
     * Add base hooks for the core functionality
     *
     * @access  private
     * @since   1.0.03
     * @return  void
     */
    private function base_hooks() {
        add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
    }

    /**
     * Loads the plugin language files.
     *
     * @access  public
     * @since   1.0.03
     * @return  void
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'superwp-woo-whatsapp-order-tracker', FALSE, dirname( plugin_basename( SUPERWPWOT_PLUGIN_FILE ) ) . '/languages/' );
    }

    public function __construct() {
        add_shortcode('superwp_woo_order_tracker', array($this, 'display_order_tracking_form'));
        add_action('admin_menu', array($this, 'superwp_add_admin_menu'));
        add_action('admin_init', array($this, 'superwp_register_settings'));
        add_action('wp_ajax_superwp_track_order', array($this, 'ajax_track_order'));
        add_action('wp_ajax_nopriv_superwp_track_order', array($this, 'ajax_track_order'));
        add_shortcode('order_total', array($this, 'order_total_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_superwp_ajax_update', array($this, 'handle_ajax_update'));
    }

    public function display_order_tracking_form() {
        $show_tracking = get_option('superwp_show_tracking_number', 1);
        $show_phone = get_option('superwp_show_phone_number', 1);
        $show_email = get_option('superwp_show_email', 1);

        $tracking_label = esc_html(get_option('superwp_tracking_label', 'Order Tracking Number'));
        $tracking_placeholder = esc_attr(get_option('superwp_tracking_placeholder', 'Enter your order number'));
        $phone_label = esc_html(get_option('superwp_phone_label', 'Phone Number'));
        $phone_placeholder = esc_attr(get_option('superwp_phone_placeholder', 'Enter your phone number'));
        $email_label = esc_html(get_option('superwp_email_label', 'Email Address'));
        $email_placeholder = esc_attr(get_option('superwp_email_placeholder', 'Enter your email'));
        $submit_text = esc_attr(get_option('superwp_submit_text', 'Track Order'));

        $whatsapp_button_text = esc_attr(get_option('superwp_whatsapp_button_text', 'Open in WhatsApp'));
        $whatsapp_button_bg_color = esc_attr(get_option('superwp_whatsapp_button_bg_color', '#25D366'));
        $whatsapp_button_text_color = esc_attr(get_option('superwp_whatsapp_button_text_color', '#FFFFFF'));

        ob_start();
        ?>
        <div class="superwp-order-tracker">
            <div id="order-tracker-message"></div>
            <form id="order-tracking-form" method="post" class="superwp-form">
                <?php wp_nonce_field('superwp_track_order_nonce', 'superwp_track_order_nonce'); ?>
                <?php if ($show_tracking) : ?>
                <div class="form-group">
                    <label for="tracking_number"><?php echo $tracking_label; ?></label>
                    <input type="text" id="tracking_number" name="tracking_number" placeholder="<?php echo $tracking_placeholder; ?>" required />
                </div>
                <?php endif; ?>
                <?php if ($show_phone) : ?>
                <div class="form-group">
                    <label for="phone_number"><?php echo $phone_label; ?></label>
                    <input type="tel" id="phone_number" name="phone_number" placeholder="<?php echo $phone_placeholder; ?>" required />
                </div>
                <?php endif; ?>
                <?php if ($show_email) : ?>
                <div class="form-group">
                    <label for="email"><?php echo $email_label; ?></label>
                    <input type="email" id="email" name="email" placeholder="<?php echo $email_placeholder; ?>" />
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <button type="submit" class="superwp-submit"><?php echo $submit_text; ?></button>
                </div>
            </form>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('#order-tracking-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var message = $('#order-tracker-message');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'superwp_track_order',
                        tracking_number: $('#tracking_number').val(),
                        phone_number: $('#phone_number').val(),
                        email: $('#email').val(),
                        superwp_track_order_nonce: $('#superwp_track_order_nonce').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            message.html(
                                '<p class="success">' + response.data.order_status + ': <?php echo esc_js(get_option('superwp_success_message', 'Order found! WhatsApp message sent.')); ?></p>' +
                                '<img src="' + response.data.qr_code + '" alt="WhatsApp QR Code" class="whatsapp-qr-code">' +
                                '<a href="' + response.data.whatsapp_url + '" target="_blank" class="whatsapp-button"><?php echo $whatsapp_button_text; ?></a>'
                            );
                            form[0].reset();
                        } else {
                            message.html('<p class="error">' + response.data + '</p>');
                        }
                    },
                    error: function() {
                        message.html('<p class="error"><?php echo esc_js(get_option('superwp_error_message', 'An error occurred. Please try again.')); ?></p>');
                    }
                });
            });
        });
        </script>
        <style>
        .whatsapp-button {
            display: inline-block;
            background-color: <?php echo $whatsapp_button_bg_color; ?>;
            color: <?php echo $whatsapp_button_text_color; ?>;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .whatsapp-button:hover {
            background-color: <?php echo $this->adjustBrightness($whatsapp_button_bg_color, -20); ?>;
        }
        .whatsapp-qr-code {
            display: block;
            margin: 10px auto;
        }
        </style>
        <?php
        return ob_get_clean();
    }

    public function ajax_track_order() {
        check_ajax_referer('superwp_track_order_nonce', 'superwp_track_order_nonce');

        $tracking_number = isset($_POST['tracking_number']) ? sanitize_text_field($_POST['tracking_number']) : '';
        $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';

        if (empty($tracking_number) || empty($phone_number)) {
            wp_send_json_error('Invalid input. Please provide both tracking number and phone number.');
        }

        $order = wc_get_order($tracking_number);

        if ($order && $order->get_billing_phone() === $phone_number) {
            $status = wc_get_order_status_name($order->get_status());
            $order_date = $order->get_date_created()->date('Y-m-d');
            $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

            $message = get_option('superwp_order_message_template');

            $placeholders = array(
                '{order_number}' => $tracking_number,
                '{order_status}' => $status,
                '{order_date}' => $order_date,
                '{customer_name}' => $customer_name,
            );

            $message = strtr($message, $placeholders);
            $message = do_shortcode($message);

            $admin_phone_number = get_option('superwp_admin_whatsapp_number');
            $whatsapp_url = $this->get_whatsapp_url($admin_phone_number, $message);
            
            $response = array(
                'success' => true,
                'data' => array(
                    'order_status' => $status,
                    'whatsapp_url' => $whatsapp_url,
                    'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($whatsapp_url)
                )
            );

            wp_send_json($response);
        } else {
            wp_send_json_error('Invalid tracking number or phone number.');
        }
    }

    public function enqueue_styles() {
        wp_enqueue_style('superwp-woo-whatsapp-tracker', plugins_url('superwp-woo-whatsapp-tracker.css', __FILE__));

        $custom_css = "
            .superwp-order-tracker {
                max-width: 500px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .superwp-form .form-group {
                margin-bottom: 20px;
            }
            .superwp-form label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
                color: " . esc_attr(get_option('superwp_form_color', '#333')) . ";
            }
            .superwp-form input[type='text'],
            .superwp-form input[type='tel'],
            .superwp-form input[type='email'] {
                width: 100%;
                padding: 10px;
                border: 1px solid " . esc_attr(get_option('superwp_form_border', '#ddd')) . ";
                border-radius: 4px;
                font-size: 16px;
            }
            .superwp-form .superwp-submit {
                background-color: " . esc_attr(get_option('superwp_submit_bg_color', '#4CAF50')) . ";
                color: " . esc_attr(get_option('superwp_submit_text_color', '#ffffff')) . ";
                padding: 12px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .superwp-form .superwp-submit:hover {
                background-color: " . esc_attr(get_option('superwp_submit_hover_bg_color', '#45a049')) . ";
            }
            #order-tracker-message {
                margin-bottom: 20px;
            }
            #order-tracker-message .success {
                color: " . esc_attr(get_option('superwp_success_color', '#4CAF50')) . ";
                background-color: #e8f5e9;
                padding: 10px;
                border-radius: 4px;
            }
            #order-tracker-message .error {
                color: " . esc_attr(get_option('superwp_error_color', '#f44336')) . ";
                background-color: #ffebee;
                padding: 10px;
                border-radius: 4px;
            }
        ";
        wp_add_inline_style('superwp-woo-whatsapp-tracker', $custom_css);
    }

    private function get_whatsapp_url($phone_number, $message) {
        return 'https://api.whatsapp.com/send?phone=' . urlencode($phone_number) . '&text=' . urlencode($message);
    }

    public function superwp_add_admin_menu() {
        add_menu_page(
            'WhatsApp Order Tracker Settings',
            'WhatsApp Tracker',
            'manage_options',
            'superwp_whatsapp_tracker_settings',
            array($this, 'superwp_tracker_settings_page'),
            'dashicons-whatsapp'
        );
    }

    public function superwp_register_settings() {
        register_setting('superwp_whatsapp_tracker_group', 'superwp_admin_whatsapp_number', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_order_message_template', 'wp_kses_post');

        register_setting('superwp_whatsapp_tracker_group', 'superwp_tracking_label', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_tracking_placeholder', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_phone_label', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_phone_placeholder', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_submit_text', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_success_message', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_error_message', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_success_color', 'sanitize_hex_color');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_error_color', 'sanitize_hex_color');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_whatsapp_button_text', 'sanitize_text_field');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_whatsapp_button_bg_color', 'sanitize_hex_color');
        register_setting('superwp_whatsapp_tracker_group', 'superwp_whatsapp_button_text_color', 'sanitize_hex_color');
    }

    public function superwp_tracker_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Check if settings were updated
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            add_settings_error('superwp_messages', 'superwp_message', __('Settings Saved', 'superwp-woo-whatsapp-order-tracker'), 'updated');
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php settings_errors('superwp_messages'); ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('superwp_whatsapp_tracker_group');
                do_settings_sections('superwp_whatsapp_tracker_group');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Admin WhatsApp Number</th>
                        <td>
                            <input type="text" name="superwp_admin_whatsapp_number" value="<?php echo esc_attr(get_option('superwp_admin_whatsapp_number')); ?>" placeholder="e.g., +1234567890" />
                            <p class="description">Enter the WhatsApp number for admin notifications.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Order Message Template</th>
                        <td>
                            <textarea name="superwp_order_message_template" rows="5" style="width: 50%;"><?php echo esc_textarea(get_option('superwp_order_message_template')); ?></textarea>
                            <p class="description">Use {order_number}, {order_status}, {order_date}, {customer_name} placeholders for dynamic content.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Form Field Labels</th>
                        <td>
                            <input type="text" name="superwp_tracking_label" value="<?php echo esc_attr(get_option('superwp_tracking_label', 'Order Tracking Number:')); ?>" placeholder="e.g., Order Tracking Number:" />
                            <p class="description">Tracking number label</p>
                            <input type="text" name="superwp_tracking_placeholder" value="<?php echo esc_attr(get_option('superwp_tracking_placeholder', 'Enter your order number')); ?>" placeholder="e.g., Enter your order number" />
                            <p class="description">Tracking number placeholder</p>
                            <input type="text" name="superwp_phone_label" value="<?php echo esc_attr(get_option('superwp_phone_label', 'Phone Number:')); ?>" placeholder="e.g., Phone Number:" />
                            <p class="description">Phone number label</p>
                            <input type="text" name="superwp_phone_placeholder" value="<?php echo esc_attr(get_option('superwp_phone_placeholder', 'Enter your phone number')); ?>" placeholder="e.g., Enter your phone number" />
                            <p class="description">Phone number placeholder</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Submit Button Text</th>
                        <td>
                            <input type="text" name="superwp_submit_text" value="<?php echo esc_attr(get_option('superwp_submit_text', 'Track Order')); ?>" placeholder="e.g., Track Order" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Success & Error Messages</th>
                        <td>
                            <input type="text" name="superwp_success_message" value="<?php echo esc_attr(get_option('superwp_success_message', 'Order found! WhatsApp message sent.')); ?>" placeholder="e.g., Order found! WhatsApp message sent." />
                            <p class="description">Success message</p>
                            <input type="text" name="superwp_error_message" value="<?php echo esc_attr(get_option('superwp_error_message', 'Invalid tracking number or phone number.')); ?>" placeholder="e.g., Invalid tracking number or phone number." />
                            <p class="description">Error message</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Form & Button Colors</th>
                        <td>
                            <input type="text" name="superwp_form_border" class="color-field" value="<?php echo esc_attr(get_option('superwp_form_border', '1px solid #ddd')); ?>" data-default-color="#ddd" />
                            <p class="description">Form border color</p>
                            <input type="text" name="superwp_form_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_form_color', '#333')); ?>" data-default-color="#333" />
                            <p class="description">Form text color</p>
                            <input type="text" name="superwp_submit_bg_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_submit_bg_color', '#4CAF50')); ?>" data-default-color="#4CAF50" />
                            <p class="description">Submit button background color</p>
                            <input type="text" name="superwp_submit_text_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_submit_text_color', '#ffffff')); ?>" data-default-color="#ffffff" />
                            <p class="description">Submit button text color</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Success & Error Colors</th>
                        <td>
                            <input type="text" name="superwp_success_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_success_color', '#4CAF50')); ?>" data-default-color="#4CAF50" />
                            <p class="description">Success message color</p>
                            <input type="text" name="superwp_error_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_error_color', '#f44336')); ?>" data-default-color="#f44336" />
                            <p class="description">Error message color</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">WhatsApp Button Settings</th>
                        <td>
                            <input type="text" name="superwp_whatsapp_button_text" value="<?php echo esc_attr(get_option('superwp_whatsapp_button_text', 'Open in WhatsApp')); ?>" />
                            <p class="description">WhatsApp button text</p>
                            <input type="text" name="superwp_whatsapp_button_bg_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_whatsapp_button_bg_color', '#25D366')); ?>" data-default-color="#25D366" />
                            <p class="description">WhatsApp button background color</p>
                            <input type="text" name="superwp_whatsapp_button_text_color" class="color-field" value="<?php echo esc_attr(get_option('superwp_whatsapp_button_text_color', '#FFFFFF')); ?>" data-default-color="#FFFFFF" />
                            <p class="description">WhatsApp button text color</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function order_total_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts, 'order_total');

        $order_id = absint($atts['id']);
        if (!$order_id) {
            return '';
        }

        $order = wc_get_order($order_id);
        if ($order) {
            return wc_price($order->get_total());
        }
        return '';
    }

    // Add this helper function to your class
    private function adjustBrightness($hex, $steps) {
        // Convert hex to rgb
        $rgb = array_map('hexdec', str_split(ltrim($hex, '#'), 2));
        
        // Adjust brightness
        foreach ($rgb as &$color) {
            $color = max(0, min(255, $color + $steps));
        }
        
        // Convert rgb back to hex
        return '#' . implode('', array_map(function($n) {
            return str_pad(dechex($n), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }

    public function handle_ajax_update() {
        check_ajax_referer('superwp_ajax_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        $setting_value = sanitize_text_field($_POST['setting_value']);
        update_option('superwp_ajax_setting', $setting_value);
        wp_send_json_success();
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_superwp_whatsapp_tracker_settings' !== $hook) {
            return;
        }
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_add_inline_script('wp-color-picker', '
            jQuery(document).ready(function($) {
                $(".color-field").wpColorPicker();
            });
        ');
    }
}

endif; // End if class_exists check.