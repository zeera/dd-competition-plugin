<?php

/**
 * Admin Helper
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Helpers;

use WpDigitalDriveCompetitions\Core\Controller;

/**
 * Admin Helper
 */
class AdminHelper extends Controller
{
    public $load_view_file = true;
    /** Page header */
    public ?string $header_file = 'header.php';
    /** Page footer */
    public ?string $footer_file = 'footer.php';
    /** Page menu */
    public ?string $menu_file = null;

    public string $base_url = WPDIGITALDRIVE_COMPETITIONS_URL;

    public function dd($var, $pretty = false, $die = false)
    {
        $backtrace = debug_backtrace();
        echo "\n<pre>\n";
        if (isset($backtrace[0]['file'])) {
            echo $backtrace[0]['file'] . "\n\n";
        }
        echo "Type: " . gettype($var) . "\n";
        echo "Time: " . date('c') . "\n";
        echo "---------------------------------\n\n";
        ($pretty) ? print_r($var) : var_dump($var);
        echo "</pre>\n";
        if ($die) {
            die();
        }
    }

    public function isAdmin()
    {
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        } else {
            return false;
        }
    }

    public function isLoggedIn()
    {
        $user = is_user_logged_in();
        return $user;
    }
}
