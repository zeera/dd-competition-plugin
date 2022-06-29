<?php
/**
 * =====================================
 * Competition Scripts
 * =====================================
 * File Description
 * =====================================
 */

declare(strict_types=1);

namespace WpDigitalDriveCompetitions\Hooks\CompetitionsBackend;

use WpDigitalDriveCompetitions\Helpers\AdminHelper;

class CompetitionSettings extends AdminHelper
{
    public static function enqueueColorPicker($hook_suffix)
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    public static function enqueueStylesAndScripts()
    {
        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/css/datetimepicker.css'));
        wp_register_style('datetimepicker-style', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/css/datetimepicker.css?v=' . $version);
        wp_enqueue_style("datetimepicker-style");

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/competition.js'));
        wp_register_script('competition-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/competition.js?v=' . $version, array('jquery'), '', true);
        wp_enqueue_script("competition-scripts");

        $version = date("ymd-Gis", filemtime(WPDIGITALDRIVE_COMPETITIONS_PATH . 'assets/js/datetimepicker.js'));
        wp_register_script('datetimepicker-scripts', WPDIGITALDRIVE_COMPETITIONS_URL . 'assets/js/datetimepicker.js?v=' . $version, array('jquery'), '', true);
        wp_enqueue_script("datetimepicker-scripts");
    }

    public static function registerField()
    {
        register_setting('digital_drive_competition_settings', 'maximum_ticket_default_value');
        register_setting('digital_drive_competition_settings', 'default_basket_quantity');
        register_setting('digital_drive_competition_settings', 'maximum_ticket_default_per_user');
        register_setting('digital_drive_competition_settings', 'answerBgColor');
        register_setting('digital_drive_competition_settings', 'selectedAnswerBgColor');
        register_setting('digital_drive_competition_settings', 'textColor');
        register_setting('digital_drive_competition_settings', 'data_per_page');
        register_setting('digital_drive_competition_settings', 'data_per_page_options');
        register_setting('digital_drive_competition_settings', 'top_info_background_color_one');
        register_setting('digital_drive_competition_settings', 'top_info_background_color_two');
        register_setting('digital_drive_competition_settings', 'top_info_text_color');
        register_setting('digital_drive_competition_settings', 'top_info_heding_color');
    }
}
