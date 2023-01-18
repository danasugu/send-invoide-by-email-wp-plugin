<?php
/**
 * Plugin Name: Send Invoice by Email
 * Plugin URI: https://danasugu.com/eSdV_Invoice
 * Description: A personalized and itemized invoice plugin for WordPress
 * Version: 1.0
 * Author: Dana Sugu
 * Author URI: https://danasugu.com/
 * License: GPL2
 */

function eSdV_generate_invoice($order_id) {
    // Get order data
    $order = wc_get_order( $order_id );

    // Prepare invoice data
    $invoice_data = array();
    $invoice_data['customer_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $invoice_data['order_number'] = $order->get_order_number();
    $invoice_data['order_date'] = $order->get_date_created()->date('Y-m-d');
    $invoice_data['billing_address'] = $order->get_formatted_billing_address();
    $invoice_data['shipping_address'] = $order->get_formatted_shipping_address();
    $invoice_data['items'] = array();

    // Get order items
    $order_items = $order->get_items();
    foreach ( $order_items as $item_id => $item ) {
        $product = $item->get_product();
        $invoice_data['items'][] = array(
            'name' => $product->get_name(),
            'quantity' => $item->get_quantity(),
            'price' => wc_price($product->get_price()),
        );
    }

    // Generate invoice HTML
    $invoice_html = '<div class="invoice-container">';
    $invoice_html .= '<h1>Invoice</h1>';
    $invoice_html .= '<p>Customer: ' . $invoice_data['customer_name'] . '</p>';
    $invoice_html .= '<p>Order Number: ' . $invoice_data['order_number'] . '</p>';
    $invoice_html .= '<p>Order Date: ' . $invoice_data['order_date'] . '</p>';
    $invoice_html .= '<h2>Billing Address</h2>';
    $invoice_html .= '<p>' . $invoice_data['billing_address'] . '</p>';
    $invoice_html .= '<h2>Shipping Address</h2>';
    $invoice_html .= '<p>' . $invoice_data['shipping_address'] . '</p>';
    $invoice_html .= '<h2>Items</h2>';
    $invoice_html .= '<table>';
    $invoice_html .= '<tr>';
    $invoice_html .= '<th>Name</th>';
    $invoice_html .= '<th>Quantity</th>';
    $invoice_html .= '<th>Price</th>';
    $invoice_html .= '</tr>';
    foreach ( $invoice_data['items'] as $item ) {
    $invoice_html .= '<tr>';
      $invoice_html .= '<td>' . $item['name'] . '</td>';
      $invoice_html .= '<td>' . $item['quantity'] . '</td>';
      $invoice_html .= '<td>' . $item['price'] . '</td>';
      $invoice_html .= '</tr>';
    }
    $invoice_html .= '</table>';
    $invoice_html .= '</div>';

// Send invoice email
$to = $order->get_billing_email();
$subject = 'Invoice for Order #' . $invoice_data['order_number'];
$headers[] = 'Content-Type: text/html; charset=UTF-8';
$headers[] = 'From: My Website <noreply@example.com>';
  wp_mail( $to, $subject, $invoice_html, $headers );
}
  add_action( 'woocommerce_thankyou', 'eSdV_generate_invoice', 10, 1 );
