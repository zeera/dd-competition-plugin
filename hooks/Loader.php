<?php

/**
 * Hooks loader class file.
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks;

//FRONTEND CLASS
use WpDigitalDriveCompetitions\Hooks\CompetitionsFrontend\CompetitionTicketNumber;

use WpDigitalDriveCompetitions\Hooks\CompetitionsFrontend\CompetitionProcess;

//BACKEND CLASS
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\CompetitionsBackendProcess;
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\WooCommerceMetaBox;
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\ActivationDeactivationHook;
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\CompetitionSettings;
use WpDigitalDriveCompetitions\Hooks\CompetitionsBackend\WooCommerceOrderStatusChangedHook;
use WpDigitalDriveCompetitions\Hooks\WcProductCompetitions;



/**
 * Hooks loader.
 */
class Loader
{
    /**
     * Initialize the hooks
     */
    public static function init()
    {
        // Activation Hook
        register_activation_hook(WPDIGITALDRIVE_COMPETITIONS_FILE, [ActivationDeactivationHook::class, 'activate']);
        // Deactivation Hooks
        register_deactivation_hook(WPDIGITALDRIVE_COMPETITIONS_FILE, [ActivationDeactivationHook::class, 'deactivate']);

        // On WooCommerce order change
        // add_action('woocommerce_order_status_changed', [WooCommerceOrderStatusChangedHook::class, 'onOrderStatusChanged'], 13, 3);

        /** Admin Styles and Scripts
         * ===================================== */
        add_action('admin_init', [CompetitionSettings::class, 'registerField']);
        add_action( 'admin_enqueue_scripts', [CompetitionSettings::class, 'enqueueColorPicker'] );
        add_action( 'admin_enqueue_scripts', [CompetitionSettings::class, 'enqueueStylesAndScripts'] );
        register_activation_hook( __FILE__, [ WooCommerceMetaBox::class, 'installTaxonomy'] );
        // remove_action( 'woocommerce_after_add_to_cart_button', [CompetitionProcess::class, 'displayNotice'] );

        /** Admin
         * ===================================== */
        add_filter('manage_edit-product_columns', [ CompetitionsBackendProcess::class, 'showProductOrder'], 10, 2);
        add_filter('manage_product_posts_custom_column', [ CompetitionsBackendProcess::class, 'addCustomButton'], 10, 2);

        /** Woocmmerce Hooks that requires Woo Classes
         * ===================================== */
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action('woocommerce_loaded', [ WooCommerceMetaBox::class, 'loadPlugin']);
            add_action('product_type_selector', [ WooCommerceMetaBox::class, 'add_competition_product_type']);
            add_action('woocommerce_product_data_tabs', [ WooCommerceMetaBox::class, 'competitionTab'], 10, 1);
            add_action('woocommerce_product_data_panels', [ WooCommerceMetaBox::class, 'competionProductData']);
            add_action('woocommerce_process_product_meta', [ WooCommerceMetaBox::class, 'competitionProductDataStore']);
            add_action('admin_footer', [ WooCommerceMetaBox::class, 'enable_js_on_wc_product']);
            add_action('woocommerce_before_add_to_cart_quantity', [ CompetitionProcess::class, 'drawDateTimeShow']);
            // add_action('woocommerce_after_add_to_cart_form', [ CompetitionProcess::class, 'countdownTimer']);
            add_action('wp_enqueue_scripts', [CompetitionProcess::class, 'cartScripts']);

            //CART HOOKS
            add_action("woocommerce_competition_add_to_cart", function () {
                do_action('woocommerce_simple_add_to_cart');
            });
            add_filter( 'woocommerce_add_to_cart_validation',  [ CompetitionProcess::class, 'validateAnswer'], 10, 5 );
            add_action('woocommerce_add_cart_item_data', [ CompetitionProcess::class, 'addCartItemData'], 10, 3);
            add_action('woocommerce_get_cart_item_from_session', [ CompetitionProcess::class, 'getCartItemFromSession'], 10, 3);
            add_action('woocommerce_get_item_data', [ CompetitionProcess::class, 'getItemData'], 10, 2);
            add_action('woocommerce_add_order_item_meta', [ CompetitionProcess::class, 'addOrderItemMeta'], 10, 3);
            add_action( 'woocommerce_update_cart_action_cart_updated', [ CompetitionProcess::class, 'onCartUpdate'], 20, 1 );

            //CHECKOUT HOOKS
            add_action('woocommerce_order_status_processing', [ CompetitionTicketNumber::class, 'create']);
            add_action('woocommerce_order_status_completed', [ CompetitionTicketNumber::class, 'create']);
            add_action('woocommerce_before_order_itemmeta', [ CompetitionTicketNumber::class, 'addTicketNumberToOders'], 10, 3);
            add_filter('woocommerce_order_item_display_meta_key', [ CompetitionProcess::class, 'filterWcOrderItemDisplayMetaKey'], 20, 3 );
            add_filter('woocommerce_after_checkout_validation', [ CompetitionProcess::class, 'guestValidation'], 20, 2 );
            //add_filter('woocommerce_checkout_fields', [ CompetitionProcess::class, 'setBillingFieldsReadOnly']);
            // add_filter('woocommerce_default_address_fields', 'override_default_address_checkout_fields', 20, 1);
            // add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
        }
    }
}
