<?php

/**
 * WooCommerce order status changed hook class file.
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks;

use WC_Order;
/**
 * Activation hook.
 */
class WooCommerceOrderStatusChangedHook
{
    /**
     * Hook called when the WooCommerce order status has changed
     *
     * When an order is set to processing, add it to the processing queue
     */
    public static function onOrderStatusChanged($orderId, $oldStatus, $newStatus)
    {
        error_log('onOrderStatusChanged');
        // Only trigger if the order is set to processing (just after purchase)
        // if ($newStatus !== 'processing') {
        //     return;
        // }

        // Get the order, this is a woocommerce call.
        $order = new WC_Order($orderId);

        // Build a struct of all the order information
        $orderInfo = [];
        $orderInfo['woo_id'] = $order->get_id();
        $orderInfo['wp_user_id'] = $order->get_customer_id();
        $orderInfo['billing_first_name'] = $order->get_billing_first_name();
        $orderInfo['billing_last_name'] = $order->get_billing_last_name();
        $orderInfo['billing_email'] = $order->get_billing_email();
        $orderInfo['billing_phone'] = $order->get_billing_phone();
        $orderInfo['billing_mobile'] = $order->get_billing_phone();
        $orderInfo['billing_company'] = $order->get_billing_company();
        $orderInfo['billing_address_1'] = $order->get_billing_address_1();
        $orderInfo['billing_address_2'] = $order->get_billing_address_2();
        $orderInfo['billing_city'] = $order->get_billing_city();
        $orderInfo['billing_state'] = $order->get_billing_state();
        $orderInfo['billing_postcode'] = $order->get_billing_postcode();
        $orderInfo['billing_country'] = $order->get_billing_country();
        $orderInfo['status'] = $order->get_status();
        $orderInfo['total'] = $order->get_total();
        $orderInfo['gift_card_recipient_name'] = $order->get_meta('Gift Card Recipient Name');
        $orderInfo['gift_card_recipient_mobile'] = $order->get_meta('Gift Card Recipient Mobile');
        $orderInfo['gift_card_recipient_email'] = $order->get_meta('Gift Card Recipient Email');

        // Get all the order products and add to an array
        $orderInfo['order_items'] = [];
        foreach ($order->get_items() as $item) {
            $itemData = $item->get_data();
            $orderInfo['order_items'][] = $itemData;
        }

        //Then do something with the data

    }

}
