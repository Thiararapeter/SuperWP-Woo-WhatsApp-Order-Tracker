# SuperWP Woo WhatsApp Order Tracker

The **SuperWP Woo WhatsApp Order Tracker** plugin offers WooCommerce store owners the ability to provide an easy and efficient order tracking experience via WhatsApp. Customers can simply input their order number and phone number to get instant updates about their order status directly through WhatsApp messages.

## Key Features

- **Order Tracking via WhatsApp**: Customers can track their WooCommerce orders by entering their order number and WhatsApp phone number.
- **Customizable WhatsApp Messages**: Create and configure personalized WhatsApp messages to notify customers of their order status (e.g., pending, processing, shipped, completed).
- **Frontend Integration via Shortcode**: Easily embed the order tracking form anywhere on your site using a simple shortcode `[superwp_order_tracker]`.
- **Admin WhatsApp Settings**: Configure the WhatsApp number from which the order tracking updates are sent.
- **Color Sliders for Customization**: Customize the colors of your form fields and buttons using easy-to-use color sliders.
- **Custom Placeholders**: Define placeholder text for the form fields (order number and phone number) to guide customers through the tracking process.
- **WooCommerce Compatibility**: Seamlessly integrates with WooCommerce to fetch order details and automatically send updates on status changes.

## Installation

### Automatic Installation via WordPress Admin

1. Log in to your WordPress admin panel.
2. Go to `Plugins` > `Add New`.
3. Search for **SuperWP Woo WhatsApp Order Tracker**.
4. Click `Install Now` and then `Activate`.

### Manual Installation

1. Download the plugin from [GitHub](https://github.com/Thiararapeter/SuperWP-Woo-WhatsApp-Order-Tracker).
2. Upload the plugin files to the `/wp-content/plugins/superwp-woo-whatsapp-order-tracker` directory.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Navigate to `WooCommerce` > `Settings` > `SuperWP Woo WhatsApp Order Tracker` to configure the plugin.

## Configuration

Once activated, you can configure the plugin in the WooCommerce settings panel under the `SuperWP Woo WhatsApp Order Tracker` section.

### Settings Overview:
1. **Admin WhatsApp Number**: Set the WhatsApp number that will send order updates to customers.
2. **Custom Message Settings**: Personalize the WhatsApp messages for different order statuses (e.g., pending, processing, shipped, completed).
3. **Form Customization**: Configure placeholders and label texts for the order number and phone number fields.
4. **Color Sliders**: Customize the colors of the form’s background, input fields, and buttons to match your store's design.

## Shortcodes

Use the following shortcode to display the order tracking form on any page or post:

[superwp_woo_order_tracker]

This will generate a form with two fields:

- **Order Number**: Customers enter their WooCommerce order number.
- **Phone Number**: Customers provide their WhatsApp phone number.

After form submission, they will receive real-time WhatsApp updates regarding their order status.

### Available Placeholders in Custom Messages
You can use the following placeholders to include dynamic content in your WhatsApp messages:
- {order_number}: The WooCommerce order number.
- {order_status}: The current status of the customer's order.
- {customer_name}: The customer's full name.
- {order_tracking_link}: A URL link for tracking the order on your website.
- {test}: A placeholder for testing purposes.

### Example Message Template:
Hello {{customer_name}}, your order {{order_number}} is currently {{order_status}}. You can track your order status here: {{order_tracking_link}}.

## Frequently Asked Questions (FAQ)

### 1. How do I set up WhatsApp messages for different order statuses?
You can define custom messages for different order statuses in the WooCommerce settings under the **SuperWP Woo WhatsApp Order Tracker** section.

### 2. Can customers track their orders from any page?
Yes! Simply use the [superwp_order_tracker] shortcode on any page or post to display the order tracking form.

### 3. Does this plugin support international phone numbers?
Yes, the plugin supports international numbers. Customers need to include their country code when entering their WhatsApp phone number.

### 4. How does the plugin send WhatsApp messages?
The plugin uses the WhatsApp number set by the admin to send order updates. You can configure this number in the settings.

## Changelog

### Version 1.0.04 (October 15, 2024)
* Fixed bug in order tracking functionality
* Improved UI of the order tracking form
* Added customizable form field labels and placeholders
* Introduced QR code generation for WhatsApp message scanning
* Enhanced WhatsApp button customization options
* Added color settings for form elements and messages
* Implemented AJAX-based settings updates for improved admin experience
* New features from core/class-superwp-woo-whatsapp-order-tracker.php:
  - Added shortcode support for displaying order total
  - Introduced customizable success and error messages
  - Added option to customize admin WhatsApp number for notifications
  - Implemented dynamic message templates with placeholders

### Version 1.0.03 (October 10, 2024)
* Added support for multiple languages
* Improved compatibility with the latest version of WooCommerce
* Enhanced security measures for form submissions

### Version 1.0.02 (October 5, 2024)
* Birthday of SuperWP Woo WhatsApp Order Tracker
