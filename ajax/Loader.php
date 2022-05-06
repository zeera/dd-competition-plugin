<?php

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Ajax;


/**
 * AJAX Loader.
 */
class Loader
{
    /**
     * Initialize all the ajax hooks
     */
    public static function init()
    {
        //Remember to check user permission levels for non priv users, as this may entail registered users not only admins
        $loggedinaction = 'wp_ajax_' . WPDIGITALDRIVE_COMPETITIONS_AJAX_PREFIX;
        $noprivaction = 'wp_ajax_nopriv_' . WPDIGITALDRIVE_COMPETITIONS_AJAX_PREFIX;

        //Calling the function for logged in users, remember to check logged in users roles for secure calls
        add_action($loggedinaction . 'adminAjax', [self::class, 'adminAjax']);

        //Calling the function for guests
        // add_action($noprivaction . 'test-ajax', [self::class, 'testAjax']);
    }
    public static function adminAjax()
    {
        if (\current_user_can('edit_posts')) { //Making certain they have appropriate permissions
            $class = new \WpDigitalDriveCompetitions\Ajax\AdminCalls();
            if ($_GET['_report_type'] == 'woo-attributes') {
                $class->report();
            } else {
                $class->priceMatchingAjax();
            }
        }
    }
}
