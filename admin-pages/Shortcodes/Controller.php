<?php
/**
 * Controller for the shortcode dashboard
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\AdminPages\Shortcodes;
use WpDigitalDriveCompetitions\Helpers\AdminHelper;
use WpDigitalDriveCompetitions\Helpers\ShortcodeProvider;
/**
 * Controller for the shortcode dashboard
 */
class Controller extends AdminHelper
{
    protected $shortcodes;

    public function actionIndex() {
        $shortcode_provider = new ShortcodeProvider;
        $this->shortcodes = $shortcode_provider->get_shortcodes();

        $this->buildPage(dirname(__FILE__) . '/index.php');
    }
}
