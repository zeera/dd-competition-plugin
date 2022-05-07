<?php
/**
 * Admin pages loader class file.
 */
declare(strict_types=1);

namespace WpDigitalDriveCompetitions\AdminPages;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;

/**
 * Admin pages loader.
 */
class Loader
{
    /**
     * Initialize the admin pages hook
     */
    public static function init()
    {
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/featured-competitions.css'));
        wp_register_style('shortcode-table-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/shortcode-table.css?v=' . $version);

        add_action('admin_menu', [self::class, 'initAdminPages']);
    }
    /**
     * Add the admin pages
     */
    public static function initAdminPages()
    {
        $adminHelper = new AdminHelper;
        if( $adminHelper->isAdmin() ) {
            // Menu page
            add_menu_page(__("Competition"), __("Competition"), "administrator", WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, '', WPDIGITALDRIVE_COMPETITIONS_URL . "assets/images/logo.svg", 85);

            // Sub menu pages (hooks for later if needed??)
            // add_submenu_page(WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, __("Dashboard"), __("Dashboard"), "administrator", WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, [self::class, "menuPageDashboard"]);

            // add_submenu_page(WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, __("Price Match Settings"), __("Price Match Settings"), "administrator", WPDIGITALDRIVE_COMPETITIONS_NAMESPACE . "_price-matching-modal-settings", [self::class, "menuPriceMatchingModal"]);

            add_submenu_page(WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, __("Shortcodes"), __("Shortcodes"), "administrator", WPDIGITALDRIVE_COMPETITIONS_NAMESPACE . "_shortcode_list", [self::class, "menuShortcodes"]);

            add_submenu_page(WPDIGITALDRIVE_COMPETITIONS_NAMESPACE, __("Entry Lists"), __("Entry Lists"), "administrator", WPDIGITALDRIVE_COMPETITIONS_NAMESPACE . "_entry_lists", [self::class, "menuEntryList"]);
        }

    }

    /**
     * Menu page Dashboard
     */
    public static function menuPageDashboard()
    {
        if (isset($_GET['id']) || isset($_GET['add'])) {
            new \WpDigitalDriveCompetitions\AdminPages\Dashboard\Controller('view');
        } else {
            new \WpDigitalDriveCompetitions\AdminPages\Dashboard\Controller('index');
        }
    }


    /**
     * Price Matching
     */
    public static function menuPriceMatchingModal()
    {
        new \WpDigitalDriveCompetitions\AdminPages\MatchPricingModal\Controller();
    }

    /**
     * Shortcodes
     */
    public static function menuShortcodes()
    {
        if(!wp_style_is('shortcode-table-style')) {
            wp_enqueue_style("shortcode-table-style");
        }
        new \WpDigitalDriveCompetitions\AdminPages\Shortcodes\Controller('index');
    }

    /**
     * Entry Lists
     */
    public static function menuEntryList()
    {
        if (isset($_GET['product_id']) && isset($_GET['page'])) {
            new \WpDigitalDriveCompetitions\AdminPages\EntryLists\Controller('view');
        } else {
            wp_redirect( admin_url( '/edit.php?post_type=product' ) );
            exit;
        }
    }
}
