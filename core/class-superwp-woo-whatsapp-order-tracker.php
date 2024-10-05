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
    }

    public function display_order_tracking_form() {
        $tracking_label = esc_html(get_option('superwp_tracking_label', 'Order Tracking Number:'));
        $tracking_placeholder = esc_attr(get_option('superwp_tracking_placeholder', 'Enter your order number'));
        $phone_label = esc_html(get_option('superwp_phone_label', 'Phone Number:'));
        $phone_placeholder = esc_attr(get_option('superwp_phone_placeholder', 'Enter your phone number'));
        $submit_text = esc_attr(get_option('superwp_submit_text', 'Track Order'));

        ob_start();
        ?>
        <div id="order-tracker-message"></div>
        <form id="order-tracking-form" method="post">
            <?php wp_nonce_field('superwp_track_order_nonce', 'superwp_track_order_nonce'); ?>
            <label for="tracking_number"><?php echo $tracking_label; ?></label>
            <input type="text" id="tracking_number" name="tracking_number" placeholder="<?php echo $tracking_placeholder; ?>" required />
            <label for="phone_number"><?php echo $phone_label; ?></label>
            <input type="text" id="phone_number" name="phone_number" placeholder="<?php echo $phone_placeholder; ?>" required />
            <input type="submit" value="<?php echo $submit_text; ?>" />
        </form>
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
                        superwp_track_order_nonce: $('#superwp_track_order_nonce').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            window.open(response.data.whatsapp_url, '_blank');
                            form[0].reset();
                            message.html('<p class="success"><?php echo esc_js(get_option('superwp_success_message', 'Order found! WhatsApp message sent.')); ?></p>');
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
        <?php
        return ob_get_clean();
    }

    public function enqueue_styles() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('superwp-woo-whatsapp-tracker', plugins_url('superwp-woo-whatsapp-tracker.css', __FILE__));

        $custom_css = "
            #order-tracking-form input[type='text'],
            #order-tracking-form input[type='submit'] {
                border: " . esc_attr(get_option('superwp_form_border', '1px solid #ddd')) . ";
                color: " . esc_attr(get_option('superwp_form_color', '#333')) . ";
            }
            #order-tracking-form input[type='submit'] {
                background-color: " . esc_attr(get_option('superwp_submit_bg_color', '#4CAF50')) . ";
                color: " . esc_attr(get_option('superwp_submit_text_color', '#ffffff')) . ";
            }
            #order-tracker-message .success {
                color: " . esc_attr(get_option('superwp_success_color', '#4CAF50')) . ";
            }
            #order-tracker-message .error {
                color: " . esc_attr(get_option('superwp_error_color', '#f44336')) . ";
            }
        ";
        wp_add_inline_style('superwp-woo-whatsapp-tracker', $custom_css);
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
            $status = $order->get_status();
            $order_date = $order->get_date_created()->date('Y-m-d');
            $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

            $message = get_option('superwp_order_message_template');

            $placeholders = array(
                '{order_number}' => $tracking_number,
                '{order_status}' => ucfirst($status),
                '{order_date}' => $order_date,
                '{customer_name}' => $customer_name,
            );

            $message = strtr($message, $placeholders);
            $message = do_shortcode($message);

            $admin_phone_number = get_option('superwp_admin_whatsapp_number');
            $whatsapp_url = $this->get_whatsapp_url($admin_phone_number, $message);
            wp_send_json_success(array('whatsapp_url' => $whatsapp_url));
        } else {
            wp_send_json_error('Invalid tracking number or phone number.');
        }
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
    }

    public function superwp_tracker_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Check if form is submitted
        if (isset($_POST['submit'])) {
            check_admin_referer('superwp_whatsapp_tracker_options');
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('superwp_whatsapp_tracker_group');
                do_settings_sections('superwp_whatsapp_tracker_group');
                wp_nonce_field('superwp_whatsapp_tracker_options');
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
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.color-field').wpColorPicker();
        });
        </script>
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
}

endif; // End if class_exists check.

// Initialize the plugin
function run_superwp_woo_whatsapp_order_tracker() {
    return Superwp_Woo_Whatsapp_Order_Tracker::instance();
}

// Run the plugin
run_superwp_woo_whatsapp_order_tracker();